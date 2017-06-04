<?php
/**
 * @file: screen_shot_test.php
 * @desc: description about this file
 * @date: 2016/2/26 19:50
 * @author: Tan 
 */

define("IMGEMAGIC_CONVERT_CMD", '/usr/bin/convert');
define("IMGEMAGIC_IDENTIFY_CMD", '/usr/bin/identify');
define("FX_SCREEN_SHOT_SRC", 'http://img.auto.fx.XX.com/img/');
define("FX_SCREEN_SHOT_SRC_ANIME", 'http://img.auto.fx.XX.com/img/anime/');

function exec_img_convert($srcPic, $dstPic, $scale="128x96") {
    $srcInfo = exec(IMGEMAGIC_IDENTIFY_CMD . " {$srcPic}");
    if (!preg_match('/([0-9]+)x([0-9]+)/', $srcInfo, $matches)) {
        return false;
    }
    if (!preg_match('/^([0-9]+)x([0-9]+)$/', $scale, $scaleMat)) {
        return false;
    }
    $srcW = intval($matches[1]);
    $srcH = intval($matches[2]);
    $scaleW = intval($scaleMat[1]);
    $scaleH = intval($scaleMat[2]);
    $maxDiv = max_divisor($scaleW, $scaleH);
    if ($srcW < $scaleW / $maxDiv || $srcH < $scaleH / $maxDiv) {	//if the resolution of src is less than the scale,return
        return false;
    }
    $similar_degree = ($srcW * 1.0 / $srcH) - ($scaleW * 1.0 / $scaleH);
    if ($similar_degree < 0.00000001 && $similar_degree > 0) {	// equal proportion
        $srcH_new = $srcH;
        $srcW_new = $srcW;
        $cropH = 0;
        $cropW = 0;
    } elseif ($similar_degree < 0) {	// crop height
        $srcH_new = $srcW * $scaleH / $scaleW;
        $srcW_new = $srcW;
        $cropH = $srcH - $srcH_new;
        $cropW = 0;
    } else {	//crop width
        $srcW_new = $srcH * $scaleW / $scaleH;
        $srcH_new = $srcH;
        $cropW = $srcW - $srcW_new;
        $cropH = 0;
    }
    $srcFormat = "{$srcW_new}x{$srcH_new}+".floor($cropW / 2.0)."+".floor($cropH / 2.0);
    //$convert_cmd = IMGEMAGIC_CONVERT_CMD . " {$srcPic} -crop {$srcFormat } +repage -resize {$scale} $dstPic";
    $convert_cmd = IMGEMAGIC_CONVERT_CMD . " {$srcPic} -crop {$srcFormat } +repage -quality 90 -resize {$scale} $dstPic";
    echo $convert_cmd;
    exec($convert_cmd);
    return true;
}

function max_divisor($a, $b) {
    $n = min($a, $b);
    for($i = $n; $i > 1; $i--) {
        if (is_int($a / $i) && is_int($b / $i)) {
            return $i;
        }
    }
    return 1;
}

$dstPic = './shot_test.jpg';
$img_path = "http://img.auto.fx.xx.com/img/61/ab/61c651038be7dc2087a771aba808de2686e353ab_0420.jpg";

exec_img_convert($img_path,$dstPic,'200x150');