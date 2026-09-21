# ระบบแจ้งซ่อมภายในโรงเรียน PHP 8 + MySQL + Bootstrap + SweetAlert2

## ขั้นตอนติดตั้ง
1. ติดตั้ง XAMPP และเปิด Apache + MySQL
2. นำโฟลเดอร์ `repair_system` ไปไว้ใน `C:\xampp\htdocs\`
3. เปิด phpMyAdmin ที่ `http://localhost/phpmyadmin`
4. สร้าง/Import ไฟล์ `database.sql`
5. ตรวจสอบ `config.php` ให้ตรงกับ MySQL ของเครื่อง
6. เปิด `http://localhost/repair_system/`
7. Login ด้วย:
   - admin / 123456
   - teacher / 123456
   - tech / 123456

## การทำงาน
- Teacher/Admin: แจ้งซ่อม
- Admin: มอบหมายช่าง
- Technician/Admin: เปลี่ยนสถานะและบันทึกผล
- ทุกบทบาท: ดู Dashboard และรายละเอียด/ความคิดเห็น
- ระบบรองรับรูปก่อนซ่อม
- ใช้ SweetAlert2 แจ้งผลสำเร็จ
