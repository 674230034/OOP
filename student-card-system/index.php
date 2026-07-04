 <?php     
    // index.php     
          
    require_once 'vendor/autoload.php';     
          
    use App\Services\StudentCardService;     
    use App\Models\Student;     
    use App\Models\StudentCard;     
          
    // Autoloader setup (ถ้าไม่ใช้ Composer)     
    spl_autoload_register(function ($class) {     
        $prefix = 'App\\';     
        $baseDir = __DIR__ . '/src/';     
             
        $len = strlen($prefix);     
        if (strncmp($prefix, $class, $len) !== 0) {     
            return;     
        }     
             
        $relativeClass = substr($class, $len);     
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . 
'.php';     
             
        if (file_exists($file)) {     
            require $file;     
        }     
    });     
          
    // ตัวอย่างการใช้งาน     
    $service = new StudentCardService();     
          
    // สร้างข้อมูลนักศึกษา (จากฐานข้อมูลหรือ form)     
    $studentData = [     
        'id' => 'STU001',     
        'firstName' => 'ปภัสสร',     
        'lastName' => 'แย้มชื่น',     
        'studentId' => '674230034',     
        'faculty' => 'คณะวิทยาศาสตร์และเทคโนโลยี',     
        'photo' => 'assets/photos/paphatson.jpg',     
        'university' => 'NAKORN PATHOM RAJBHAT UNIVERSITY'     
    ];     
          
    // สร้างบัตรนักศึกษา     
    $card = $service->createStudentCard($studentData);     
          
    // ตรวจสอบความถูกต้อง     
    $validation = $service->validateCard($card);     
          
    if ($validation['isValid']) {     
        // แสดงบัตร     
        $service->displayCard($card);     
    } else {     
        echo "<div class='error'>";     
echo "<h2>ข้อผิดพลาด:</h2>";     
echo "<ul>";     
foreach ($validation['errors'] as $error) {     
echo "<li>{$error}</li>";     
}     
echo "</ul>";     
echo "</div>";     
} 