 <?php     
    // tests/StudentCardTest.php     
          
    use PHPUnit\Framework\TestCase;     
    use App\Models\Student;     
    use App\Models\StudentCard;     
    use App\Services\StudentCardService;     
          
    class StudentCardTest extends TestCase     
    {     
        private StudentCardService $service;     
          
        protected function setUp(): void     
        {     
            $this->service = new StudentCardService();     
        }     
          
        public function testCreateStudentCard()     
        {     
            $data = [     
                'firstName' => 'ปภัสสร',     
                'lastName' => 'แย้มชื่น',     
                'studentId' => '674230034',     
                'faculty' => 'วิทยาศาสตร์'     
            ];     
          
            $card = $this->service->createStudentCard($data);     
          
            $this->assertInstanceOf(StudentCard::class, $card);     
            $this->assertEquals('67123456', $card->getStudent()->getStudentId());     
        }     
          
        public function testCardValidation()     
        {     
            $data = [     
                'firstName' => 'ปภัสสร',     
                'lastName' => 'แย้มชื่น',     
                'studentId' => '674230034',     
                'faculty' => 'วิทยาศาสตร์'     
            ];     
          
            $card = $this->service->createStudentCard($data);     
            $validation = $this->service->validateCard($card);     
          
            $this->assertTrue($validation['isValid']);     
        }     
          
        public function testCardExpiration()     
        {     
            $data = [     
                'firstName' => 'ปภัสสร',     
                'lastName' => 'แย้มชื่น',     
                'studentId' => '674230034',     
'faculty' => 'วิทยาศาสตร์'     
];     
$card = $this->service->createStudentCard($data);     
$this->assertTrue($card->isValid());     
}     
}  