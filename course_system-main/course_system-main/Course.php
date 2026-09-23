<?php
/**
 * Class Course
 * ผู้รับผิดชอบ: ปภัสสร (รับผิดชอบส่วนของการจัดการข้อมูลรายวิชาและที่นั่ง)
 */
class Course
{
    private string $courseCode;
    private string $courseName;
    private string $instructor;
    private int $credits;
    private int $capacity;
    private int $enrolled;

    public function __construct(
        string $courseCode,
        string $courseName,
        string $instructor,
        int $credits,
        int $capacity,
        int $enrolled = 0
    ) {
        $this->courseCode = $courseCode;
        $this->courseName = $courseName;
        $this->instructor = $instructor;
        $this->credits = $credits;
        $this->capacity = $capacity;
        $this->enrolled = $enrolled;
    }

    // ตรวจสอบว่าวิชาเต็มหรือยัง
    public function is_full(): bool
    {
        return $this->enrolled >= $this->capacity;
    }

    // เพิ่มจำนวนผู้ลงทะเบียน
    public function addStudent(): bool
    {
        if ($this->is_full()) {
            return false;
        }

        $this->enrolled++;

        return true;
    }

    // ลดจำนวนผู้ลงทะเบียน
    public function removeStudent(): void
    {
        if ($this->enrolled > 0) {
            $this->enrolled--;
        }
    }

    public function getCourseCode(): string
    {
        return $this->courseCode;
    }

    public function getCourseName(): string
    {
        return $this->courseName;
    }

    public function getInstructor(): string
    {
        return $this->instructor;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getEnrolled(): int
    {
        return $this->enrolled;
    }

    public function getAvailableSeats(): int
    {
        return $this->capacity - $this->enrolled;
    }
}