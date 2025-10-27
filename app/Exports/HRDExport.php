<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Collection;

class HRDExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    use Exportable;

    protected $keuangan;
    protected $type;
    protected $startDataRow = 9; // Data starts after header

    public function __construct($keuangan, $type = 'all')
    {
        $this->keuangan = $keuangan;
        $this->type = $type;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->loadTemplateHeader($sheet);
                $this->addLogo($sheet);

                // Setup page for A4 Portrait printing
                $sheet->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0)
                    ->setHorizontalCentered(true);
            },
            AfterSheet::class => function (AfterSheet $event) {
                $this->applyDataStyling($event->sheet->getDelegate());
            },
        ];
    }



    protected function loadTemplateHeader(Worksheet $sheet)
    {
        // Load the template file
        $templatePath = storage_path('app/templates/templatehrd.xlsx');
        $template = IOFactory::load($templatePath);
        $templateSheet = $template->getActiveSheet();

        // Copy header from template (rows 1-8)
        for ($row = 1; $row <= 8; $row++) {
            $maxColumn = $templateSheet->getHighestColumn();
            for ($col = 'A'; $col <= $maxColumn; $col++) {
                $cellValue = $templateSheet->getCell($col . $row)->getValue();
                $sheet->setCellValue($col . $row, $cellValue);
            }
        }

        // Set merged cells for header
        $sheet->mergeCells('C4:X4'); // Company name
        $sheet->mergeCells('C5:X5'); // Address
        $sheet->mergeCells('C6:X6'); // Report title
        $sheet->mergeCells('A7:B7'); // Period label
        $sheet->mergeCells('A8:B8'); // Code label

        // Update period and code
        if ($this->keuangan && $this->keuangan->periode) {
            $periode = $this->keuangan->periode;
            $tanggalMulai = $periode->tanggal_mulai->format('d-m-Y');
            $tanggalSelesai = $periode->tanggal_selesai->format('d-m-Y');
            $sheet->setCellValue('C7', ': ' . $tanggalMulai . ' - ' . $tanggalSelesai);
        }
        $sheet->setCellValue('C8', ': ' . ($this->keuangan->kode ?? '-'));

        // Apply header styling
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('C4:X6')->applyFromArray($headerStyle);
    }

    protected function addLogo(Worksheet $sheet)
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('storage/images/logo.png'));
        $drawing->setHeight(55);
        $drawing->setCoordinates('B4');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);
    }

    public function collection()
    {
        $data = [];
        $penggajians = $this->keuangan->penggajians;
        $no = 1;

        if (empty($penggajians)) {
            return new Collection();
        }

        foreach ($penggajians as $penggajian) {
            if (!$penggajian->karyawan || !$penggajian->karyawan->departemen) {
                continue;
            }

            // Get period dates from keuangan
            $periode = $this->keuangan->periode;
            $hariKerja = $periode ? $periode->tanggal_mulai->diffInDays($periode->tanggal_selesai) + 1 : 0;

            // Decode JSON details
            $detailTunjangan = json_decode($penggajian->detail_tunjangan, true) ?? [];
            $detailPotongan = json_decode($penggajian->detail_potongan, true) ?? [];
            $detailDepartemen = json_decode($penggajian->detail_departemen, true) ?? [];

            // Get attendance data from dataAbsensi
            $hadir = $penggajian->dataAbsensi['hadir'] ?? 0;
            $izin = $penggajian->dataAbsensi['izin'] ?? 0;
            $cuti = ($penggajian->dataAbsensi['cuti'] ?? 0) + ($penggajian->dataAbsensi['izin_cuti'] ?? 0);
            $tidakHadir = $penggajian->dataAbsensi['tidak_hadir'] ?? 0;
            $totalKeterlambatan = $penggajian->dataAbsensi['total_keterlambatan'] ?? 0;
            $totalPulangAwal = $penggajian->dataAbsensi['total_pulang_awal'] ?? 0;
            $lemburHariBiasa = $penggajian->dataAbsensi['lembur_hari_biasa'] ?? 0;
            $lemburHariLibur = $penggajian->dataAbsensi['lembur_hari_libur'] ?? 0;

            // Calculate allowances and deductions
            $tunjanganKehadiran = collect($detailTunjangan)->firstWhere('nama', 'Tunjangan Kehadiran')['nominal'] ?? 0;
            $tunjanganLembur = collect($detailTunjangan)->firstWhere('nama', 'Tunjangan Lembur')['nominal'] ?? 0;
            $potonganKetidakhadiran = collect($detailPotongan)->firstWhere('nama', 'Potongan Ketidakhadiran')['nominal'] ?? 0;
            $potonganKeterlambatan = collect($detailPotongan)->firstWhere('nama', 'Potongan Keterlambatan')['nominal'] ?? 0;
            $potonganBPJS = collect($detailPotongan)->firstWhere('nama', 'Potongan BPJS')['nominal'] ?? 0;

            $data[] = [
                $no++,
                $penggajian->karyawan->departemen->name_departemen,
                $penggajian->karyawan->name,
                $detailDepartemen['bagian'] ?? '-',
                $detailDepartemen['jabatan'] ?? '-',
                $hariKerja,
                $hadir,
                $izin,
                $cuti,
                $tidakHadir,
                $totalKeterlambatan,
                $totalPulangAwal,
                $lemburHariBiasa,
                $lemburHariLibur,
                $penggajian->gaji_pokok,
                $penggajian->karyawan->jabatan->tunjangan_jabatan ?? 0,
                $penggajian->karyawan->profesi->tunjangan_profesi ?? 0,
                $tunjanganKehadiran,
                $tunjanganLembur,
                $potonganKetidakhadiran,
                $potonganKeterlambatan,
                $potonganBPJS,
                $penggajian->potongan,
                $penggajian->gaji_bersih
            ];
        }

        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            'NO',
            'DEPARTEMEN',
            'NAMA KARYAWAN',
            'BAGIAN',
            'JABATAN',
            'HARI KERJA',
            'HADIR',
            'IZIN',
            'CUTI',
            'TIDAK HADIR',
            'KETERLAMBATAN',
            'PULANG AWAL',
            'JAM LEMBUR BIASA',
            'JAM LEMBUR LIBUR',
            'GAJI POKOK',
            'TUNJANGAN JABATAN',
            'TUNJANGAN PROFESI',
            'TUNJANGAN KEHADIRAN',
            'TUNJANGAN LEMBUR',
            'POTONGAN KETIDAKHADIRAN',
            'POTONGAN KETERLAMBATAN',
            'POTONGAN BPJS',
            'TOTAL POTONGAN',
            'GAJI BERSIH'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'D:H' => ['numberFormat' => ['formatCode' => '#,##0']],
        ];
    }

    protected function applyDataStyling(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = 'X';

        // Apply borders to data section
        $dataRange = 'A' . $this->startDataRow . ':' . $lastColumn . $lastRow;
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Calculate department totals and grand total
        $departments = [];
        $grandTotal = array_fill(0, 24, 0); // Initialize array for grand total

        for ($row = $this->startDataRow; $row <= $lastRow; $row++) {
            $department = $sheet->getCell('B' . $row)->getValue();

            if (!isset($departments[$department])) {
                $departments[$department] = array_fill(0, 24, 0);
            }

            // Sum each column for department totals
            for ($col = 0; $col < 24; $col++) {
                $cellValue = $sheet->getCellByColumnAndRow($col + 1, $row)->getValue();
                // Convert to float if numeric, otherwise use 0
                $numericValue = is_numeric($cellValue) ? (float)$cellValue : 0;
                $departments[$department][$col] += $numericValue;
                $grandTotal[$col] += $numericValue;
            }
        }

        // Add department totals
        $currentRow = $lastRow + 1;
        foreach ($departments as $department => $totals) {
            $sheet->insertNewRowBefore($currentRow, 1);

            // Merge cells for department name and align right
            $sheet->mergeCells('A' . $currentRow . ':N' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Total ' . $department);
            $sheet->getStyle('A' . $currentRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Add department totals (starting from column O)
            for ($col = 14; $col < 24; $col++) {
                $sheet->setCellValueByColumnAndRow($col + 1, $currentRow, $totals[$col]);
            }

            // Style department totals
            $sheet->getStyle('A' . $currentRow . ':' . $lastColumn . $currentRow)
                ->getFont()->setBold(true);
            $sheet->getStyle('O' . $currentRow . ':' . $lastColumn . $currentRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('O' . $currentRow . ':' . $lastColumn . $currentRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            $currentRow++;
        }

        // Add grand total with similar styling
        $sheet->insertNewRowBefore($currentRow, 1);
        $sheet->mergeCells('A' . $currentRow . ':N' . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'Grand Total');
        $sheet->getStyle('A' . $currentRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Add grand totals (starting from column O)
        for ($col = 14; $col < 24; $col++) {
            $sheet->setCellValueByColumnAndRow($col + 1, $currentRow, $grandTotal[$col]);
        }

        // Style grand total
        $sheet->getStyle('A' . $currentRow . ':' . $lastColumn . $currentRow)
            ->getFont()->setBold(true);
        $sheet->getStyle('O' . $currentRow . ':' . $lastColumn . $currentRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('O' . $currentRow . ':' . $lastColumn . $currentRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Style grand total
        $sheet->getStyle('A' . $currentRow . ':' . $lastColumn . $currentRow)
            ->getBorders()->getTop()->setBorderStyle(Border::BORDER_THICK);

        // Center align text and number columns (A-N)
        $sheet->getStyle('A' . $this->startDataRow . ':N' . $lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Right align and format monetary columns (O-X)
        $sheet->getStyle('O' . $this->startDataRow . ':' . $lastColumn . $lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Format numbers with thousand separator
        $sheet->getStyle('O' . $this->startDataRow . ':' . $lastColumn . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Set font for all data
        $sheet->getStyle($dataRange)->getFont()
            ->setName('Calibri')
            ->setSize(11);
    }
}
