<?php

require_once 'Room.php';
require_once 'Staff.php';
require_once 'Tenant.php';

class Booking
{
    private string $bookingId;
    private Tenant $tenant;
    private Room $room;
    private Staff $staff;
    private float $depositAmount;
    private string $status;

    public function __construct(string $bookingId, Tenant $tenant, Room $room, Staff $staff)
    {
        $this->bookingId = $bookingId;
        $this->tenant = $tenant;
        $this->room = $room;
        $this->staff = $staff;
        $this->depositAmount = 0.0;
        $this->status = 'Pending';
    }

    // 3.0 กระบวนการจอง
    public function processBooking(): bool
    {
        // เรียกใช้ 3.1 ตรวจสอบห้องพักผ่าน Object ของ Room
        if (!$this->room->checkAvailability()) {
            $this->status = 'Failed';
            return false;
        }

        // เรียกใช้ 3.2 คำนวณค่าประกันผ่าน Object ของ Staff
        $this->depositAmount = $this->staff->calculateDeposit($this->room->getPricePerNight());

        // เปลี่ยนสถานะห้องพักไม่ให้ว่าง
        $this->room->setAvailable(false);
        $this->status = 'Confirmed';

        return true;
    }

    public function getBookingDetails(): array
    {
        return [
            'bookingId' => $this->bookingId,
            'tenantName' => $this->tenant->getName(),
            'roomNo' => $this->room->getRoomNo(),
            'staffName' => $this->staff->getName(),
            'depositAmount' => $this->depositAmount,
            'status' => $this->status
        ];
    }
}