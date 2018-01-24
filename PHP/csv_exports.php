<?php

/**
 * 导出csv
 *
 * @param  array  $data
 * @param  array  $header_data
 * @param  string $file_name
 * @return
 */
function export_csv($data = [], $header_data = [], $file_name = '') {
	header('Pragma: public');
	header("Pragma: no-cache");
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: pre-check=0, post-check=0, max-age=0');
	header('Content-Transfer-Encoding: binary');
	header('Content-Encoding: none');
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename=' . $file_name);
	header('Cache-Control: max-age=0');
	header("Expires: 0");

	$fp = fopen('php://output', 'a');
	if (!empty($header_data)) {
		foreach ($header_data as $key => $value) {
			$header_data[$key] = iconv('utf-8', 'gbk', $value);
		}
		fputcsv($fp, $header_data);
	}
	$num = 0;
	//每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
	$limit = 100000;
	//逐行取出数据，不浪费内存
	$count = count($data);
	if ($count > 0) {
		for ($i = 0; $i < $count; $i++) {
			$num++;
			//刷新一下输出buffer，防止由于数据过多造成问题
			if ($limit == $num) {
				ob_flush();
				flush();
				$num = 0;
			}
			$row = $data[$i];
			foreach ($row as $key => $value) {
				$row[$key] = iconv('utf-8', 'gbk', $value);
			}
			fputcsv($fp, $row);
		}
	}
	fclose($fp);
	exit();
}