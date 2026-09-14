<?php

class Student
{
    private string $studentId;
    private string $faculty;
    private int $year;

    private array $enrolledCourses = [];

    // หน่วยกิตสูงสุด
    private int $maxCredits = 22;

    public function __construct(
        string $studentId,
        string $faculty,
        int $year
    ) {
        $this->studentId = $studentId;
        $this->faculty = $faculty;
        $this->year = $year;
    }

    // ตรวจสอบว่าเคยลงวิชานี้หรือยัง
    public function hasCourse(string $courseCode): bool
    {
        return in_array(
            $courseCode,
            $this->enrolledCourses
        );
    }

    // เพิ่มวิชา
    public function addCourse(string $courseCode): void
    {
        $this->enrolledCourses[] = $courseCode;
    }

    // ลบวิชา
    public function removeCourse(string $courseCode): void
    {
        $key = array_search(
            $courseCode,
            $this->enrolledCourses
        );

        if ($key !== false) {
            unset($this->enrolledCourses[$key]);
            $this->enrolledCourses =
                array_values($this->enrolledCourses);
        }
    }

    // รับรายการวิชาที่ลงทะเบียน
    public function setEnrolledCourses(
        array $courses
    ): void {
        $this->enrolledCourses = $courses;
    }

    // คำนวณหน่วยกิตรวม
    public function getTotalCredits(
        array $courseData
    ): int {

        $total = 0;

        foreach ($this->enrolledCourses as $courseCode) {

            if (isset($courseData[$courseCode])) {
                $total += $courseData[$courseCode]->getCredits();
            }
        }

        return $total;
    }

    public function getStudentId(): string
    {
        return $this->studentId;
    }

    public function getFaculty(): string
    {
        return $this->faculty;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMaxCredits(): int
    {
        return $this->maxCredits;
    }

    public function getEnrolledCourses(): array
    {
        return $this->enrolledCourses;
    }
}