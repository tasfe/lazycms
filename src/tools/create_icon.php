<?php
$str = 'abcd';
for($j=0;$j<strlen($str);$j++) {
    for($i=1;$i<=9;$i++) {
        printf('img.%s%d{background-position:-%dpx -%dpx;}'."\n", substr($str,$j,1), $i, $j*20+20, $i*20);
    }
}
