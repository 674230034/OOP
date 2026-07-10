# PHP OOP - อินเตอร์เฟส (Interfaces)

**Interface** คล้ายกับ Abstract Class แต่เป็นข้อตกลง (Contract) ที่เข้มงวดกว่าเดิม อินเตอร์เฟสจะบอกแค่ว่า คลาสที่นำฉันไปใช้ "ต้องทำอะไรได้บ้าง" แต่จะไม่บอกว่า "ทำอย่างไร"

### ⚖️ ข้อแตกต่างระหว่าง Abstract Class กับ Interface
1. เมธอดทั้งหมดใน Interface ต้องเป็น `public` เท่านั้น และ**ห้าม**มีโค้ดทำงานอยู่ภายในเลย (มีได้แต่โครง)
2. คลาสตัวหนึ่งสามารถ `implements` (ใช้อินเตอร์เฟส) ได้**หลายตัวพร้อมกัน** (ในขณะที่ `extends` คลาสแม่ได้แค่ตัวเดียว)

---

## 🚀 ตัวอย่างโค้ดใช้งานจริง (Example)

เราใช้คีย์เวิร์ด **`interface`** ในการสร้าง และ **`implements`** ในการนำไปใช้

```php
<?php
interface Animal {
  public function makeSound(); // บังคับว่าคลาสสัตว์ต้องส่งเสียงได้
}

class Cat implements Animal {
  public function makeSound() {
    echo "เหมียว ๆ";
  }
}

class Dog implements Animal {
  public function makeSound() {
    echo "โฮ่ง ๆ";
  }
}

$cat = new Cat();
$cat->makeSound(); // ผลลัพธ์: เหมียว ๆ
?>