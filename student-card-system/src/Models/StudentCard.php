<?php
// src/Models/StudentCard.php     

namespace App\Models;

class StudentCard
{
    private string $cardNumber;
    private \DateTime $issueDate;
    private \DateTime $expireDate;
    private Student $student;
    private string $cardType;

    public function __construct(
        Student $student,
        string $cardNumber = null,
        int $validYears = 4
    ) {
        $this->student = $student;
        $this->cardNumber = $cardNumber ?? $this->generateCardNumber();
        $this->issueDate = new \DateTime();
        $this->expireDate = (clone $this->issueDate)->modify("+{$validYears} years");
        $this->cardType = 'VISA'; // หรือ MasterCard, etc.     
    }

    private function generateCardNumber(): string
    {
        // Generate card number based on student ID and random     
        $prefix = '1234';
        $studentPart = substr($this->student->getStudentId(), 0, 4);
        $random1 = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $random2 = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        return "{$prefix} {$studentPart} {$random1} {$random2}";
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function getIssueDate(): \DateTime
    {
        return $this->issueDate;
    }

    public function getExpireDate(): \DateTime
    {
        return $this->expireDate;
    }

public function getExpireDateThai(): string
{
    // 1. แปลงปี ค.ศ. 4 หลัก (เช่น 2026) ให้เป็น พ.ศ. 4 หลัก (จะได้ 2569)
    $thaiYearFull = (int)$this->expireDate->format('Y') + 543; 
    
    // 2. ใช้ % 100 เพื่อตัดเอาเฉพาะเลข 2 หลักท้าย (จาก 2569 จะเหลือแค่ 69)
    $year = $thaiYearFull % 100; 
    
    // 3. เติมเลข 0 ด้านหน้าในกรณีที่คำนวณแล้วได้หลักเดียว (เช่น พ.ศ. 2505 -> 05)
    $yearStr = str_pad($year, 2, '0', STR_PAD_LEFT);
    
    // 4. ส่งค่ากลับในรูปแบบ เดือน/ปี พ.ศ. 2 หลัก (เช่น 07/69)
    return $this->expireDate->format('m/') . $yearStr;
}

    public function getStudent(): Student
    {
        return $this->student;
    }

    public function getCardType(): string
    {
        return $this->cardType;
    }

    public function isValid(): bool
    {
        return new \DateTime() < $this->expireDate;
    }

    public function toArray(): array
    {
        return [
            'cardNumber' => $this->cardNumber,
            'issueDate' => $this->issueDate->format('Y-m-d'),
            'expireDate' => $this->expireDate->format('Y-m-d'),
            'expireDateThai' => $this->getExpireDateThai(),
            'cardType' => $this->cardType,
            'isValid' => $this->isValid(),
            'student' => $this->student->toArray()
        ];
    }
}