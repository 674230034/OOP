# PHP OOP - การสืบทอดคุณสมบัติ (Inheritance)

**Inheritance** คือแนวคิดที่คุณสามารถสร้างคลาสใหม่โดยสืบทอดคุณสมบัติ (Properties) และเมธอด (Methods) มาจากคลาสที่มีอยู่ก่อนแล้วได้ ช่วยลดการเขียนโค้ดซ้ำซ้อน (Code Reusability)

* **คลาสแม่ (Parent/Base Class):** คลาสต้นแบบที่ส่งต่อคุณสมบัติ
* **คลาสลูก (Child/Derived Class):** คลาสที่สืบทอดคุณสมบัติมา และสามารถมีฟังก์ชันเฉพาะตัวเพิ่มเองได้

เราใช้คีย์เวิร์ด **`extends`** ในการสืบทอดคลาส

---

## 🚀 ตัวอย่างโค้ดใช้งานจริง (Example)



```php
<?php
// คลาสแม่
class Vehicle {
  public $brand;
  
  public function __construct($brand) {
    $this->brand = $brand;
  }
  public function honk() {
    return "ปี๊บ ๆ!";
  }
}

// คลาสลูก สืบทอดมาจาก Vehicle
class Car extends Vehicle {
  public $model;

  public function __construct($brand, $model) {
    // เรียกใช้ constructor ของคลาสแม่
    parent::__construct($brand);
    $this->model = $model;
  }
  
  public function intro() {
    return "นี่คือรถยนต์ยี่ห้อ {$this->brand} รุ่น {$this->model} " . $this->honk();
  }
}

$myCar = new Car("Toyota", "Yaris");
echo $myCar->intro(); 
// ผลลัพธ์: นี่คือรถยนต์ยี่ห้อ Toyota รุ่น Yaris ปี๊บ ๆ!
?>