<?php
class Fruit {
  protected $name;

  public function get_details() {
    echo "Name: " . $this->name . ".";
  }
}

$apple = new Fruit();
$apple->name = "Apple"; // Error: Cannot access protected property
$apple->get_details();
?>