<?php
require_once "config.php"; requireRole(['teacher','admin']);
$msg="";
if($_SERVER['REQUEST_METHOD']==='POST'){
    $requestNo='RP-'.date('Ym').'-'.str_pad((string)(($pdo->query("SELECT COUNT(*) FROM repair_requests")->fetchColumn())+1),3,'0',STR_PAD_LEFT);
    $stmt=$pdo->prepare("INSERT INTO repair_requests
    (request_no,user_id,building,room,problem_type,description,urgency)
    VALUES(?,?,?,?,?,?,?)");
    $stmt->execute([$requestNo,$_SESSION['user']['id'],$_POST['building'],$_POST['room'],
        $_POST['problem_type'],$_POST['description'],$_POST['urgency']]);
    $id=$pdo->lastInsertId();

    if(!empty($_FILES['image']['name']) && $_FILES['image']['error']===UPLOAD_ERR_OK){
        $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
        $mime=mime_content_type($_FILES['image']['tmp_name']);
        if(isset($allowed[$mime]) && $_FILES['image']['size']<=5*1024*1024){
            if(!is_dir('uploads')) mkdir('uploads',0755,true);
            $name=uniqid('repair_').'.'.$allowed[$mime];
            move_uploaded_file($_FILES['image']['tmp_name'],'uploads/'.$name);
            $pdo->prepare("INSERT INTO repair_images(repair_id,image_type,image_path) VALUES(?,?,?)")
                ->execute([$id,'before','uploads/'.$name]);
        }
    }
    header("Location: repair_detail.php?id=".$id."&created=1"); exit;
}
include "header.php";
?>
<h2>📝 แจ้งซ่อมใหม่</h2>
<div class="card p-4">
<form method="post" enctype="multipart/form-data">
<div class="row">
<div class="col-md-6 mb-3"><label>อาคาร</label><input name="building" class="form-control" required></div>
<div class="col-md-6 mb-3"><label>ห้อง</label><input name="room" class="form-control" required></div>
<div class="col-md-6 mb-3"><label>ประเภทปัญหา</label>
<select name="problem_type" class="form-select" required>
<option value="electrical">ไฟฟ้า</option><option value="furniture">เฟอร์นิเจอร์</option>
<option value="computer">คอมพิวเตอร์</option><option value="aircon">แอร์</option><option value="other">อื่นๆ</option>
</select></div>
<div class="col-md-6 mb-3"><label>ความเร่งด่วน</label>
<select name="urgency" class="form-select"><option value="low">ต่ำ</option><option value="medium" selected>ปานกลาง</option><option value="high">สูง</option></select></div>
<div class="col-12 mb-3"><label>รายละเอียดปัญหา</label><textarea name="description" class="form-control" rows="4" required></textarea></div>
<div class="col-12 mb-3"><label>รูปภาพก่อนซ่อม</label><input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif"></div>
</div>
<button class="btn btn-success">ส่งแจ้งซ่อม</button>
<a href="dashboard.php" class="btn btn-secondary">ยกเลิก</a>
</form></div>
<?php include "footer.php"; ?>
