<?php
$total = 0;
$mutix = [];

function dfs_queue($row, $number = 8) {
    global $total;
    if ($row == $number) {
        $total++;
    } else {
        for ($col = 0; $col != $number; $col++) {
            $mutix[$row] = $col;
            if (check_ok($row, $col)) {
                dfs_queue($row + 1);
            }
        }
    }
}

function check_ok($row) {
    global $mutix;
    for ($i = 0; $i < $row; $i++) {
        if ($mutix[$row] == $mutix[$i] || $row - $mutix[$row] = $i - $mutix[$i] || $row + $mutix[$row] == $i + $mutix[$i]) {
            return false;
        }
    }
    return true;
}

dfs_queue(0);
var_dump($total);