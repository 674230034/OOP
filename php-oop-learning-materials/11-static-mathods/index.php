<?php
class calc {
  // static method
  public static function sum($x, $y) {
    return $x * $y;
  }
}

// Call static method
$res = calc::sum(6, 4);
echo $res;
?>