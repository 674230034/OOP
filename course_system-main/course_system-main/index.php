<?php
// เริ่มต้นใช้งาน Session เพื่อจดจำตัวนักศึกษาที่กำลังทำรายการ
session_start();

require_once "Course.php";
require_once "Student.php";
require_once "Enrollment.php";

// ================================
// เชื่อมต่อฐานข้อมูล
// ================================
$host = "localhost";
$dbname = "u835268696_course_system";
$username = "u835268696_tester4";
$password = "!Chang2992";
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $e->getMessage());
}

// ================================
// จัดการ State (เข้าสู่ระบบจำลอง / ออกจากระบบ)
// ================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    // 1. ระบุตัวตนนักศึกษา (ไม่ใช่การ Login ไม่มีรหัสผ่าน)
    if ($action === "start_session") {
        $input_id = trim($_POST["student_id"]);
        $input_faculty = trim($_POST["faculty"]);
        $input_year = (int)$_POST["year"];

        if (!empty($input_id) && !empty($input_faculty) && !empty($input_year)) {
            // เช็คว่ามีนักศึกษาคนนี้ใน DB หรือยัง
            $stmt = $db->prepare("SELECT * FROM students WHERE student_id = ?");
            $stmt->execute([$input_id]);
            $existing = $stmt->fetch();

            // ถ้ายังไม่มี ให้บันทึกข้อมูลใหม่ลงฐานข้อมูล
            if (!$existing) {
                $insertStmt = $db->prepare("INSERT INTO students (student_id, faculty, year) VALUES (?, ?, ?)");
                $insertStmt->execute([$input_id, $input_faculty, $input_year]);
            } else {
                // ถ้ามีแล้ว ให้อัปเดตข้อมูลคณะและชั้นปีให้เป็นปัจจุบัน
                $updateStmt = $db->prepare("UPDATE students SET faculty = ?, year = ? WHERE student_id = ?");
                $updateStmt->execute([$input_faculty, $input_year, $input_id]);
            }

            // บันทึกลง Session แล้วรีเฟรชหน้า
            $_SESSION["current_student_id"] = $input_id;
            header("Location: index.php");
            exit;
        }
    }

    // 2. กดยืนยันการลงทะเบียน (รีเซ็ตหน้าจอสำหรับคนต่อไป)
    if ($action === "finish_session") {
        unset($_SESSION["current_student_id"]);
        header("Location: index.php");
        exit;
    }
}

// ================================
// ตัวแปรสำหรับแจ้งเตือน
// ================================
$alertStatus = "";
$alertMessage = "";

// หากมีการระบุนักศึกษาแล้ว ให้ประมวลผลการลงทะเบียน
if (isset($_SESSION["current_student_id"])) {
    
    // ดึงข้อมูลนักศึกษาคนปัจจุบัน
    $studentId = $_SESSION["current_student_id"];
    $stmt = $db->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$studentId]);
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

    $student = new Student($studentData["student_id"], $studentData["faculty"], $studentData["year"]);
    $enrollment = new Enrollment($db);

    // รับคำสั่งลงทะเบียน / ยกเลิก
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $courseCode = $_POST["course_code"] ?? "";
        $action = $_POST["action"] ?? "";

        if ($action === "register" || $action === "action_cancel") {
            $sql = "SELECT * FROM courses WHERE course_code = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$courseCode]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $course = new Course(
                    $data["course_code"], $data["course_name"], $data["instructor"],
                    $data["credits"], $data["capacity"], $data["enrolled"]
                );

                if ($action === "register") {
                    $response = $enrollment->register($student, $course);
                } elseif ($action === "action_cancel") {
                    $response = $enrollment->cancel($student, $course);
                }

                if(isset($response)) {
                    list($alertStatus, $alertMessage) = explode("|", $response);
                }
            }
        }
    }

    // ดึงข้อมูลรายวิชาทั้งหมด
    $sql = "SELECT * FROM courses";
    $stmt = $db->query($sql);
    $courseRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $courses = [];
    foreach ($courseRows as $row) {
        $courses[$row["course_code"]] = new Course(
            $row["course_code"], $row["course_name"], $row["instructor"],
            $row["credits"], $row["capacity"], $row["enrolled"]
        );
    }

    // ดึงวิชาที่ลงทะเบียนแล้วเพื่อคำนวณหน่วยกิต
    $enrolledCourses = $enrollment->getStudentCourses($student->getStudentId());
    $totalCredits = 0;
    foreach ($enrolledCourses as $c) {
        $totalCredits += (int)$c["credits"];
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบลงทะเบียนเรียน - OOP Project</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f4f6f9; min-height: 100vh; display: flex; flex-direction: column; }
        .content-wrapper { flex: 1; }
        .course-card { transition: transform 0.2s; }
        .course-card:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">🎓 ระบบลงทะเบียนเรียน (OOP)</a>
    </div>
</nav>

