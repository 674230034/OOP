<?php
session_start();

$host = "localhost";
$dbname = "school_repair";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die("เชื่อมต่อฐานข้อมูลไม่ได้: " . $e->getMessage());
}

function requireLogin() {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }
}

function requireRole($roles) {
    requireLogin();
    if (!in_array($_SESSION['user']['role'], $roles)) {
        die("ไม่มีสิทธิ์เข้าถึงหน้านี้");
    }
}
?>