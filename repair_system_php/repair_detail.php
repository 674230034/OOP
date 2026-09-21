<?php
require_once "config.php"; requireLogin();
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare("SELECT r.*,u.fullname requester,t.fullname technician FROM repair_requests r
JOIN users u ON r.user_id=u.id LEFT JOIN users t ON r.assigned_to=t.id WHERE r.id=?");
$stmt->execute([$id]); $r=$stmt->fetch();
if(!$r) die("ไม่พบข้อมูล");
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['action']) && $_POST['action']==='status'){
        $status=$_POST['status'];
        if(in_array($status,['accepted','in_progress','completed'])){
            $completion=($status==='completed')?date('Y-m-d'):null;
            $pdo->prepare("UPDATE repair_requests SET status=?,completion_date=?,cost=?,result=? WHERE id=?")
                ->execute([$status,$completion,$_POST['cost']!==''?$_POST['cost']:null,$_POST['result']??null,$id]);
        }
        header("Location: repair_detail.php?id=$id&updated=1");exit;
    }
    if(isset($_POST['action']) && $_POST['action']==='assign' && $_SESSION['user']['role']==='admin'){
        $pdo->prepare("UPDATE repair_requests SET assigned_to=?,due_date=?,status='accepted' WHERE id=?")
            ->execute([(int)$_POST['technician_id'],$_POST['due_date']?:null,$id]);
        header("Location: repair_detail.php?id=$id&updated=1");exit;
    }
    if(isset($_POST['action']) && $_POST['action']==='comment'){
        $pdo->prepare("INSERT INTO comments(repair_id,user_id,comment) VALUES(?,?,?)")
            ->execute([$id,$_SESSION['user']['id'],trim($_POST['comment'])]);
        header("Location: repair_detail.php?id=$id");exit;
    }
}
$images=$pdo->prepare("SELECT * FROM repair_images WHERE repair_id=?");$images->execute([$id]);$images=$images->fetchAll();
$comments=$pdo->prepare("SELECT c.*,u.fullname FROM comments c JOIN users u ON c.user_id=u.id WHERE repair_id=? ORDER BY c.created_at DESC");$comments->execute([$id]);$comments=$comments->fetchAll();
$techs=$pdo->query("SELECT id,fullname FROM users WHERE role='technician'")->fetchAll();
include "header.php";
?>
<?php if(isset($_GET['created'])): ?><script>Swal.fire('สำเร็จ','แจ้งซ่อมเรียบร้อยแล้ว','success');</script><?php endif; ?>
<?php if(isset($_GET['updated'])): ?><script>Swal.fire('สำเร็จ','อัปเดตข้อมูลเรียบร้อยแล้ว','success');</script><?php endif; ?>

<div class="d-flex justify-content-between mb-3"><h2>รายละเอียดใบแจ้งซ่อม</h2><a href="dashboard.php" class="btn btn-secondary">กลับ</a></div>
<div class="card p-4 mb-3">
<h5><?=$r['request_no']?> <span class="badge bg-info"><?=$r['status']?></span></h5>
<p><b>ผู้แจ้ง:</b> <?=htmlspecialchars($r['requester'])?></p>
<p><b>สถานที่:</b> <?=htmlspecialchars($r['building'])?> / <?=htmlspecialchars($r['room'])?></p>
<p><b>ประเภท:</b> <?=htmlspecialchars($r['problem_type'])?> | <b>ความเร่งด่วน:</b> <?=htmlspecialchars($r['urgency'])?></p>
<p><b>รายละเอียด:</b><br><?=nl2br(htmlspecialchars($r['description']))?></p>
<p><b>ผู้รับผิดชอบ:</b> <?=htmlspecialchars($r['technician']??'-')?> | <b>กำหนดเสร็จ:</b> <?=htmlspecialchars($r['due_date']??'-')?></p>
</div>

<?php if($_SESSION['user']['role']==='admin'): ?>
<div class="card p-4 mb-3"><h5>👨‍🔧 มอบหมายงาน</h5>
<form method="post" class="row g-2"><input type="hidden" name="action" value="assign">
<div class="col-md-5"><select name="technician_id" class="form-select" required><?php foreach($techs as $t): ?><option value="<?=$t['id']?>"><?=$t['fullname']?></option><?php endforeach;?></select></div>
<div class="col-md-4"><input type="date" name="due_date" class="form-control"></div>
<div class="col-md-3"><button class="btn btn-primary w-100">มอบหมาย</button></div>
</form></div>
<?php endif; ?>

<?php if(in_array($_SESSION['user']['role'],['admin','technician'])): ?>
<div class="card p-4 mb-3"><h5>🔧 อัปเดตงาน</h5>
<form method="post"><input type="hidden" name="action" value="status">
<select name="status" class="form-select mb-2">
<option value="accepted">รับเรื่องแล้ว</option><option value="in_progress">กำลังดำเนินการ</option><option value="completed">ซ่อมเสร็จ</option>
</select>
<input name="cost" type="number" step="0.01" class="form-control mb-2" placeholder="ค่าใช้จ่าย">
<textarea name="result" class="form-control mb-2" placeholder="ผลการซ่อม"></textarea>
<button class="btn btn-success">บันทึก</button></form></div>
<?php endif; ?>

<div class="card p-4 mb-3"><h5>📸 รูปภาพ</h5>
<div class="row"><?php foreach($images as $img): ?><div class="col-md-3 mb-2"><img src="<?=htmlspecialchars($img['image_path'])?>" class="thumb"><br><small><?=$img['image_type']?></small></div><?php endforeach;?></div>
</div>

<div class="card p-4"><h5>💬 ความคิดเห็น</h5>
<form method="post" class="mb-3"><input type="hidden" name="action" value="comment"><textarea name="comment" class="form-control mb-2" required></textarea><button class="btn btn-outline-primary">เพิ่มความคิดเห็น</button></form>
<?php foreach($comments as $c): ?><div class="border-bottom py-2"><b><?=htmlspecialchars($c['fullname'])?></b>: <?=nl2br(htmlspecialchars($c['comment']))?></div><?php endforeach;?>
</div>
<?php include "footer.php"; ?>
