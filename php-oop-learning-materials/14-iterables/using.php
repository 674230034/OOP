<?php
function printIterable(iterable $x) {
  foreach($x as $item) {
    echo $item;
  }
}

// Called with an array:
printIterable(["a", "b", "c"]);

// Called with an object:
$iterator = new ArrayIterator(["d", "e", "f"]);
printIterable($iterator);
?>