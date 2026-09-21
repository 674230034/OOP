<?php require_once "config.php"; ?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ระบบแจ้งซ่อมภายในโรงเรียน</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body{background:#f5f7fb}.navbar{background:#2c3e50}.card{border:0;box-shadow:0 2px 10px rgba(0,0,0,.07)}
.stat{border-left:5px solid #667eea}.thumb{width:90px;height:70px;object-fit:cover;border-radius:6px}
</style>
</head>
<body>
<nav class="navbar navbar-dark">
<div class="container">
<a class="navbar-brand" href="dashboard.php">🔧 ระบบแจ้งซ่อม</a>
<div class="text-white">
<?=htmlspecialchars($_SESSION['user']['fullname'])?> |
<a class="text-warning text-decoration-none" href="logout.php">ออกจากระบบ</a>
</div>
</div></nav>
<div class="container py-4">
