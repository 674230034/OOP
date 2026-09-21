<?php
require_once "config.php";
if (isset($_SESSION['user'])) { header("Location: dashboard.php"); exit; }

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $user["password"] === md5($password)) {
        unset($user["password"]);
        $_SESSION["user"] = $user;
        header("Location: dashboard.php");
        exit;
    }
    $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
}
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>เข้าสู่ระบบ - ระบบแจ้งซ่อม</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width:430px;margin-top:90px">
<div class="card shadow">
<div class="card-body p-4">
<h3 class="text-center mb-4">🔧 ระบบแจ้งซ่อมภายในโรงเรียน</h3>
<?php if($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post">
<label class="form-label">ชื่อผู้ใช้</label>
<input name="username" class="form-control mb-3" required>
<label class="form-label">รหัสผ่าน</label>
<input type="password" name="password" class="form-control mb-3" required>
<button class="btn btn-primary w-100">เข้าสู่ระบบ</button>
</form>
<hr>
<small>ทดลอง: admin / teacher / tech<br>รหัสผ่าน: 123456</small>
</div></div></div>
</body></html>