<?php
$car1_brand = "Toyota";
$car1_color = "Red";

function driveProcedural($brand, $color) {
    return "รถยนต์ยี่ห้อ $brand สี $color กำลังวิ่ง... (แบบเก่า)<br>";
}
echo driveProcedural($car1_brand, $car1_color);

echo "<hr>";


// 1. Blueprint (พิมพ์เขียว) เรียกว่า "Class"
class Car {
    // คุณลักษณะ / ข้อมูล (Properties)
    public $brand;
    public $color;

    // พฤติกรรม / ฟังก์ชันทำงาน (Methods)
    public function drive() {
        return "รถยนต์ยี่ห้อ " . $this->brand . " สี " . $this->color . " กำลังวิ่งอย่างปลอดภัย! (แบบ OOP)<br>";
    }
}

// 2. การสร้างวัตถุจริงจากพิมพ์เขียว เรียกว่า "Object" (หรือ Instance)
$myCar = new Car();
$myCar->brand = "Honda";
$myCar->color = "Black";

// เรียกใช้งานพฤติกรรมของวัตถุนั้น
echo $myCar->drive();

// สามารถสร้าง Object เพิ่มกี่ชิ้นก็ได้จาก Class เดียวกัน
$friendCar = new Car();
$friendCar->brand = "Tesla";
$friendCar->color = "White";
echo $friendCar->drive();