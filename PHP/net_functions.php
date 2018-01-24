<?php
/**
 * @desc: 常用的PHP函数-网络处理函数
 * @date: 2015/11/25 18:11
 * @author: Tan <tanda517886160@163.com>
 * @version: 1.0
 */

/*
 *查询IP所在的地址
 *
 * return array（）
 */
function getCity($ip) {
    $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
    $ip  = json_decode(file_get_contents($url));
    if ((string) $ip->code == '1') {
        return false;
    }
    $data = (array) $ip->data;
    return $data;
}

/**
 * 判断是否Ajax请求
 */
function is_ajax_request() {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取客户端IP
 *
 * @return string
 */
function get_ip_address() {
    foreach ([
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'] as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return false;
}
