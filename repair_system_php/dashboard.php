<?php
require_once "config.php"; requireLogin();

$total=$pdo->query("SELECT COUNT(*) c FROM repair_requests")->fetch()['c'];
$pending=$pdo->query("SELECT COUNT(*) c FROM repair_requests WHERE status='pending'")->fetch()['c'];
$progress=$pdo->query("SELECT COUNT(*) c FROM repair_requests WHERE status='in_progress'")->fetch()['c'];
$completed=$pdo->query("SELECT COUNT(*) c FROM repair_requests WHERE status='completed'")->fetch()['c'];

$sql="SELECT r.*,u.fullname requester,t.fullname technician
      FROM repair_requests r
      JOIN users u ON r.user_id=u.id
      LEFT JOIN users t ON r.assigned_to=t.id
      ORDER BY r.created_at DESC";
$rows=$pdo->query($sql)->fetchAll();
include "header.php";
?>
<div class="d-flex justify-content-between align-items-center mb-4">
<h2>Dashboard</h2>
<?php if(in_array($_SESSION['user']['role'],['teacher','admin'])): ?>
<a href="new_repair.php" class="btn btn-primary">+ แจ้งซ่อมใหม่</a>
<?php endif; ?>
</div>
<div class="row g-3 mb-4">
<?php foreach([['งานทั้งหมด',$total],['รอรับเรื่อง',$pending],['กำลังดำเนินการ',$progress],['ซ่อมเสร็จ',$completed]] as $s): ?>
<div class="col-md-3"><div class="card stat p-3"><h6><?=$s[0]?></h6><h2><?=$s[1]?></h2></div></div>
<?php endforeach; ?>
</div>
<div class="card p-3">
<h5>รายการแจ้งซ่อม</h5>
<div class="table-responsive">
<table class="table align-middle">
<thead><tr><th>เลขที่</th><th>ผู้แจ้ง</th><th>สถานที่</th><th>ปัญหา</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
<td><?=htmlspecialchars($r['request_no'])?></td>
<td><?=htmlspecialchars($r['requester'])?></td>
<td><?=htmlspecialchars($r['building']." / ".$r['room'])?></td>
<td><?=htmlspecialchars($r['description'])?></td>
<td><span class="badge bg-secondary"><?=htmlspecialchars($r['status'])?></span></td>
<td><a href="repair_detail.php?id=<?=$r['id']?>" class="btn btn-sm btn-outline-primary">ดูรายละเอียด</a></td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></div>
<?php include "footer.php"; ?>
