<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Services\StudentRecordsExportService;
use Livewire\Livewire;
use App\Livewire\Admin\Student\StudentRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use ZipArchive;

class StudentRecordsExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (!is_dir(base_path('tests/exports'))) {
            mkdir(base_path('tests/exports'), 0755, true);
        }
    }

    public function test_modal_has_document_title_and_export_options()
    {
        $admin = Admin::create([
            'admin_id'  => 'ADM-001',
            'full_name' => 'Carl Ashley Administrator',
            'email'     => 'admin@agrico.test',
            'password'  => bcrypt('password'),
        ]);

        Livewire::actingAs($admin, 'admin')
            ->test(StudentRecord::class)
            ->call('openSectionRecordsModal')
            ->assertSet('documentTitle', 'GENERAL ACTION PLAN FOR ACADEMIC YEAR 2025-2026')
            ->assertSee('Document Title')
            ->assertSee('Print / Save Copy')
            ->assertSee('MS Word (.docx)')
            ->assertSee('MS Excel (.xlsx)');
    }

    public function test_docx_export_is_landscape_and_contains_header_title_and_footer()
    {
        $admin = Admin::create([
            'admin_id'  => 'ADM-002',
            'full_name' => 'Prof. Maria Santos',
            'email'     => 'maria@agrico.test',
            'password'  => bcrypt('password'),
        ]);

        $student1 = User::create([
            'user_id' => 'STD-001',
            'student_id' => '2026-0001',
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@student.agrico.test',
            'password' => bcrypt('password'),
            'role' => 'student',
            'section' => 'BSIT-4A',
            'status' => 'Active',
            'student_record_status' => 'Active',
            'last_admin_id' => 'ADM-002',
        ]);

        $student2 = User::create([
            'user_id' => 'STD-002',
            'student_id' => '2026-0002',
            'name' => 'Maria Clara de los Santos',
            'email' => 'clara@student.agrico.test',
            'password' => bcrypt('password'),
            'role' => 'student',
            'section' => 'BSIT-4A',
            'status' => 'Active',
            'student_record_status' => 'Active',
            'last_admin_id' => 'ADM-002',
        ]);

        $students = collect([$student1, $student2]);
        $cards = [
            [
                'id' => 1,
                'activity_name' => 'Activity #1 - Field Soil Test',
                'scan_name' => 'Scan Soil Activity',
                'pin_name' => 'Pin Location Activity',
                'date' => '2026-10-17',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'show_scans' => true,
                'show_pins' => true,
            ]
        ];

        $service = new StudentRecordsExportService();
        $response = $service->exportDocx(
            $students,
            $cards,
            'BSIT-4A',
            'GENERAL ACTION PLAN FOR ACADEMIC YEAR 2025-2026',
            'Prof. Maria Santos'
        );

        $outputPath = base_path('tests/exports/sample_student_records.docx');
        ob_start();
        $response->sendContent();
        $binary = ob_get_clean();
        file_put_contents($outputPath, $binary);

        $this->assertFileExists($outputPath);
        $this->assertGreaterThan(1000, filesize($outputPath));

        // Inspect Word2007 OpenXML contents inside zip
        $zip = new ZipArchive();
        $opened = $zip->open($outputPath);
        $this->assertTrue($opened === true, 'Failed to open exported docx as ZIP');

        $docXml = $zip->getFromName('word/document.xml');
        $footerXml = $zip->getFromName('word/footer1.xml') ?: $zip->getFromName('word/footer2.xml') ?: '';
        $zip->close();

        // Assert landscape orientation
        $this->assertStringContainsString('w:orient="landscape"', $docXml, 'Docx must be landscape oriented');

        // Assert CSU Header & Republic
        $this->assertStringContainsString('REPUBLIC OF THE PHILIPPINES', $docXml, 'Docx must contain Republic of the Philippines');
        $this->assertStringContainsString('CAGAYAN STATE UNIVERSITY Lal-lo Campus', $docXml, 'Docx must contain Campus Title');
        $this->assertStringContainsString('0945-0898-648', $docXml, 'Docx must contain Contact Phone');
        $this->assertStringContainsString('cicspadayaperformingartsguild@gmail.com', $docXml, 'Docx must contain Contact Email');

        // Assert Document Title
        $this->assertStringContainsString('GENERAL ACTION PLAN FOR ACADEMIC YEAR 2025-2026', $docXml, 'Docx must contain Document Title');

        // Assert Student Data
        $this->assertStringContainsString('Juan Dela Cruz', $docXml, 'Docx must contain student name Juan Dela Cruz');
        $this->assertStringContainsString('Maria Clara de los Santos', $docXml, 'Docx must contain student name Maria Clara');
        $this->assertStringContainsString('BSIT-4A', $docXml, 'Docx must contain student section');

        // Assert Footer Admin Name & Centered Date
        $combinedXml = $docXml . $footerXml;
        $this->assertStringContainsString('Prof. Maria Santos', $combinedXml, 'Docx must contain Admin full name in footer');
        $this->assertStringContainsString(date('F j, Y'), $combinedXml, 'Docx must contain Date in footer');
    }

    public function test_xlsx_export_is_landscape_and_contains_header_title_and_footer()
    {
        $student = User::create([
            'user_id' => 'STD-003',
            'student_id' => '2026-0003',
            'name' => 'Jose Rizal',
            'email' => 'jose@student.agrico.test',
            'password' => bcrypt('password'),
            'role' => 'student',
            'section' => 'BSAGRI-3B',
            'status' => 'Active',
            'student_record_status' => 'Active',
            'last_admin_id' => 'ADM-001',
        ]);

        $students = collect([$student]);
        $cards = [
            [
                'id' => 1,
                'activity_name' => 'Activity #1',
                'scan_name' => 'Scan Soil Activity',
                'pin_name' => 'Pin Location Activity',
                'date' => '2026-10-17',
                'start_time' => '',
                'end_time' => '',
                'show_scans' => true,
                'show_pins' => true,
            ]
        ];

        $service = new StudentRecordsExportService();
        $response = $service->exportXlsx(
            $students,
            $cards,
            'BSAGRI-3B',
            'SPECIAL RESEARCH FIELD LAB',
            'Dr. Josefa Llanes Escoda'
        );

        $outputPath = base_path('tests/exports/sample_student_records.xlsx');
        ob_start();
        $response->sendContent();
        $binary = ob_get_clean();
        file_put_contents($outputPath, $binary);

        $this->assertFileExists($outputPath);
        $this->assertGreaterThan(1000, filesize($outputPath));

        // Load spreadsheet to verify properties
        $spreadsheet = SpreadsheetIOFactory::load($outputPath);
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Assert Landscape orientation
        $orientation = $sheet->getPageSetup()->getOrientation();
        $this->assertEquals(PageSetup::ORIENTATION_LANDSCAPE, $orientation, 'Excel sheet must be landscape');

        // 2. Assert Header text
        $this->assertStringContainsString('REPUBLIC OF THE PHILIPPINES', $sheet->getCell('A1')->getValue());
        $this->assertEquals('CAGAYAN STATE UNIVERSITY Lal-lo Campus', $sheet->getCell('A2')->getValue());

        // 3. Assert Document Title
        $this->assertEquals('SPECIAL RESEARCH FIELD LAB', $sheet->getCell('A3')->getValue());

        // 4. Assert Footer contains admin name and date
        $footerText = $sheet->getHeaderFooter()->getOddFooter();
        $this->assertStringContainsString('Dr. Josefa Llanes Escoda', $footerText, 'Excel footer must contain Admin name');
        $this->assertStringContainsString(date('F j, Y'), $footerText, 'Excel footer must contain Date');

        // 5. Assert Table contains student
        $foundStudent = false;
        for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
            if ($sheet->getCell("B{$row}")->getValue() === 'Jose Rizal') {
                $foundStudent = true;
                break;
            }
        }
        $this->assertTrue($foundStudent, 'Excel must contain student Jose Rizal');
    }

    public function test_empty_section_generates_valid_exports_without_crashing()
    {
        $service = new StudentRecordsExportService();

        // Empty students
        $emptyStudents = collect();
        $cards = [
            [
                'id' => 1,
                'activity_name' => 'Activity #1',
                'scan_name' => 'Scan',
                'pin_name' => 'Pin',
                'date' => '2026-10-17',
                'start_time' => '',
                'end_time' => '',
                'show_scans' => true,
                'show_pins' => false,
            ]
        ];

        // Docx
        $docxResponse = $service->exportDocx(
            $emptyStudents,
            $cards,
            'EmptySection',
            '', // empty title
            'Admin Tester'
        );
        $docxPath = base_path('tests/exports/empty_section_records.docx');
        ob_start();
        $docxResponse->sendContent();
        file_put_contents($docxPath, ob_get_clean());
        $this->assertFileExists($docxPath);

        // Xlsx
        $xlsxResponse = $service->exportXlsx(
            $emptyStudents,
            $cards,
            'EmptySection',
            '', // empty title
            'Admin Tester'
        );
        $xlsxPath = base_path('tests/exports/empty_section_records.xlsx');
        ob_start();
        $xlsxResponse->sendContent();
        file_put_contents($xlsxPath, ob_get_clean());
        $this->assertFileExists($xlsxPath);
    }
}
