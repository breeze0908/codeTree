<?php

/**
 * 杀掉已存在的进程
 * @return void
 *
 */
function kill_process_if_has_exist() {
	// 得到当前文件名和进程PID
	$cur_pid   = posix_getpid();
	$basic_cmd = basename(__FILE__);
	$cmd       = "ps aux | grep '" . $basic_cmd . "' | grep -v grep | awk '{print $2}' | grep -v $cur_pid | xargs kill -9";
	echo $cmd . "\n";
	system($cmd);
}

/**
 * 进程已经存在，退出执行
 * @return void
 */
function exit_process_if_has_exist() {
	$cur_pid   = posix_getpid();
	$basic_cmd = basename(__FILE__);
	$output    = [];
	exec("ps aux | grep 'php {$basic_cmd}' | grep -v grep | grep -v $cur_pid", $output);
	if (is_array($output) && count($output) > 0) {
		exit();
	}
}

/**
 * 进程已经存在，退出执行
 * @return void
 */
function exit_process_if_has_exist_02($basic_cmd, $sec_cmd) {
	$output    = [];
	$cmd_shell = get_process_grep_shell(1, $basic_cmd, $sec_cmd);

	echo "执行进程检查：{$cmd_shell}" . PHP_EOL;
	exec($cmd_shell, $output);
	if (is_array($output) && count($output) > 0) {
		echo "进程已经存在，退出执行当前进程" . PHP_EOL;
		exit();
	}
}

/**
 * 获取处理进程的shell
 * @param int $type  1-退出， 0-杀死
 * @return string
 */
function get_process_grep_shell($type = 1) {
	$args = func_get_args();
	unset($args[0]);

	foreach ($args as $arg) {
		$grep_arr[] = sprintf("grep %s", $arg);
	}

	$cur_pid        = posix_getpid();
	$basic_grep_str = ($grep_arr) ? implode(" | ", $grep_arr) : "grep " . basename(__FILE__);

	if ($type == 'exit') {
		return "ps aux | {$basic_grep_str} | grep -v grep | grep -v {$cur_pid}";
	} else {
		return "ps aux | {$basic_grep_str} | grep -v grep | awk '{print $2}' | grep -v $cur_pid | xargs kill -9";
	}
}

/**
Method to execute a command in the terminal
Uses :
1. system
2. passthru
3. exec
4. shell_exec
 */
function terminal($command) {
	//system
	if (function_exists('system')) {
		ob_start();
		system($command, $return_var);
		$output = ob_get_contents();
		ob_end_clean();
	} else if (function_exists('passthru')) {
		//passthru
		ob_start();
		passthru($command, $return_var);
		$output = ob_get_contents();
		ob_end_clean();
	} else if (function_exists('exec')) {
		//exec
		exec($command, $output, $return_var);
		$output = implode("", $output);
	} else if (function_exists('shell_exec')) {
		//shell_exec
		$output = shell_exec($command);
	} else {
		$output     = 'Command execution not possible on this system';
		$return_var = 1;
	}
	return ['output' => $output, 'status' => $return_var];
}



/**
 * 计算内存使用
 *
 * @return
 */
function cal_memory_get_usage() {
	$size = memory_get_usage(true);
	$unit = array('b ', 'kb', 'mb', 'gb', 'tb', 'pb');
	$i    = floor(log($size, 1024));
	return sprintf("%02f", @round($size / pow(1024, $i, 2))) . ' ' . $unit[$i];
}