<div class="container content-wrapper">
    
    <?php if (!isset($_SESSION["current_student_id"])): ?>
        
        <!-- หน้าจอระบุตัวตน (ไม่ใช่หน้า Login สอดคล้องกับโจทย์) -->
        <div class="row justify-content-center align-items-center mt-5">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-primary">👋 ยินดีต้อนรับ</h4>
                            <p class="text-muted">กรุณาระบุข้อมูลเพื่อทำรายการลงทะเบียนเรียน</p>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="start_session">
                            <div class="mb-3">
                                <label class="form-label fw-bold">รหัสนักศึกษา</label>
                                <input type="text" name="student_id" class="form-control form-control-lg" required placeholder="เช่น 674230034">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">คณะ</label>
                                <input type="text" name="faculty" class="form-control form-control-lg" required placeholder="เช่น เทคโนโลยีสารสนเทศ">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">ชั้นปีที่</label>
                                <input type="number" name="year" class="form-control form-control-lg" min="1" max="4" required placeholder="1-4">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">เข้าสู่ระบบลงทะเบียน</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>

        <!-- หน้าจอ Dashboard ลงทะเบียน -->
        <div class="row">
            <!-- Sidebar ข้อมูลนักศึกษาและวิชาที่ลงทะเบียน -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold text-primary">👤 ข้อมูลนักศึกษา</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>รหัสนักศึกษา:</strong> <?= $student->getStudentId(); ?></p>
                        <p class="mb-1"><strong>คณะ:</strong> <?= $student->getFaculty(); ?></p>
                        <p class="mb-3"><strong>ชั้นปีที่:</strong> <?= $student->getYear(); ?></p>
                        
                        <div class="progress mb-2" style="height: 20px;">
                            <?php 
                                $percent = ($totalCredits / $student->getMaxCredits()) * 100; 
                                $bgClass = $percent > 80 ? 'bg-danger' : 'bg-success';
                            ?>
                            <div class="progress-bar <?= $bgClass ?>" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $totalCredits ?>" aria-valuemin="0" aria-valuemax="<?= $student->getMaxCredits() ?>">
                                หน่วยกิต: <?= $totalCredits; ?> / <?= $student->getMaxCredits(); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold text-success">📚 วิชาที่ลงทะเบียนแล้ว</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php if (count($enrolledCourses) === 0): ?>
                                <li class="list-group-item text-muted text-center py-4">ยังไม่มีวิชาที่ลงทะเบียน</li>
                            <?php else: ?>
                                <?php foreach ($enrolledCourses as $course): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= $course["course_code"]; ?></strong><br>
                                            <small class="text-muted"><?= $course["course_name"]; ?></small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill"><?= $course["credits"]; ?> หน่วยกิต</span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- ปุ่มยืนยันเสร็จสิ้น และให้คนต่อไปกรอก -->
                <form method="POST" id="finishForm">
                    <input type="hidden" name="action" value="finish_session">
                    <button type="button" class="btn btn-success btn-lg w-100 shadow fw-bold" onclick="confirmFinish()">
                        ✅ ยืนยันเสร็จสิ้น (ทำรายการคนต่อไป)
                    </button>
                </form>
            </div>

            <!-- Main Content รายวิชาที่เปิดสอน -->
            <div class="col-lg-8">
                <h4 class="fw-bold mb-3">📝 รายวิชาที่เปิดให้ลงทะเบียน</h4>
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <?php foreach ($courses as $course): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0 course-card">
                                <div class="card-body">
                                    <h5 class="card-title text-primary fw-bold"><?= $course->getCourseCode(); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-dark"><?= $course->getCourseName(); ?></h6>
                                    <hr>
                                    <p class="card-text mb-1"><small><strong>อาจารย์:</strong> <?= $course->getInstructor(); ?></small></p>
                                    <p class="card-text mb-1"><small><strong>หน่วยกิต:</strong> <?= $course->getCredits(); ?> หน่วยกิต</small></p>
                                    <p class="card-text"><small><strong>ที่นั่งคงเหลือ:</strong> <?= $course->getAvailableSeats(); ?> / <?= $course->getCapacity(); ?></small></p>
                                </div>
                                <div class="card-footer bg-white border-0 pb-3">
                                    <?php
                                    $alreadyEnrolled = false;
                                    foreach ($enrolledCourses as $item) {
                                        if ($item["course_code"] === $course->getCourseCode()) {
                                            $alreadyEnrolled = true;
                                            break;
                                        }
                                    }
                                    ?>

                                    <?php if ($alreadyEnrolled): ?>
                                        <form method="POST">
                                            <input type="hidden" name="course_code" value="<?= $course->getCourseCode(); ?>">
                                            <input type="hidden" name="action" value="action_cancel">
                                            <button class="btn btn-outline-danger w-100" type="submit">ยกเลิกการลงทะเบียน</button>
                                        </form>
                                    <?php elseif (!$course->is_full()): ?>
                                        <form method="POST">
                                            <input type="hidden" name="course_code" value="<?= $course->getCourseCode(); ?>">
                                            <input type="hidden" name="action" value="register">
                                            <button class="btn btn-primary w-100" type="submit">ลงทะเบียน</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary w-100" disabled>วิชาเต็มแล้ว</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<footer class="bg-dark text-white text-center py-3 mt-5">
    <small>พัฒนาระบบโดย: ธนพล น้อยประสาน และ ปภัสสร | นำเสนอโครงงาน Object-Oriented Programming</small>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 Logic -->
<script>
    // แจ้งเตือนเมื่อกดลงทะเบียนหรือยกเลิก
    <?php if ($alertMessage !== ""): ?>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: '<?= $alertStatus ?>',
                title: '<?= $alertStatus == "success" ? "สำเร็จ!" : "แจ้งเตือน!" ?>',
                text: '<?= $alertMessage ?>',
                confirmButtonColor: '#0d6efd'
            });
        });
    <?php endif; ?>

    // ยืนยันก่อนกดเสร็จสิ้น
    function confirmFinish() {
        Swal.fire({
            title: 'ยืนยันทำรายการเสร็จสิ้น?',
            text: "ระบบจะล้างข้อมูลเพื่อเริ่มลงทะเบียนให้นักศึกษาคนต่อไป",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, เสร็จสิ้น!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('finishForm').submit();
            }
        })
    }
</script>

</body>
</html>