# เอกสารวิเคราะห์ ออกแบบ และพัฒนาเชิงวัตถุ (OOAD & OOP)
## ระบบจองห้องพัก (Room Booking System)

---

## ส่วนที่ 1: Object-Oriented Analysis & Design (OOAD)

### 1.1 Object-Oriented Analysis (OOA) — การวิเคราะห์ระบบ

การวิเคราะห์ระบบอ้างอิงจาก **Use Case Diagram** ร่วมกับข้อกำหนดระบบที่ได้รับการพัฒนาเพิ่มเติม

* **Actors (ผู้มีส่วนร่วมในระบบ)**
  * **ผู้เช่า (Tenant):** ผู้ใช้งานที่เลือกห้องพักและกรอกข้อมูลส่วนตัวเพื่อทำการจอง
  * **พนักงาน (Staff):** ผู้ดูแลระบบที่รับผิดชอบการตรวจสอบความพร้อมของห้องพักและคำนวณเงินประกันการจอง

* **Use Cases & Core Features (กรณีการใช้งานหลัก)**
  * **3.0 จอง (Main Use Case):** กระบวนการสร้างรายการจอง โดยรับข้อมูลผู้เช่า เลือกห้องพัก เลือกพนักงานผู้ดูแล พร้อมบันทึกประวัติลงระบบ
  * **3.1 ตรวจสอบห้องพัก (Included Use Case):** ตรวจสอบสถานะความพร้อมของห้องพัก (ว่าง/ไม่ว่าง) และแสดงสถานะด้วยสีประจำประเภทห้องพัก
  * **3.2 คำนวณค่าประกัน (Included Use Case):** คำนวณเงินมัดจำประกันการจอง (คิดเป็น 50% ของราคาห้องพัก) โดยพนักงาน

---

### 1.2 Object-Oriented Design (OOD) — การออกแบบระบบเชิงวัตถุ

#### Class Structure & Responsibilities

| Class Name | Attributes | Methods / Functions | Responsibilities |
| :--- | :--- | :--- | :--- |
| **`Room`** | `roomNo`, `roomType`, `pricePerNight`, `isAvailable` | `checkAvailability()` **(3.1)**<br>`setAvailable()`<br>`getColorClass()` | เก็บข้อมูลห้องพัก ตรวจสอบสถานะ และคืนค่าคลาสสี (Bootstrap Badge/Card) ตามประเภทและราคาห้องพัก |
| **`Staff`** | `staffId`, `name` | `calculateDeposit()` **(3.2)**<br>`getName()` | เก็บข้อมูลพนักงาน และประมวลผลคำนวณค่าประกันการจอง |
| **`Tenant`** | `tenantId`, `name`, `phone` | `getName()`, `getPhone()` | จัดเก็บข้อมูลส่วนตัวของผู้เช่า |
| **`Booking`** | `bookingId`, `tenant`, `room`, `staff`, `depositAmount`, `status`, `bookingDate` | `processBooking()` **(3.0)**<br>`getBookingDetails()` | ควบคุม Workflow การจอง ประสานงานระหว่างวัตถุ และสรุปข้อมูลการจอง |

---

## ส่วนที่ 2: Object-Oriented Programming (OOP)

### 2.1 การประยุกต์ใช้หลักการ OOP 4 ประการ

1. **Encapsulation (การห่อหุ้มข้อมูล):**
   * ซ่อน Attributes ทั้งหมดในทุก Class ด้วย Access Modifier แบบ `private` 
   * ควบคุมการอ่านและแก้ไขค่าผ่าน Getter/Setter Methods เช่น `checkAvailability()` และ `setAvailable()`

2. **Abstraction (การซ่อนความซับซ้อน):**
   * คลาส `Booking` ซ่อน Logic การตรวจสอบห้องว่างและการคำนวณเงินประกันไว้ภายใน Method `processBooking()`
   * คลาส `Room` ซ่อนเงื่อนไขการส่งคืนค่าคลาสสีตามประเภทห้องพักไว้ใน Method `getColorClass()`

3. **Single Responsibility Principle (SRP):**
   * แยกหน้าที่ชัดเจน: `Room` (จัดการห้อง), `Staff` (คำนวณประกัน), `Tenant` (เก็บข้อมูลผู้เช่า) และ `Booking` (ควบคุมการจอง)

4. **Object Association & Dependency Injection:**
   * คลาส `Booking` มีความสัมพันธ์แบบ **Has-A** โดยรับวัตถุ `Tenant`, `Room`, และ `Staff` เข้ามาทาง Constructor ในขั้นตอนการสร้าง Object

---

### 2.2 System Architecture & State Management

* **Object Lifecycle & Session Handling:**
  * เมื่อเปิดใช้งาน ระบบจะจำลองวัตถุ `Room` แบ่งตามราคาและสีลงใน `$_SESSION['rooms']`
  * เมื่อมีการส่งแบบฟอร์ม (POST Request) ระบบจะดึง Instance ของ `Room` และ `Staff` ที่เลือก มาสร้างเป็น Instance ของ `Booking`
* **Real-time State Update:**
  * เมื่อสั่ง `$booking->processBooking()` สำเร็จ วัตถุ `Room` จะถูกอัปเดตสถานะเป็น `isAvailable = false` (แสดงสีเตือน "ไม่ว่าง/มีผู้จอง")
  * รายการจองจะถูกบันทึกลง `$_SESSION['bookings_history']` เพื่อนำไป Render ในตารางแสดงสถานะการจองแบบ Real-time

---

### 2.3 การเชื่อมโยงกับ Use Case Diagram

* **Use Case 3.0 (จอง):** ตรงกับ Method `Booking::processBooking()`
* **Use Case 3.1 (ตรวจสอบห้องพัก):** ตรงกับ Method `Room::checkAvailability()` และ `Room::getColorClass()`
* **Use Case 3.2 (คำนวณค่าประกัน):** ตรงกับ Method `Staff::calculateDeposit()`