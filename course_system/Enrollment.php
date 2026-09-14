<?php

require_once "Course.php";
require_once "Student.php";

class Enrollment
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // ลงทะเบียนเรียน
    public function register(
        Student $student,
        Course $course
    ): string {

        // ตรวจสอบวิชาเต็ม
        if ($course->is_full()) {
            return "❌ ไม่สามารถลงทะเบียนได้ เพราะวิชาเต็ม";
        }

        // ตรวจสอบวิชาซ้ำ
        $sql = "
            SELECT *
            FROM enrollments
            WHERE student_id = ?
            AND course_code = ?
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $student->getStudentId(),
            $course->getCourseCode()
        ]);

        if ($stmt->fetch()) {
            return "❌ คุณลงทะเบียนวิชานี้ไปแล้ว";
        }

        // ดึงวิชาที่ลงทะเบียน
        $sql = "
            SELECT c.*
            FROM enrollments e
            JOIN courses c
            ON e.course_code = c.course_code
            WHERE e.student_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $student->getStudentId()
        ]);

        $currentCredits = 0;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $currentCredits += (int)$row["credits"];
        }

        // ตรวจสอบหน่วยกิต
        if (
            $currentCredits + $course->getCredits()
            > $student->getMaxCredits()
        ) {
            return "❌ ไม่สามารถลงทะเบียนได้ เพราะหน่วยกิตเกิน 22 หน่วยกิต";
        }

        try {

            $this->db->beginTransaction();

            // เพิ่มการลงทะเบียน
            $sql = "
                INSERT INTO enrollments
                (student_id, course_code)
                VALUES (?, ?)
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                $student->getStudentId(),
                $course->getCourseCode()
            ]);

            // เพิ่มจำนวนคนลงทะเบียน
            $sql = "
                UPDATE courses
                SET enrolled = enrolled + 1
                WHERE course_code = ?
                AND enrolled < capacity
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                $course->getCourseCode()
            ]);

            $this->db->commit();

            return "✅ ลงทะเบียน {$course->getCourseCode()} สำเร็จ";

        } catch (Exception $e) {

            $this->db->rollBack();

            return "❌ เกิดข้อผิดพลาดในการลงทะเบียน";
        }
    }

    // ยกเลิกการลงทะเบียน
    public function cancel(
        Student $student,
        Course $course
    ): string {

        $sql = "
            SELECT *
            FROM enrollments
            WHERE student_id = ?
            AND course_code = ?
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $student->getStudentId(),
            $course->getCourseCode()
        ]);

        if (!$stmt->fetch()) {
            return "❌ ยังไม่ได้ลงทะเบียนวิชานี้";
        }

        try {

            $this->db->beginTransaction();

            // ลบการลงทะเบียน
            $sql = "
                DELETE FROM enrollments
                WHERE student_id = ?
                AND course_code = ?
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                $student->getStudentId(),
                $course->getCourseCode()
            ]);

            // เพิ่มที่นั่งกลับ
            $sql = "
                UPDATE courses
                SET enrolled = enrolled - 1
                WHERE course_code = ?
                AND enrolled > 0
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                $course->getCourseCode()
            ]);

            $this->db->commit();

            return "✅ ยกเลิก {$course->getCourseCode()} สำเร็จ";

        } catch (Exception $e) {

            $this->db->rollBack();

            return "❌ เกิดข้อผิดพลาด";
        }
    }

    // ดึงวิชาที่นักศึกษาลงทะเบียน
    public function getStudentCourses(
        string $studentId
    ): array {

        $sql = "
            SELECT c.*
            FROM enrollments e
            JOIN courses c
            ON e.course_code = c.course_code
            WHERE e.student_id = ?
            ORDER BY e.enrolled_at
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $studentId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}