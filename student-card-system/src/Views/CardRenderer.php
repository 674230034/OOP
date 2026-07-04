<?php
// src/Views/CardRenderer.php
namespace App\Views;
use App\Models\StudentCard;
class CardRenderer
{
    private StudentCard $card;
    public function __construct(StudentCard $card)
    {
        $this->card = $card;
    }
    public function render(): string
    {
        $student = $this->card->getStudent();
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="th">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width,
initial-scale=1.0">
            <title>บัตรนักศึกษา - <?= htmlspecialchars($student->getFullName()) ?></title>
            <style>
                <?= $this->getStyles() ?>
            </style>
        </head>

        <body>
            <div class="card-container">
                <!-- ด้านหน้าบัตร -->
                <div class="card-front">
                    <div class="card-header">
                        <div class="university-logo">
                            <img src="assets/logo.png" alt="University Logo">
                        </div>
                        <div class="university-name">

                            <div class="thai-
text">มหาวิทยาลัยราชภัฏนครปฐม</div>

                            <div class="english-text">NAKORN PATHOM
                                RAJBHAT UNIVERSITY</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="photo-section">
                            <img src="<?=
                                htmlspecialchars($student->getPhoto()) ?>" alt="Student Photo" class="student-photo">

                        </div>
                        <div class="info-section">
                            <div class="info-row">
                                <span class="label">ชื่อ-สกุล</span>
                                <span class="separator">:</span>
                                <span class="value">
                                    <?=
                                        htmlspecialchars($student->getFullName()) ?>
                                </span>
                            </div>

                            <div class="info-row">

                                <span class="label">รหัสนักศึกษา</span>
                                <span class="separator">:</span>

                                <span class="value">
                                    <?=
                                        htmlspecialchars($student->getStudentId()) ?>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="label">สาขาวิชา</span>
                                <span class="separator">:</span>
                                <span class="value">
                                    <?=
                                        htmlspecialchars($student->getFaculty()) ?>
                                </span>
                            </div>
                        </div>
                        <div class="chip-icon">
                            <div class="chip"></div>
                        </div>
                    </div>
                    <div class="card-number">
                        <?= $this->formatCardNumber($this->card->getCardNumber()) ?>
                    </div>
                    <div class="card-footer">
                        <!-- <div class="student-name-en">
                            <?= htmlspecialchars($student->getFullNameEN()) ?>
                        </div> -->
                        <div class="student-name-en">
                            PAPHATSORN YAMCHUEN
                        </div>

                        <div class="valid-thru">
                            <span>VALID THRU</span>
                            <span><?= $this->card->getExpireDateThai() ?></span>
                        </div>
                        <div class="card-brand">
                            <span class="visa-logo">VISA</span>
                        </div>

                        <div class="card-symbol">

                            <div class="symbol-circle"></div>
                        </div>
                    </div>
                    <div class="card-type-thai">
                        บัตรประจำตัวนักศึกษา
                    </div>
                </div>
                <!-- ด้านหลังบัตร -->
                <div class="card-back">
                    <div class="magnetic-strip"></div>
                    <div class="signature-panel">
                        <div class="signature-line"></div>

                        <div class="signature-text">ลายมือชื่อผู้ถือบัตร</div>

                    </div>

                    <div class="barcode">

                        <!-- <img src="assets/barcode-<?= $student->getStudentId() ?>.png"> -->
                        <img src="assets/barcode.png" alt="รูปบาร์โค้ด">
                        <div class="barcode-number"><?= $student->getStudentId() ?></div>
                    </div>

                    <div class="back-decoration">

                        <div class="flower-pattern"></div>
                    </div>
                </div>
            </div>
        </body>

        </html>
        <?php
        return ob_get_clean();
    }
    private function formatCardNumber(string $number): string
    {
        return chunk_split($number, 4, ' ');
    }
    private function getStyles(): string
    {
        return <<<'CSS'
* {
margin: 0;
padding: 0;
box-sizing: border-box;

}
body {
font-family: 'Sarabun', 'Arial', sans-serif;
background: linear-gradient(135deg, #667eea 0%,
#764ba2 100%);
min-height: 100vh;
display: flex;
justify-content: center;
align-items: center;
padding: 20px;

}
.card-container {
display: flex;
flex-direction: column;
gap: 40px;
/* perspective: 1000px; */
}
.card-front, 
.card-back {
width: 540px;
height: 340px;
border-radius: 15px;
overflow: hidden;
box-shadow: 0 20px 60px rgba(0,0,0,0.3);
background: linear-gradient(135deg, #f5f7fa 0%,
#c3cfe2 100%);
position: relative;
}
.card-front {
background: linear-gradient(135deg, #ff9a9e 0%,
#fecfef 50%, #fecfef 100%);
}
.card-header {
background: linear-gradient(90deg, #c0392b 0%,
#e74c3c 50%, #ecf0f1 100%);
padding: 12px 20px;
display: flex;
align-items: center;
gap: 15px;
color: white;
height: 70px;
}
.university-logo img {
width: 50px;
height: 50px
border-radius: 50%;

}
.university-name {
flex: 1;
}
.university-name .thai-text {
font-size: 18px;
font-weight: bold;

}
.university-name .english-text {
font-size: 12px;
margin-top: 3px;

}
.card-body {
padding: 15px 25px 5px 25px;
display: flex;
gap: 20px;
align-items: flex-start;
position: relative;
/* margin-top: 10px; */

}
.photo-section img {
width: 100px;
height: 130px;
object-fit: cover;
border: 3px solid white;
border-radius: 8px;
box-shadow: 0 4px 8px rgba(0,0,0,0.2);

}
.info-section {
flex: 1;
font-size: 14px;
margin-top: 8px;
padding-right: 60px;
}
.info-row {
margin-bottom: 10px;
display: flex;
align-items: center;

}
.info-row .label {
font-weight: bold;
width: 85px;
color: #2c3e50;

}
.info-row .separator {
margin: 0 5px;
color: #7f8c8d;

}
.info-row .value {
flex: 1;
color: #34495e;
font-weight: bold;

}
.chip {
width: 48px;
height: 36px;
background: linear-gradient(135deg, #f1c40f 0%,

#f39c12 100%);
border-radius: 5px;
position: absolute;
right: 20px;
top: 23px;
border: 1px solid #d68910;

}
.card-number {
    font-family: 'Courier New', Courier, monospace; /* ใช้ฟอนต์แบบ Monospace เพื่อให้ตัวเลขกว้างเท่ากันทุกตัว */
    font-size: 24px;          /* ปรับขนาดตามความเหมาะสม */
    font-weight: bold;
    color: #1a365d;           /* สีน้ำเงินเข้มตามหน้าบัตร */
    
    letter-spacing: 2px;      /* ระยะห่างระหว่างตัวเลขแต่ละตัว */
    word-spacing: 12px;       /* ระยะห่างระหว่างกลุ่มตัวเลข (ตรงที่เคาะ Spacebar) */
    
    text-align: center;
}
.card-footer{
    position:absolute;
    top: 260px;
    left: 0;
    width: 100%;
    padding: 0 30px;

    display:flex;
    justify-content:space-between;
    align-items:center;
}
.student-name-en {
font-family: 'Courier New', monospace;
    font-size: 14px;
    color: #555555;       /* สีเทา/ดำ ตามหน้าบัตร */
    text-transform: uppercase; /* บังคับให้เป็นตัวพิมพ์ใหญ่ทั้งหมด */

}
.valid-thru {
text-align: center;
font-size: 8px;

}
.valid-thru span:first-child {
display: block;
color: #7f8c8d;

}
.valid-thru span:last-child {
font-weight: bold;
color: #2c3e50;
font-size: 14px;

}
.visa-logo {
font-size: 25px;
font-weight: bold;
color: #1a1f71;
font-style: italic;
}
.card-symbol {
width: 32px;
height: 32px;

}
.symbol-circle {
width: 100%;
height: 100%;

border: 3.5px solid #e74c3c;
border-radius: 50%;
position: relative;

}
.symbol-circle::before {
content: '';
position: absolute;
top: 50%;
left: 50%;
transform: translate(-50%, -50%);

width: 16px;
height: 16px;
border: 3.5px solid #c0392b;
border-radius: 50%;

}
.card-type-thai{
    position:absolute;
    bottom:0;
    left:0;
    width:100%;

    background:linear-gradient(90deg,#c0392b,#e74c3c);
    color:white;
    text-align:center;
    padding:8px;
    font-weight:bold;
    font-size: 14px;
    letter-spacing: 1px;
    height: 38px;

}
.card-back {
    margin-top: 20px;

background: linear-gradient(135deg, #ffecd2 0%,

#fcb69f 100%);
}
.magnetic-strip {
height: 50px;
background: #000;
margin-top: 20px;

}
.signature-panel {
margin: 20px;
padding: 15px;
background: white;
border-radius: 5px;

}
.signature-line {
border-bottom: 2px dashed #bdc3c7;
height: 40px;
margin-bottom: 5px;
}
.signature-text {
text-align: right;
font-size: 12px;
color: #7f8c8d;

}
.barcode {
text-align: center;
margin: 20px;
padding: 10px;
background: white;
border-radius: 5px;

}
.barcode-number {
margin-top: 5px;

font-family: 'Courier New', monospace;
font-size: 14px;

}
.flower-pattern {
position: absolute;
bottom: 10px;
left: 10px;
width: 80px;
height: 80px;
background: radial-gradient(circle, #e74c3c 20%,

transparent 20%),
radial-gradient(circle, #e74c3c 20%,
transparent 20%);
background-size: 30px 30px;
opacity: 0.3;
}
@media print {
    body {
background: white;
}
.card-front, .card-back {
box-shadow: none;
page-break-inside: avoid;

}
}
CSS;
    }
}