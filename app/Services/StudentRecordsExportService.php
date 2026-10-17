<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentRecordsExportService
{
    /**
     * Build clean safe filename.
     */
    public static function buildFilename(string $section, string $extension): string
    {
        $cleanSection = trim($section);
        if ($cleanSection === '' || strcasecmp($cleanSection, 'all') === 0) {
            $cleanSection = 'All';
        } else {
            // Remove invalid filename characters
            $cleanSection = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $cleanSection);
        }

        $date = date('Y-m-d');
        return "StudentRecords_{$cleanSection}_{$date}.{$extension}";
    }

    /**
     * Generate and stream MS Word (.docx) export in landscape.
     */
    public function exportDocx($students, array $cards, string $sectionFilter, string $documentTitle, string $adminName): StreamedResponse
    {
        $phpWord = new PhpWord();

        // Default document styling (Black & White)
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(9);

        // Section setup: Landscape A4 with 0.5 inch margins
        $section = $phpWord->addSection([
            'orientation'  => 'landscape',
            'pageSizeW'    => Converter::inchToTwip(11.69), // A4 landscape width
            'pageSizeH'    => Converter::inchToTwip(8.27),  // A4 landscape height
            'marginTop'    => Converter::inchToTwip(0.5),
            'marginBottom' => Converter::inchToTwip(0.5),
            'marginLeft'   => Converter::inchToTwip(0.5),
            'marginRight'  => Converter::inchToTwip(0.5),
        ]);

        // Total usable printable width ~15390 twips (10.69 inches * 1440)
        $usableWidthTwips = 15390;

        // TOP 3-COLUMN HEADER TABLE (Left: Republic & CSU info, Center: Logo, Right: Contact info)
        $logoPath = public_path('images/csu-logo.png');
        $hasLogo = file_exists($logoPath);

        $headerTable = $section->addTable([
            'alignment' => JcTable::CENTER,
            'cellMargin' => 30,
        ]);
        $headerRow = $headerTable->addRow();

        // 1. Left Column: Republic & CSU Addresses
        $leftCellWidth = Converter::inchToTwip(4.2);
        $leftCell = $headerRow->addCell($leftCellWidth, ['valign' => 'center']);
        $leftCell->addText('REPUBLIC OF THE PHILIPPINES', [
            'name'  => 'Arial',
            'size'  => 7.5,
            'bold'  => true,
            'color' => '000000',
        ], ['spaceAfter' => 10]);
        $leftCell->addText('CAGAYAN STATE UNIVERSITY', [
            'name'  => 'Arial',
            'size'  => 9,
            'bold'  => true,
            'color' => '000000',
        ], ['spaceAfter' => 10]);
        $leftCell->addText("Andrews Campus, Tuguegarao City, Cagayan (Central Office)\nCSU Lal-lo Campus, Sta. Maria, Lal-lo, Cagayan (Sub Office)", [
            'name'  => 'Arial',
            'size'  => 7,
            'color' => '000000',
        ], ['spaceAfter' => 0]);

        // 2. Center Column: CSU Logo
        $centerCellWidth = Converter::inchToTwip(2.29);
        $centerCell = $headerRow->addCell($centerCellWidth, ['valign' => 'center']);
        if ($hasLogo) {
            try {
                $centerCell->addImage($logoPath, [
                    'height'          => 48,
                    'alignment'       => Jc::CENTER,
                    'keepAspectRatio' => true,
                ]);
            } catch (\Throwable $e) {}
        }

        // 3. Right Column: Contact info
        $rightCellWidth = Converter::inchToTwip(4.2);
        $rightCell = $headerRow->addCell($rightCellWidth, ['valign' => 'center']);
        $p1 = $rightCell->addTextRun(['spaceAfter' => 10]);
        $p1->addText('Phone Number: ', ['name' => 'Arial', 'size' => 7, 'bold' => true, 'color' => '000000']);
        $p1->addText('0945-0898-648', ['name' => 'Arial', 'size' => 7, 'color' => '000000']);

        $p2 = $rightCell->addTextRun(['spaceAfter' => 10]);
        $p2->addText('Email Address: ', ['name' => 'Arial', 'size' => 7, 'bold' => true, 'color' => '000000']);
        $p2->addText('cicspadayaperformingartsguild@gmail.com', ['name' => 'Arial', 'size' => 7, 'color' => '000000']);

        $p3 = $rightCell->addTextRun(['spaceAfter' => 0]);
        $p3->addText('Facebook page: ', ['name' => 'Arial', 'size' => 7, 'bold' => true, 'color' => '000000']);
        $p3->addText('CICS Padaya Performing Arts Guild', ['name' => 'Arial', 'size' => 7, 'color' => '000000']);

        // CAMPUS MAIN TITLE
        $section->addText('CAGAYAN STATE UNIVERSITY Lal-lo Campus', [
            'name'  => 'Arial',
            'size'  => 11,
            'bold'  => true,
            'color' => '000000',
        ], [
            'alignment'   => Jc::CENTER,
            'spaceBefore' => 80,
            'spaceAfter'  => 40,
        ]);

        // HORIZONTAL DIVIDER LINE
        $lineTable = $section->addTable(['alignment' => JcTable::CENTER]);
        $lineRow = $lineTable->addRow(1);
        $lineRow->addCell($usableWidthTwips, [
            'borderBottomSize'  => 12, // 1.5 pt solid black line
            'borderBottomColor' => '000000',
        ]);

        // DOCUMENT TITLE (if provided)
        $trimmedTitle = trim($documentTitle);
        if ($trimmedTitle !== '') {
            $section->addText($trimmedTitle, [
                'name'  => 'Arial',
                'size'  => 10.5,
                'bold'  => true,
                'color' => '000000',
            ], [
                'alignment'   => Jc::CENTER,
                'spaceBefore' => 80,
                'spaceAfter'  => 120,
            ]);
        } else {
            $section->addTextBreak(1, ['size' => 6]);
        }

        // CALCULATE TABLE COLUMNS & SIZING
        // Base columns: #, Student Name, Section, Total = 4 fixed columns
        // Dynamic columns: for each card, 1 or 2 subcolumns
        $subcolumnCount = 0;
        foreach ($cards as $card) {
            if (!empty($card['show_scans'])) $subcolumnCount++;
            if (!empty($card['show_pins'])) $subcolumnCount++;
        }
        $totalColumnCount = 4 + $subcolumnCount;

        // Dynamic font sizing based on column count
        $fontSize = 9;
        if ($totalColumnCount > 8) {
            $fontSize = 8;
        }
        if ($totalColumnCount > 12) {
            $fontSize = 7;
        }

        // Allocate column twip widths
        $numWidth = 600;
        $nameWidth = min(3200, max(2200, (int)($usableWidthTwips * 0.22)));
        $sectionWidth = min(1800, max(1200, (int)($usableWidthTwips * 0.12)));
        $totalWidth = 1000;
        $remainingTwips = $usableWidthTwips - ($numWidth + $nameWidth + $sectionWidth + $totalWidth);
        $activityColWidth = $subcolumnCount > 0 ? (int) floor($remainingTwips / $subcolumnCount) : 1200;

        // TABLE STYLE: Strict black border on white background (no fills)
        $tableStyle = [
            'borderColor' => '000000',
            'borderSize'  => 6, // 0.75 pt thin black border
            'cellMarginTop' => 60,
            'cellMarginBottom' => 60,
            'cellMarginLeft' => 80,
            'cellMarginRight' => 80,
            'alignment'   => JcTable::CENTER,
        ];
        $table = $section->addTable($tableStyle);

        // CELL STYLES: Black only
        $cellBase = [
            'valign'      => 'center',
            'borderColor' => '000000',
            'borderSize'  => 6,
        ];
        $headerFont = [
            'name'  => 'Arial',
            'size'  => $fontSize,
            'bold'  => true,
            'color' => '000000',
        ];
        $headerSubFont = [
            'name'  => 'Arial',
            'size'  => max(6, $fontSize - 1),
            'bold'  => false,
            'color' => '000000',
        ];
        $bodyFont = [
            'name'  => 'Arial',
            'size'  => $fontSize,
            'bold'  => false,
            'color' => '000000',
        ];
        $bodyBoldFont = [
            'name'  => 'Arial',
            'size'  => $fontSize,
            'bold'  => true,
            'color' => '000000',
        ];

        // 1. TABLE HEADER ROW 1 (vMerge restart for fixed cols, gridSpan for activity groups)
        $row1 = $table->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);

        // # column
        $row1->addCell($numWidth, array_merge($cellBase, ['vMerge' => 'restart']))
            ->addText('#', $headerFont, ['alignment' => Jc::CENTER]);

        // Student Name column
        $row1->addCell($nameWidth, array_merge($cellBase, ['vMerge' => 'restart']))
            ->addText('Student Name', $headerFont, ['alignment' => Jc::LEFT]);

        // Section column
        $row1->addCell($sectionWidth, array_merge($cellBase, ['vMerge' => 'restart']))
            ->addText('Section', $headerFont, ['alignment' => Jc::CENTER]);

        // Activity Card Groups
        foreach ($cards as $i => $card) {
            $cols = (!empty($card['show_scans']) ? 1 : 0) + (!empty($card['show_pins']) ? 1 : 0);
            if ($cols === 0) continue;

            $groupWidth = $activityColWidth * $cols;

            $dateLabel = !empty($card['all_dates'])
                ? 'All Dates'
                : (!empty($card['date']) ? \Carbon\Carbon::parse($card['date'])->format('m/d/Y') : 'All Dates');

            $start = !empty($card['start_time']) ? \Carbon\Carbon::parse($card['start_time'])->format('g:i A') : null;
            $end   = !empty($card['end_time'])   ? \Carbon\Carbon::parse($card['end_time'])->format('g:i A')   : null;
            $timeLabel = $start && $end ? "{$start} – {$end}"
                       : ($start ? "From {$start}" : ($end ? "Until {$end}" : 'All Day'));

            $activityTitle = !empty($card['activity_name']) ? $card['activity_name'] : ('Activity #' . ($i + 1));
            $windowText = trim($dateLabel . ' · ' . $timeLabel, ' ·');

            $cell = $row1->addCell($groupWidth, array_merge($cellBase, ['gridSpan' => $cols]));
            $cell->addText($activityTitle, $headerFont, ['alignment' => Jc::CENTER, 'spaceAfter' => 20]);
            $cell->addText($windowText, $headerSubFont, ['alignment' => Jc::CENTER]);
        }

        // Total column
        $row1->addCell($totalWidth, array_merge($cellBase, ['vMerge' => 'restart']))
            ->addText('Total', $headerFont, ['alignment' => Jc::CENTER]);

        // 2. TABLE HEADER ROW 2 (vMerge continue for fixed cols, subcolumn names for cards)
        $row2 = $table->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);

        $row2->addCell($numWidth, array_merge($cellBase, ['vMerge' => 'continue']));
        $row2->addCell($nameWidth, array_merge($cellBase, ['vMerge' => 'continue']));
        $row2->addCell($sectionWidth, array_merge($cellBase, ['vMerge' => 'continue']));

        foreach ($cards as $i => $card) {
            if (!empty($card['show_scans'])) {
                $scanName = $card['scan_name'] ?? 'Scan';
                $row2->addCell($activityColWidth, $cellBase)
                    ->addText($scanName, $headerFont, ['alignment' => Jc::CENTER]);
            }
            if (!empty($card['show_pins'])) {
                $pinName = $card['pin_name'] ?? 'Pin Location';
                $row2->addCell($activityColWidth, $cellBase)
                    ->addText($pinName, $headerFont, ['alignment' => Jc::CENTER]);
            }
        }

        $row2->addCell($totalWidth, array_merge($cellBase, ['vMerge' => 'continue']));

        // 3. TABLE BODY ROWS
        if ($students->isEmpty()) {
            $emptyRow = $table->addRow(null, ['cantSplit' => true]);
            $emptyCell = $emptyRow->addCell($usableWidthTwips, array_merge($cellBase, ['gridSpan' => $totalColumnCount]));
            $emptyCell->addText('No students found.', $bodyFont, ['alignment' => Jc::CENTER]);
        } else {
            foreach ($students as $index => $student) {
                $row = $table->addRow(null, ['cantSplit' => true]);
                $rowTotal = 0;

                // Index #
                $row->addCell($numWidth, $cellBase)
                    ->addText((string)($index + 1), $bodyFont, ['alignment' => Jc::CENTER]);

                // Student Name
                $row->addCell($nameWidth, $cellBase)
                    ->addText($student->name ?? '', $bodyBoldFont, ['alignment' => Jc::LEFT]);

                // Section
                $row->addCell($sectionWidth, $cellBase)
                    ->addText($student->section ?? '', $bodyFont, ['alignment' => Jc::CENTER]);

                // Activity counts
                foreach ($cards as $i => $card) {
                    if (!empty($card['show_scans'])) {
                        $s = (int) ($student->{"card_{$i}_scans_count"} ?? 0);
                        $rowTotal += $s;
                        $row->addCell($activityColWidth, $cellBase)
                            ->addText((string)$s, $bodyFont, ['alignment' => Jc::CENTER]);
                    }
                    if (!empty($card['show_pins'])) {
                        $p = (int) ($student->{"card_{$i}_pins_count"} ?? 0);
                        $rowTotal += $p;
                        $row->addCell($activityColWidth, $cellBase)
                            ->addText((string)$p, $bodyFont, ['alignment' => Jc::CENTER]);
                    }
                }

                // Total
                $row->addCell($totalWidth, $cellBase)
                    ->addText((string)$rowTotal, $bodyBoldFont, ['alignment' => Jc::CENTER]);
            }
        }

        // FOOTER ON EVERY PAGE (Left: Prepared by, Center: Date)
        $footer = $section->addFooter();
        $footerTable = $footer->addTable([
            'alignment' => JcTable::CENTER,
            'cellMargin' => 20,
        ]);
        $footerRow = $footerTable->addRow();

        // Left: Prepared by
        $preparedCell = $footerRow->addCell(Converter::inchToTwip(5.34));
        $preparedCell->addText("Prepared by: {$adminName}", [
            'name'  => 'Arial',
            'size'  => 8.5,
            'color' => '000000',
        ], ['alignment' => Jc::LEFT]);

        // Center: Date
        $dateCell = $footerRow->addCell(Converter::inchToTwip(5.34));
        $dateCell->addText(date('F j, Y'), [
            'name'  => 'Arial',
            'size'  => 8.5,
            'color' => '000000',
        ], ['alignment' => Jc::CENTER]);

        // Stream download
        $filename = self::buildFilename($sectionFilter, 'docx');

        return response()->streamDownload(function () use ($phpWord) {
            $objWriter = WordIOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Generate and stream MS Excel (.xlsx) export in landscape.
     */
    public function exportXlsx($students, array $cards, string $sectionFilter, string $documentTitle, string $adminName): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Student Records');

        // Page setup: Landscape, A4, Fit to 1 page wide
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
        $pageSetup->setFitToPage(true);
        $pageSetup->setFitToWidth(1);
        $pageSetup->setFitToHeight(0); // Automatic height pages

        // Margins: 0.5 inch (0.5 in = 12.7mm)
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setRight(0.5);

        // Count total columns
        $subcolumnCount = 0;
        foreach ($cards as $card) {
            if (!empty($card['show_scans'])) $subcolumnCount++;
            if (!empty($card['show_pins'])) $subcolumnCount++;
        }
        $totalColumnCount = 4 + $subcolumnCount;
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumnCount);

        $currentRow = 1;

        // Column indices for 3-column header
        $leftEndCol = max(1, (int)floor($totalColumnCount * 0.4));
        $leftEndLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($leftEndCol);

        $midCol = min($totalColumnCount, $leftEndCol + 1);
        $midLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($midCol);

        $rightStartCol = min($totalColumnCount, $midCol + 1);
        $rightStartLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($rightStartCol);

        // 1. LEFT HEADER BLOCK: Republic & CSU Addresses
        $sheet->mergeCells("A1:{$leftEndLetter}1");
        $sheet->setCellValue("A1", "REPUBLIC OF THE PHILIPPINES\nCAGAYAN STATE UNIVERSITY\nAndrews Campus, Tuguegarao City, Cagayan (Central Office)\nCSU Lal-lo Campus, Sta. Maria, Lal-lo, Cagayan (Sub Office)");
        $sheet->getStyle("A1")->getFont()->setName('Arial')->setSize(7.5)->getColor()->setRGB('000000');
        $sheet->getStyle("A1")->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);

        // 2. CENTER: CSU LOGO
        $logoPath = public_path('images/csu-logo.png');
        if (file_exists($logoPath)) {
            try {
                $drawing = new Drawing();
                $drawing->setName('CSU Logo');
                $drawing->setDescription('CSU Logo');
                $drawing->setPath($logoPath);
                $drawing->setHeight(46);
                $drawing->setCoordinates("{$midLetter}1");
                $drawing->setOffsetX(8);
                $drawing->setOffsetY(4);
                $drawing->setWorksheet($sheet);
            } catch (\Throwable $e) {}
        }

        // 3. RIGHT HEADER BLOCK: Contact Info
        if ($rightStartCol <= $totalColumnCount) {
            $sheet->mergeCells("{$rightStartLetter}1:{$lastColLetter}1");
            $sheet->setCellValue("{$rightStartLetter}1", "Phone Number: 0945-0898-648\nEmail Address: cicspadayaperformingartsguild@gmail.com\nFacebook page: CICS Padaya Performing Arts Guild");
            $sheet->getStyle("{$rightStartLetter}1")->getFont()->setName('Arial')->setSize(7.5)->getColor()->setRGB('000000');
            $sheet->getStyle("{$rightStartLetter}1")->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
        }

        $sheet->getRowDimension(1)->setRowHeight(58);
        $currentRow = 2;

        // CAMPUS MAIN TITLE WITH DIVIDER LINE
        $sheet->mergeCells("A{$currentRow}:{$lastColLetter}{$currentRow}");
        $sheet->setCellValue("A{$currentRow}", 'CAGAYAN STATE UNIVERSITY Lal-lo Campus');
        $sheet->getStyle("A{$currentRow}")->getFont()->setName('Arial')->setSize(11)->setBold(true)->getColor()->setRGB('000000');
        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB('000000');
        $sheet->getRowDimension($currentRow)->setRowHeight(24);
        $currentRow++;

        // DOCUMENT TITLE ROW (if provided)
        $trimmedTitle = trim($documentTitle);
        if ($trimmedTitle !== '') {
            $sheet->mergeCells("A{$currentRow}:{$lastColLetter}{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", $trimmedTitle);
            $sheet->getStyle("A{$currentRow}")->getFont()->setName('Arial')->setSize(10.5)->setBold(true)->getColor()->setRGB('000000');
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension($currentRow)->setRowHeight(22);
            $currentRow++;
        }

        // Spacer row
        $sheet->getRowDimension($currentRow)->setRowHeight(6);
        $currentRow++;

        // TABLE HEADER: Row 1 & Row 2
        $tableHeaderRow1 = $currentRow;
        $tableHeaderRow2 = $currentRow + 1;

        // Repeat header row on every printed page
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($tableHeaderRow1, $tableHeaderRow2);

        // Fixed columns: #, Student Name, Section
        $sheet->mergeCells("A{$tableHeaderRow1}:A{$tableHeaderRow2}");
        $sheet->setCellValue("A{$tableHeaderRow1}", '#');

        $sheet->mergeCells("B{$tableHeaderRow1}:B{$tableHeaderRow2}");
        $sheet->setCellValue("B{$tableHeaderRow1}", 'Student Name');

        $sheet->mergeCells("C{$tableHeaderRow1}:C{$tableHeaderRow2}");
        $sheet->setCellValue("C{$tableHeaderRow1}", 'Section');

        $colIdx = 4; // Column D

        foreach ($cards as $i => $card) {
            $cols = (!empty($card['show_scans']) ? 1 : 0) + (!empty($card['show_pins']) ? 1 : 0);
            if ($cols === 0) continue;

            $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $endColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + $cols - 1);

            $dateLabel = !empty($card['all_dates'])
                ? 'All Dates'
                : (!empty($card['date']) ? \Carbon\Carbon::parse($card['date'])->format('m/d/Y') : 'All Dates');

            $start = !empty($card['start_time']) ? \Carbon\Carbon::parse($card['start_time'])->format('g:i A') : null;
            $end   = !empty($card['end_time'])   ? \Carbon\Carbon::parse($card['end_time'])->format('g:i A')   : null;
            $timeLabel = $start && $end ? "{$start} – {$end}"
                       : ($start ? "From {$start}" : ($end ? "Until {$end}" : 'All Day'));

            $activityTitle = !empty($card['activity_name']) ? $card['activity_name'] : ('Activity #' . ($i + 1));
            $windowText = trim($dateLabel . ' · ' . $timeLabel, ' ·');

            $sheet->mergeCells("{$startColLetter}{$tableHeaderRow1}:{$endColLetter}{$tableHeaderRow1}");
            $sheet->setCellValue("{$startColLetter}{$tableHeaderRow1}", "{$activityTitle}\n{$windowText}");

            if (!empty($card['show_scans'])) {
                $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue("{$cLetter}{$tableHeaderRow2}", $card['scan_name'] ?? 'Scan');
                $colIdx++;
            }
            if (!empty($card['show_pins'])) {
                $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue("{$cLetter}{$tableHeaderRow2}", $card['pin_name'] ?? 'Pin Location');
                $colIdx++;
            }
        }

        // Total Column
        $totalColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
        $sheet->mergeCells("{$totalColLetter}{$tableHeaderRow1}:{$totalColLetter}{$tableHeaderRow2}");
        $sheet->setCellValue("{$totalColLetter}{$tableHeaderRow1}", 'Total');

        // Apply styles to headers: Bold, Black text, White background (No Fill), Thin Black borders
        $headerRange = "A{$tableHeaderRow1}:{$lastColLetter}{$tableHeaderRow2}";
        $sheet->getStyle($headerRange)->getFont()->setName('Arial')->setSize(9.5)->setBold(true)->getColor()->setRGB('000000');
        $sheet->getStyle($headerRange)->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_NONE);

        $sheet->getRowDimension($tableHeaderRow1)->setRowHeight(34);
        $sheet->getRowDimension($tableHeaderRow2)->setRowHeight(22);

        $dataStartRow = $tableHeaderRow2 + 1;
        $currentRow = $dataStartRow;

        // TABLE DATA ROWS
        if ($students->isEmpty()) {
            $sheet->mergeCells("A{$currentRow}:{$lastColLetter}{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", 'No students found.');
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($currentRow)->setRowHeight(24);
            $currentRow++;
        } else {
            foreach ($students as $index => $student) {
                $sheet->setCellValue("A{$currentRow}", $index + 1);
                $sheet->setCellValue("B{$currentRow}", $student->name ?? '');
                $sheet->setCellValue("C{$currentRow}", $student->section ?? '');

                $cIdx = 4;
                $rowTotal = 0;

                foreach ($cards as $i => $card) {
                    if (!empty($card['show_scans'])) {
                        $s = (int) ($student->{"card_{$i}_scans_count"} ?? 0);
                        $rowTotal += $s;
                        $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx);
                        $sheet->setCellValueExplicit($cLetter . $currentRow, $s, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                        $cIdx++;
                    }
                    if (!empty($card['show_pins'])) {
                        $p = (int) ($student->{"card_{$i}_pins_count"} ?? 0);
                        $rowTotal += $p;
                        $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx);
                        $sheet->setCellValueExplicit($cLetter . $currentRow, $p, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                        $cIdx++;
                    }
                }

                // Total
                $totLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx);
                $sheet->setCellValueExplicit($totLetter . $currentRow, $rowTotal, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

                $sheet->getRowDimension($currentRow)->setRowHeight(20);
                $currentRow++;
            }
        }

        $dataEndRow = $currentRow - 1;

        // STYLE TABLE BODY: Black text, no fills, thin black borders
        $tableRange = "A{$tableHeaderRow1}:{$lastColLetter}{$dataEndRow}";
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
        $sheet->getStyle("A{$dataStartRow}:{$lastColLetter}{$dataEndRow}")->getFont()->setName('Arial')->setSize(9)->getColor()->setRGB('000000');

        // Alignments
        $sheet->getStyle("A{$dataStartRow}:A{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$dataStartRow}:B{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("C{$dataStartRow}:{$lastColLetter}{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Bold student names and row totals
        $sheet->getStyle("B{$dataStartRow}:B{$dataEndRow}")->getFont()->setBold(true);
        $sheet->getStyle("{$lastColLetter}{$dataStartRow}:{$lastColLetter}{$dataEndRow}")->getFont()->setBold(true);

        // Column Auto-sizing / Sensible Widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        for ($col = 4; $col <= $totalColumnCount; $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setWidth(14);
        }

        // FOOTER ON EVERY PAGE (Left: Admin Name, Center: Date)
        $todayStr = date('F j, Y');
        $sheet->getHeaderFooter()->setOddFooter("&LPrepared by: {$adminName}&C{$todayStr}&R");
        $sheet->getHeaderFooter()->setEvenFooter("&LPrepared by: {$adminName}&C{$todayStr}&R");

        $filename = self::buildFilename($sectionFilter, 'xlsx');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = SpreadsheetIOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
