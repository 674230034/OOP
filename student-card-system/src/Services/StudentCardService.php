<?php     
    // src/Services/StudentCardService.php     
          
    namespace App\Services;     
          
    use App\Models\Student;     
    use App\Models\StudentCard;     
    use App\Views\CardRenderer;     
          
    class StudentCardService     
    {     
        public function createStudentCard(array $data): StudentCard     
        {     
            $student = new Student(     
                $data['id'] ?? uniqid(),     
                $data['firstName'],     
                $data['lastName'],     
                $data['studentId'],     
                $data['faculty'],     
                $data['photo'] ?? 'assets/default-photo.jpg',     
                $data['university'] ?? 'NAKORN PATHOM RAJBHAT 
UNIVERSITY'     
            );     
          
            return new StudentCard($student);     
        }     
          
        public function displayCard(StudentCard $card): void     
        {     
            $renderer = new CardRenderer($card);     
            echo $renderer->render();     
        }     
          
        public function validateCard(StudentCard $card): array     
        {     
            $errors = [];     
                 
            if (empty($card->getStudent()->getFullName())) {     
                $errors[] = 'ชื่อ-สกุล ไม่ถูกต้อง';     
            }     
          
            if (strlen($card->getStudent()->getStudentId()) < 8) {     
                $errors[] = 'รหัสนักศึกษาไม่ถูกต้อง';     
            }     
          
            if (!$card->isValid()) {     
                $errors[] = 'บัตรหมดอายุ';     
            }     
          
            return [     
                'isValid' => empty($errors),     
                'errors' => $errors     
            ];     
        }     
public function exportToPDF(StudentCard $card): string     
{     
// ใช้ library เช่น TCPDF หรือ Dompdf     
// Implementation depends on chosen library     
return "PDF generated for student: " . $card->getStudent()->getStudentId();     
}     
}   