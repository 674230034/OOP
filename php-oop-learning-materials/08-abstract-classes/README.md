# PHP OOP - คลาสต้นแบบ (Abstract Classes)

**Abstract Class** คือคลาสต้นแบบที่ไม่สามารถนำไปสร้างเป็น Object ตรง ๆ ได้ (สั่ง `new AbstractClass()` จะ Error) มันมีไว้เพื่อเป็น**พิมพ์เขียวให้คลาสลูกนำไปสืบทอด (extends) เท่านั้น**

* ต้องมีคีย์เวิร์ด **`abstract`** นำหน้าคลาส
* ภายในคลาสสามารถมี **Abstract Method** (เมธอดที่มีแต่ชื่อ ไม่มีโค้ดด้านใน) เพื่อ**บังคับ**ให้คลาสลูกทุกคลาสที่นำไปใช้ ต้องไปเขียนโค้ดทำงานจริงเอาเอง

---

## 🚀 ตัวอย่างโค้ดใช้งานจริง (Example)

```php
<?php
// คลาสแม่ที่เป็น Abstract
abstract class Car {
  public $name;
  public function __construct($name) {
    $this->name = $name;
  }
  // Abstract method (บังคับให้คลาสลูกไปเขียนโค้ดรันเอาเอง)
  abstract public function intro() : string;
}

// คลาสลูกที่สืบทอดไป "ต้อง" สร้างเมธอด intro() ไม่งั้นจะ Error
class Audi extends Car {
  public function intro() : string {
    return "นี่คือรถยนต์เยอรมัน! ยี่ห้อ: $this->name";
  }
}

$audi = new Audi("Audi Q7");
echo $audi->intro();
?>