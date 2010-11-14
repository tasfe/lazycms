<?php
$setting = array(
    // 黑色概率
    0 => 10,
    // 白色概率
    1 => 0.1,
);
// Requires the GD Library
header("Content-type: image/png");
$im = imagecreatetruecolor(512, 512) or die("Cannot Initialize new GD image stream");
$white = imagecolorallocate($im, 255, 255, 255);
$start = microtime(true);
for ($y = 0; $y < 512; $y++) {
    for ($x = 0; $x < 512; $x++) {
        if (random($setting) === 1) {
            imagesetpixel($im, $x, $y, $white);
        }
    }
}
$time = microtime(true) - $start;
header("X-Exec-Time: " . $time);
imagepng($im);
imagedestroy($im);

/**
 * 全概率计算函数
 *
 * @param  $ps  array('a'=>0.5,'b'=>0.2,'c'=>0.4)
 * @return array key
 */
function random($ps){
    $much = 10000;
    $max  = array_sum($ps) * $much;
    $rand = mt_rand(1,$max);
    $base = 0;
    foreach ($ps as $k=>$v) {
        if ($base*$much < $rand && $rand <= ($base+$v)*$much) {
            return $k;
        } else {
            $base = $v;
        }
    }
    return false;
}
