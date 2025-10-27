<?php

namespace App\Exports;

// Import library yang diperlukan untuk ekspor Excel
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

// Import library untuk manipulasi Excel
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

// Import library Laravel dan helper
use Illuminate\Support\Collection;
use App\Helpers\Terbilang;

class KeuanganExport implements WithMultipleSheets
{
    // Menggunakan trait Exportable untuk kemudahan ekspor
    use Exportable;

    // Menyimpan data keuangan yang akan diekspor
    protected $keuangan;

    public function __construct($keuangan)
    {
        // Inisialisasi data keuangan saat objek dibuat
        $this->keuangan = $keuangan;
    }

    public function sheets(): array
    {
        // Mengembalikan array berisi sheet-sheet yang akan dibuat
        // Setiap sheet memiliki tipe data dan judul yang berbeda
        return [
            'Bulanan'  => new KeuanganSheet($this->keuangan, 'bulanan', 'LAPORAN GAJI KARYAWAN BULANAN'),
            'Borongan' => new KeuanganSheet($this->keuangan, 'borongan', 'LAPORAN GAJI KARYAWAN BORONGAN'),
            'Harian'   => new KeuanganSheet($this->keuangan, 'harian', 'LAPORAN GAJI KARYAWAN HARIAN'),
            'All'      => new KeuanganSheet($this->keuangan, 'all', 'LAPORAN GAJI KARYAWAN')
        ];
    }
}

class KeuanganSheet implements FromCollection, WithTitle, WithColumnWidths, WithEvents
{
    // Kelas untuk mengatur format dan isi setiap sheet

    protected $keuangan;    // Data keuangan yang akan ditampilkan
    protected $type;        // Tipe laporan (bulanan/borongan/harian/all)
    protected $title;       // Judul laporan
    protected $startDataRow = 9; // Baris awal untuk data (setelah header)

    public function __construct($keuangan, $type, $title)
    {
        $this->keuangan = $keuangan;
        $this->type = $type;
        $this->title = $title;
    }

