<?php     
    // src/Models/Student.php     
          
    namespace App\Models;     
          
    class Student     
    {     
        private string $id;     
        private string $firstName;     
        private string $lastName;     
        private string $studentId;     
        private string $faculty;     
        private string $photo;     
        private string $university;     
          
        public function __construct(     
            string $id,     
            string $firstName,     
            string $lastName,     
            string $studentId,     
            string $faculty,     
            string $photo = 'default.jpg',     
            string $university = 'NAKORN PATHOM RAJBHAT UNIVERSITY'     
        ) {     
            $this->id = $id;     
            $this->firstName = $firstName;     
            $this->lastName = $lastName;     
            $this->studentId = $studentId;     
            $this->faculty = $faculty;     
            $this->photo = $photo;     
            $this->university = $university;     
        }     
          
        // Getters     
        public function getId(): string     
        {     
            return $this->id;     
        }     
          
        public function getFirstName(): string     
        {     
            return $this->firstName;     
        }     
          
        public function getLastName(): string     
        {     
            return $this->lastName;     
        }     
          
        public function getFullName(): string     
        {     
            return "{$this->firstName} {$this->lastName}";     
        }     
          
        public function getFullNameEN(): string     
        {     
            return strtoupper("{$this->firstName} {$this->lastName}");     
        }     
          
        public function getStudentId(): string     
        {     
            return $this->studentId;     
        }     
          
        public function getFaculty(): string     
        {     
            return $this->faculty;     
        }     
          
        public function getPhoto(): string     
        {     
            return $this->photo;     
        }     
          
        public function getUniversity(): string     
        {     
            return $this->university;     
        }     
          
        // Convert to array     
        public function toArray(): array     
        {     
            return [     
                'id' => $this->id,     
                'firstName' => $this->firstName,     
                'lastName' => $this->lastName,     
                'studentId' => $this->studentId,     
                'faculty' => $this->faculty,     
                'photo' => $this->photo,     
                'university' => $this->university     
            ];     
        }     
    }   