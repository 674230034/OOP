<?php

class Room
{
    private string $roomNo;
    private string $roomType;
    private float $pricePerNight;
    private bool $isAvailable;

    public function __construct(string $roomNo, string $roomType, float $pricePerNight, bool $isAvailable = true)
    {
        $this->roomNo = $roomNo;
        $this->roomType = $roomType;
        $this->pricePerNight = $pricePerNight;
        $this->isAvailable = $isAvailable;
    }

    // 3.1 ตรวจสอบห้องพัก
    public function checkAvailability(): bool
    {
        return $this->isAvailable;
    }

    public function setAvailable(bool $status): void
    {
        $this->isAvailable = $status;
    }

    public function getPricePerNight(): float
    {
        return $this->pricePerNight;
    }

    public function getRoomNo(): string
    {
        return $this->roomNo;
    }
}