    public function columnWidths(): array
    {
        // Mengatur lebar setiap kolom dalam excel
        return [
            'A' => 14,  // Kolom No
            'B' => 20,  // Kolom Uraian
            'C' => 33,  // Kolom Jumlah Total Upah
            'D' => 33,  // Kolom Potongan Kedisiplinan
            'E' => 20,  // Kolom Pengajuan
        ];
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

                // Set print area
                $sheet->getPageSetup()->setPrintArea('A1:E' . ($this->getDataCount() + 16));
            },
            AfterSheet::class => function (AfterSheet $event) {
                $this->applyDataStyling($event->sheet->getDelegate());
            },
        ];
    }

    protected function addLogo(Worksheet $sheet)
    {
        // Fungsi untuk menambahkan logo perusahaan ke dalam excel
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('storage/images/logo.png'));
        $drawing->setHeight(55);
        $drawing->setCoordinates('A2');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);
    }

    protected function loadTemplateHeader(Worksheet $sheet)
    {
        // Load the template file
        $templatePath = storage_path('app/templates/templatekeuangan.xlsx');
        $template = IOFactory::load($templatePath);
        $templateSheet = $template->getActiveSheet();

        // Copy header from template (rows 1-8)
        for ($row = 1; $row <= 8; $row++) {
            $maxColumn = $templateSheet->getHighestColumn();
            for ($col = 'A'; $col <= $maxColumn; $col++) {
                // Skip A2 cell (where we'll add the logo separately)
                if ($col === 'A' && $row === 2) {
                    continue;
                }

                $cellValue = $templateSheet->getCell($col . $row)->getValue();
                $sheet->setCellValue($col . $row, $cellValue);
            }
        }

        // Update dynamic content in the header with null checks
        $tanggalMulai = '';
        $tanggalSelesai = '';

        if ($this->keuangan && $this->keuangan->periode) {
            $periode = $this->keuangan->periode;
            $tanggalMulai = $periode->tanggal_mulai->format('d-m-Y');
            $tanggalSelesai = $periode->tanggal_selesai->format('d-m-Y');
        }

        // Update report title (row 4, column C) dan merge serta center
        $sheet->setCellValue('B4', $this->title);
        $sheet->mergeCells('B4:E4');  // Merge sel C4 sampai E4
        $sheet->getStyle('B4')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:E2')->getFont()->setBold(true);
        $sheet->getStyle('C4');

        // Update period (row 5, column C) dan merge serta center
        $sheet->setCellValue('C5', 'Gaji Bulanan ' . $tanggalMulai . ' - ' . $tanggalSelesai);
        $sheet->mergeCells('C5:E5');  // Merge sel C5 sampai E5

        // Update date range (row 6, column C)
        $sheet->setCellValue('C6', $tanggalMulai . ' s/d ' . $tanggalSelesai);
    }

    protected function applyDataStyling(Worksheet $sheet)
    {
        // Mendapatkan nomor baris terakhir setelah data ditambahkan
        $lastRow = $sheet->getHighestRow();

        // Menghitung posisi baris untuk setiap bagian laporan
        $dataCount = $this->getDataCount();
        $totalRowIndex = $this->startDataRow + $dataCount;      // Baris untuk total
        $terbilangRowIndex = $totalRowIndex + 1;                // Baris untuk teks terbilang
        $emptyRowIndex = $terbilangRowIndex + 1;                // Baris kosong setelah terbilang
        $signatureLabelRowIndex = $emptyRowIndex + 1;           // Baris untuk label tanda tangan
        $signatureSpaceRowIndex = $signatureLabelRowIndex + 1;  // Baris kosong untuk tanda tangan
        $signatureNameRowIndex = $signatureSpaceRowIndex + 1;   // Baris untuk nama penanda tangan
        $signatureTitleRowIndex = $signatureNameRowIndex + 1;   // Baris untuk jabatan penanda tangan

        // Add borders ONLY to header table (row 8) and data section (NOT signature section)
        $headerRange = 'A8:E8';
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Menerapkan border tipis HANYA ke bagian data (hingga baris total)
        $dataRange = 'A' . $this->startDataRow . ':E' . $totalRowIndex;
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Mengatur font Calibri ukuran 11 untuk semua sel data
        $allContentRange = 'A' . $this->startDataRow . ':E' . $lastRow;
        $sheet->getStyle($allContentRange)->getFont()
            ->setName('Calibri')
            ->setSize(11);

        // Format angka dengan pemisah ribuan (contoh: 1,000,000)
        $sheet->getStyle('C' . $this->startDataRow . ':E' . $totalRowIndex)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Rata kanan untuk semua kolom angka (kolom jumlah)
        $sheet->getStyle('C' . $this->startDataRow . ':E' . $lastRow)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Rata tengah untuk kolom nomor
        $sheet->getStyle('A' . $this->startDataRow . ':A' . $totalRowIndex)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Membuat baris total menjadi tebal
        $sheet->getStyle('A' . $totalRowIndex . ':E' . $totalRowIndex)
            ->getFont()->setBold(true);

        // Mengaktifkan text wrapping dan alignment untuk sel terbilang
        $sheet->getStyle('B' . $terbilangRowIndex)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('C' . $terbilangRowIndex)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_TOP);

        // Menggabungkan sel untuk header
        // Menggabungkan sel dan center alignment untuk nama perusahaan
        $sheet->mergeCells('B2:E2');  // Company name
        $sheet->getStyle('B2:E2')->getFont()->setBold(true);  // Membuat teks menjadi bold
        $sheet->getStyle('B2')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Menggabungkan sel dan center alignment untuk alamat
        $sheet->mergeCells('B3:E3');  // Address
        $sheet->getStyle('B3')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Menggabungkan sel untuk teks terbilang
        $sheet->mergeCells('C' . $terbilangRowIndex . ':E' . $terbilangRowIndex);

        // Mengatur tinggi baris khusus
        $sheet->getRowDimension($terbilangRowIndex)->setRowHeight(50);     // Baris lebih tinggi untuk teks terbilang
        $sheet->getRowDimension($signatureSpaceRowIndex)->setRowHeight(50); // Ruang untuk tanda tangan

        // Styling bagian tanda tangan
        // Rata tengah untuk label tanda tangan (Pemohon/Mengetahui)
        $sheet->getStyle('B' . $signatureLabelRowIndex)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $signatureLabelRowIndex)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Rata tengah untuk nama dan jabatan penanda tangan
        $sheet->getStyle('B' . $signatureNameRowIndex . ':B' . $signatureTitleRowIndex)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $signatureNameRowIndex . ':E' . $signatureTitleRowIndex)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Removed all border styling for signature section as requested
    }

    protected function getDataCount()
    {
        // Add null check for keuangan
        if (!$this->keuangan) {
            return 2; // Default to 2 rows if no data
        }

        $penggajians = $this->keuangan->penggajians;

        // If we have data, count the departments
        if (!empty($penggajians)) {
            // Filter by type if not 'all'
            if ($this->type !== 'all') {
                $penggajians = $penggajians->filter(function ($penggajian) {
                    return $penggajian->karyawan->statuskaryawan->value === ucfirst($this->type);
                });
            }

            // Count unique departments
            $departments = [];
            foreach ($penggajians as $penggajian) {
                if ($penggajian->karyawan && $penggajian->karyawan->departemen) {
                    $dept = $penggajian->karyawan->departemen->name_departemen;
                    $departments[$dept] = true;
                }
            }

            return count($departments) ?: 2; // Default to 2 if no departments found
        }

        return 2; // Default sample data has 2 rows
    }

    public function collection()
    {
        $summary = [];
        $grandTotal = 0;
        $totalPotongan = 0;
        $totalPengajuan = 0;

        // Initialize penggajians with null check
        $penggajians = $this->keuangan ? $this->keuangan->penggajians : null;

        // Default to sample data for testing/empty case
        $result = new Collection();

        // If we have data, process it
        if (!empty($penggajians)) {
            // Filter by type if not 'all'
            if ($this->type !== 'all') {
                $penggajians = $penggajians->filter(function ($penggajian) {
                    return $penggajian->karyawan->statuskaryawan->value === ucfirst($this->type);
                });
            }

            $no = 1;
            foreach ($penggajians as $penggajian) {
                if (!$penggajian->karyawan || !$penggajian->karyawan->departemen) {
                    continue;
                }

                $dept = $penggajian->karyawan->departemen->name_departemen;

                if (!isset($summary[$dept])) {
                    $summary[$dept] = [
                        'no' => $no++,
                        'uraian' => strtoupper($dept),
                        'jumlah_total_upah' => 0,
                        'potongan_kedisiplinan' => 0,
                        'pengajuan' => 0
                    ];
                }

                $detail_tunjangan = json_decode($penggajian->detail_tunjangan, true) ?? [];
                $detail_potongan = json_decode($penggajian->detail_potongan, true) ?? [];

                $total_tunjangan = array_sum(array_column($detail_tunjangan, 'nominal') ?? []);
                $total_potongan = array_sum(array_column($detail_potongan, 'nominal') ?? []);

                $summary[$dept]['jumlah_total_upah'] += (float) ($penggajian->gaji_pokok + $total_tunjangan);
                $summary[$dept]['potongan_kedisiplinan'] += (float) $total_potongan;
                $summary[$dept]['pengajuan'] += (float) $penggajian->gaji_bersih;

                $grandTotal += (float) $penggajian->gaji_bersih;
                $totalPotongan += (float) $penggajian->total_potongan;
                $totalPengajuan += (float) $penggajian->gaji_bersih;
            }

            $result = collect(array_values($summary))->sortBy('no')->values();

            // Calculate totals from actual data
            $totalJumlahUpah = array_sum(array_column($summary, 'jumlah_total_upah'));
            $totalPotonganKedisiplinan = array_sum(array_column($summary, 'potongan_kedisiplinan'));
            $totalPengajuan = array_sum(array_column($summary, 'pengajuan'));
        }



        // Add grand total row
        $result->push([
            'no' => '',
            'uraian' => 'Total',
            'jumlah_total_upah' => $totalJumlahUpah,
            'potongan_kedisiplinan' => $totalPotonganKedisiplinan,
            'pengajuan' => $totalPengajuan
        ]);

        // Add terbilang row
        $terbilangText = Terbilang::format($totalPengajuan);
        $result->push([
            'no' => '',
            'uraian' => 'Terbilang:',
            'jumlah_total_upah' => $terbilangText,
            'potongan_kedisiplinan' => '',
            'pengajuan' => ''
        ]);

        // Add empty row
        $result->push([
            'no' => '',
            'uraian' => '',
            'jumlah_total_upah' => '',
            'potongan_kedisiplinan' => '',
            'pengajuan' => ''
        ]);

        // Add signature labels
        $result->push([
            'no' => '',
            'uraian' => 'Pemohon',
            'jumlah_total_upah' => '',
            'potongan_kedisiplinan' => '',
            'pengajuan' => 'Mengetahui'
        ]);

        // Add empty rows for signatures (space for actual signatures)
        $result->push([
            'no' => '',
            'uraian' => '',
            'jumlah_total_upah' => '',
            'potongan_kedisiplinan' => '',
            'pengajuan' => ''
        ]);

        // Add signature names
        $result->push([
            'no' => '',
            'uraian' => 'RENNY',
            'jumlah_total_upah' => '',
            'potongan_kedisiplinan' => '',
            'pengajuan' => 'LILIS SETYANI'
        ]);

        // Add signature positions
        $result->push([
            'no' => '',
            'uraian' => 'HR',
            'jumlah_total_upah' => '',
            'potongan_kedisiplinan' => '',
            'pengajuan' => 'Ka.Dept HRD'
        ]);

        return $result;
    }

    public function title(): string
    {
        // Fungsi untuk menentukan judul sheet
        return ucfirst($this->type);
    }
}
