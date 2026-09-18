<?php

class Staff
{
    private string $staffId;
    private string $name;

    public function __construct(string $staffId, string $name)
    {
        $this->staffId = $staffId;
        $this->name = $name;
    }

    // 3.2 คำนวณค่าประกัน (ตัวอย่าง: ค่าประกันคิดเป็น 50% ของราคาห้องพัก)
    public function calculateDeposit(float $roomPrice): float
    {
        $depositRate = 0.50; // 50%
        return $roomPrice * $depositRate;
    }

    public function getName(): string
    {
        return $this->name;
    }
}