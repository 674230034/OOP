<?php

require_once "Course.php";
require_once "Student.php";
require_once "Enrollment.php";


// ================================
// เชื่อมต่อฐานข้อมูล
// ================================

$host = "localhost";
$dbname = "course_enrollment";
$username = "root";
$password = "";

try {

    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $db->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die("เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $e->getMessage());
}


// ================================
// สร้าง Object
// ================================

$student = new Student(
    "674230034",
    "เทคโนโลยีสารสนเทศ",
    2
);

$enrollment = new Enrollment($db);


// ================================
// รับข้อมูลจากปุ่ม
// ================================

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $courseCode = $_POST["course_code"] ?? "";
    $action = $_POST["action"] ?? "";

    // ดึงข้อมูลวิชา
    $sql = "
        SELECT *
        FROM courses
        WHERE course_code = ?
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        $courseCode
    ]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {

        $course = new Course(
            $data["course_code"],
            $data["course_name"],
            $data["instructor"],
            $data["credits"],
            $data["capacity"],
            $data["enrolled"]
        );

        if ($action === "register") {

            $message = $enrollment->register(
                $student,
                $course
            );

        } elseif ($action === "cancel") {

            $message = $enrollment->cancel(
                $student,
                $course
            );
        }
    }
}


// ================================
// ดึงข้อมูลรายวิชา
// ================================

$sql = "SELECT * FROM courses";

$stmt = $db->query($sql);

$courseRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$courses = [];

foreach ($courseRows as $row) {

    $courses[$row["course_code"]] = new Course(
        $row["course_code"],
        $row["course_name"],
        $row["instructor"],
        $row["credits"],
        $row["capacity"],
        $row["enrolled"]
    );
}


// ================================
// ดึงวิชาที่ลงทะเบียนแล้ว
// ================================

$enrolledCourses =
    $enrollment->getStudentCourses(
        $student->getStudentId()
    );


// คำนวณหน่วยกิต
$totalCredits = 0;

foreach ($enrolledCourses as $course) {

    $totalCredits += (int)$course["credits"];
}

?>

<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ระบบลงทะเบียนเรียน</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        header {
            background: #2563eb;
            color: white;
            text-align: center;
            padding: 30px;
        }

        header h1 {
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 30px auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,.08);
        }

        .card h2 {
            color: #2563eb;
            margin-top: 0;
        }

        .courses {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(250px, 1fr));

            gap: 20px;
        }

        .course {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,.08);
        }

        .course h3 {
            color: #2563eb;
            margin-top: 0;
        }

        button {
            width: 100%;
            border: none;
            padding: 11px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            margin-top: 10px;
        }

        .register {
            background: #2563eb;
            color: white;
        }

        .cancel {
            background: #dc2626;
            color: white;
        }

        button:hover {
            opacity: .85;
        }

        button:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }

        .message {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: bold;
        }

        .course-item {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .total {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }

        footer {
            background: #1e293b;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }

    </style>

</head>

<body>


<header>

    <h1>ระบบลงทะเบียนเรียน</h1>

    <p>Course Enrollment System</p>

</header>


<div class="container">


    <!-- ข้อมูลนักศึกษา -->

    <div class="card">

        <h2>ข้อมูลนักศึกษา</h2>

        <p>
            <strong>รหัสนักศึกษา:</strong>
            <?= $student->getStudentId(); ?>
        </p>

        <p>
            <strong>คณะ:</strong>
            <?= $student->getFaculty(); ?>
        </p>

        <p>
            <strong>ชั้นปี:</strong>
            <?= $student->getYear(); ?>
        </p>

        <p>
            <strong>หน่วยกิต:</strong>
            <?= $totalCredits; ?>
            /
            <?= $student->getMaxCredits(); ?>
        </p>

    </div>


    <!-- ข้อความแจ้งเตือน -->

    <?php if ($message !== ""): ?>

        <div class="message">

            <?= htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- รายวิชา -->

    <h2>รายวิชาที่เปิดให้ลงทะเบียน</h2>

    <div class="courses">

        <?php foreach ($courses as $course): ?>

            <div class="course">

                <h3>
                    <?= $course->getCourseCode(); ?>
                </h3>

                <p>
                    <strong>
                        <?= $course->getCourseName(); ?>
                    </strong>
                </p>

                <p>
                    อาจารย์:
                    <?= $course->getInstructor(); ?>
                </p>

                <p>
                    หน่วยกิต:
                    <?= $course->getCredits(); ?>
                    หน่วยกิต
                </p>

                <p>
                    ที่นั่งคงเหลือ:
                    <?= $course->getAvailableSeats(); ?>
                    /
                    <?= $course->getCapacity(); ?>
                </p>


                <?php

                $alreadyEnrolled = false;

                foreach ($enrolledCourses as $item) {

                    if (
                        $item["course_code"]
                        === $course->getCourseCode()
                    ) {
                        $alreadyEnrolled = true;
                        break;
                    }
                }

                ?>


                <?php if ($alreadyEnrolled): ?>

                    <form method="POST">

                        <input
                            type="hidden"
                            name="course_code"
                            value="<?= $course->getCourseCode(); ?>"
                        >

                        <input
                            type="hidden"
                            name="action"
                            value="cancel"
                        >

                        <button
                            class="cancel"
                            type="submit"
                        >
                            ยกเลิกการลงทะเบียน
                        </button>

                    </form>


                <?php elseif (!$course->is_full()): ?>

                    <form method="POST">

                        <input
                            type="hidden"
                            name="course_code"
                            value="<?= $course->getCourseCode(); ?>"
                        >

                        <input
                            type="hidden"
                            name="action"
                            value="register"
                        >

                        <button
                            class="register"
                            type="submit"
                        >
                            ลงทะเบียน
                        </button>

                    </form>


                <?php else: ?>

                    <button disabled>
                        วิชาเต็มแล้ว
                    </button>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>


    <!-- วิชาที่ลงทะเบียนแล้ว -->

    <div class="card">

        <h2>วิชาที่ลงทะเบียนแล้ว</h2>

        <?php if (count($enrolledCourses) === 0): ?>

            <p>ยังไม่มีวิชาที่ลงทะเบียน</p>

        <?php else: ?>

            <?php foreach ($enrolledCourses as $course): ?>

                <div class="course-item">

                    <strong>
                        <?= $course["course_code"]; ?>
                    </strong>

                    -
                    <?= $course["course_name"]; ?>

                    (
                    <?= $course["credits"]; ?>
                    หน่วยกิต
                    )

                </div>

            <?php endforeach; ?>


            <div class="total">

                หน่วยกิตรวม:
                <?= $totalCredits; ?>
                /
                <?= $student->getMaxCredits(); ?>

            </div>

        <?php endif; ?>

    </div>

</div>


<footer>

    Course Enrollment System | PHP OOP + MySQL

</footer>


</body>

</html>