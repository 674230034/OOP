<?php

class Tenant
{
    private string $tenantId;
    private string $name;
    private string $phone;

    public function __construct(string $tenantId, string $name, string $phone)
    {
        $this->tenantId = $tenantId;
        $this->name = $name;
        $this->phone = $phone;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}