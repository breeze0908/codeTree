<?php


//php代码确保多进程同时写入一个文件成功  


//1、多进程或多线程同时写同一个文件的解决方案如下
function T_write($filename, $string) {  
    $fp = fopen($filename, 'a');  	// 追加方式打开  
    if (flock($fp, LOCK_EX)) {   	// 加写锁：独占锁  
        fputs($fp, $string);   		// 写文件  
        flock($fp, LOCK_UN);   		// 解锁  
    }  
    fclose($fp);  
}

//2、多进程或多线程同时读同一个文件的解决方案如下：
function T_read($filename, $length) {  
    $fp = fopen($filename, 'r');        // 只读方式打开
    if (flock($fp, LOCK_SH)) {          // 加读锁：共享锁 
        $result = fgets($fp, $length);  // 读取文件一行或length字节长度
        flock($fp, LOCK_UN);  //解锁
    }  
    fclose($fp);  
    return $result;  
}


//其它方案
function writeData($filepath, $data) 
{ 
    $fp = fopen($filepath,'a');  
    do{ 
        usleep(100); 
    }while (!flock($fp, LOCK_EX)); // 加写锁：独占锁  
    
    $res = fwrite($fp, $data."\n"); 
    flock($fp, LOCK_UN); 
    fclose($fp);  
    return $res; 
} 



function writeData($path, $mode,$data,$max_retries = 10)  
{  
    $fp = fopen($path, $mode);   
    $retries = 0;   
    do{  
       if ($retries > 0)   
       {  
            usleep(rand(1, 10000));  
       }  
       $retries += 1;
    }while (!flock($fp, LOCK_EX) and $retries<= $max_retries);   
    //判断是否等于最大重试次数，是则返回false
    if ($retries == $max_retries)   
    {  
       return false;  
    }  
    fwrite($fp, "$data");  
    flock($fp, LOCK_UN);  
    fclose($fp);   
    return true;   
}


function write_file($filename, $content)
{
    $lock = $filename . '.lck';
    $write_length = 0;
    while(true) {
        if( file_exists($lock) ) {
            usleep(100);
        } else {
            touch($lock);
            $write_length = file_put_contents($filename, $content, FILE_APPEND);
            break;
        }
    }
    if( file_exists($lock) ) {
        unlink($lock);
    }
    return $write_length;
}
