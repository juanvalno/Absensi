<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $periode->nama_periode ?? 'Periode Gaji' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            font-size: 6pt;
            line-height: 1.1;
            background-color: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page-container {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Adjusted slip container for portrait mode */
        .slip-container {
            height: 97mm;
            position: relative;
            page-break-inside: avoid;
            border-bottom: 0.3px solid #ddd;
            padding-bottom: 2mm;
        }

        .slip-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1mm 2mm;
            border-bottom: 0.3px solid #ddd;
            height: 10mm;
        }

        .company-logo {
            height: 8mm;
            max-width: 18mm;
            filter: grayscale(100%);
        }

        .company-info {
            text-align: center;
            flex-grow: 1;
        }

        .company-name {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 0.3mm;
        }

        .company-address {
            font-size: 5pt;
        }

        .slip-title {
            font-size: 7pt;
            font-weight: bold;
            margin-top: 0.7mm;
        }

        .slip-body {
            padding: 1.5mm;
            display: flex;
            flex-direction: column;
            height: calc(100% - 14mm);
        }

        .employee-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5mm;
            padding-bottom: 1mm;
        }

        .info-left,
        .info-right {
            width: 49%;
        }

        .info-row {
            display: flex;
            margin-bottom: 0.2mm;
            font-size: 6pt;
        }

        .info-label {
            width: 35%;
            font-weight: bold;
        }

        .info-value {
            width: 65%;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .salary-area {
            display: flex;
            margin-bottom: 1mm;
        }

        .salary-section {
            width: 50%;
            padding-right: 1mm;
        }

        .section-title {
            font-weight: bold;
            font-size: 6pt;
            margin-bottom: 0.8mm;
            padding-bottom: 0.5mm;
        }

        .component-list {
            height: 26mm;
            overflow-y: hidden;
        }

        .component-item {
            display: flex;
            justify-content: space-between;
            padding: 0.3mm 0;
        }

        .component-name {
            width: 68%;
        }

        .component-value {
            width: 32%;
            text-align: right;
            font-weight: bold;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8mm 0;
            border-top: 0.5px solid #000;
            margin-top: 0.8mm;
            font-weight: bold;
        }

        .net-salary {
            padding: 1mm;
            text-align: right;
            font-weight: bold;
            font-size: 7pt;
            border-bottom: 0.5px solid #000;
            margin-bottom: 1mm;
        }

        .attendance-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .attendance-title {
            font-weight: bold;
            font-size: 6pt;
            margin-bottom: 0.5mm;
            border-bottom: 0.5px solid #000;
            padding-bottom: 0.5mm;
        }

        .attendance-summary {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0.8mm;
            margin-bottom: 0.8mm;
        }

        .attendance-item {
            font-size: 5.5pt;
            line-height: 1.1;
        }

        .detail-content {
            font-size: 5.5pt;
            white-space: normal;
            line-height: 1.1;
        }

        .detail-section {
            margin-bottom: 0.8mm;
        }

        .inline-details {
            display: inline;
        }

        .cutting-guide {
            height: 6mm;
            text-align: center;
            font-size: 6pt;
            font-weight: bold;
            position: relative;
            padding-top: 2mm;
            padding-bottom: 2mm;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0.5mm 0;
        }

        .cutting-line {
            border: none;
            border-bottom: 1px dashed #000;
            width: 42%;
            display: inline-block;
            height: 0.5mm;
            vertical-align: middle;
            margin: 0 3mm;
        }

        .page-break {
            page-break-after: always;
            height: 0;
            margin: 0;
            padding: 0;
        }

        /* Watermark styling */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            z-index: -1;
            width: 30%;
            height: auto;
            filter: grayscale(100%);
        }

        /* Print styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                filter: grayscale(100%);
            }

            img {
                filter: grayscale(100%);
            }
        }
    </style>
</head>

<body>
    <div class="page-container">
        @php
            // Calculate slips per page
            $slipsPerPage = 3;
            $slipCount = 0;
        @endphp

        @foreach ($penggajians as $index => $penggajian)
            @php
                $slipCount++;

                // Get employee data
                $karyawan = $penggajian->karyawan;

                // Get period data
                $periode = $penggajian->periodeGaji;

                // Parse salary components
                $detailTunjangan = is_string($penggajian->detail_tunjangan)
                    ? json_decode($penggajian->detail_tunjangan, true)
                    : (is_array($penggajian->detail_tunjangan)
                        ? $penggajian->detail_tunjangan
                        : []);

                $detailPotongan = is_string($penggajian->detail_potongan)
                    ? json_decode($penggajian->detail_potongan, true)
                    : (is_array($penggajian->detail_potongan)
                        ? $penggajian->detail_potongan
                        : []);

                $detailDepartemen = is_string($penggajian->detail_departemen)
                    ? json_decode($penggajian->detail_departemen, true)
                    : (is_array($penggajian->detail_departemen)
                        ? $penggajian->detail_departemen
                        : []);

                // Check for any data absensi
                $hariKerja = isset($penggajian->hariKerja)
                    ? $penggajian->hariKerja
                    : (isset($dataAbsensi['total_hari_kerja'])
                        ? $dataAbsensi['total_hari_kerja']
                        : 0);

                $hariHadir = isset($penggajian->hariHadir)
                    ? $penggajian->hariHadir
                    : (isset($dataAbsensi['hadir'])
                        ? $dataAbsensi['hadir']
                        : 0);

                $hariIzin = isset($penggajian->hariIzin)
                    ? $penggajian->hariIzin
                    : (isset($dataAbsensi['izin'])
                        ? $dataAbsensi['izin']
                        : 0);

                $hariCuti = isset($penggajian->hariCuti)
                    ? $penggajian->hariCuti
                    : (isset($dataAbsensi['cuti'])
                        ? $dataAbsensi['cuti']
                        : 0);

                $izinCuti = isset($penggajian->izinCuti)
                    ? $penggajian->izinCuti
                    : (isset($dataAbsensi['izin_cuti'])
                        ? $dataAbsensi['izin_cuti']
                        : 0);

                $hariTidakHadir = isset($penggajian->hariTidakHadir)
                    ? $penggajian->hariTidakHadir
                    : (isset($dataAbsensi['tidak_hadir'])
                        ? $dataAbsensi['tidak_hadir']
                        : 0);

                $kehadiranRate = isset($penggajian->kehadiranRate)
                    ? $penggajian->kehadiranRate
                    : (isset($dataAbsensi['kehadiran_rate'])
                        ? $dataAbsensi['kehadiran_rate']
                        : 0);

                $keterlambatanFormatted = isset($penggajian->keterlambatanFormatted)
                    ? $penggajian->keterlambatanFormatted
                    : (isset($dataAbsensi['keterlambatan_display'])
                        ? $dataAbsensi['keterlambatan_display']
                        : '0 menit');

                $pulangAwalFormatted = isset($penggajian->pulangAwalFormatted)
                    ? $penggajian->pulangAwalFormatted
                    : (isset($dataAbsensi['pulang_awal_display'])
                        ? $dataAbsensi['pulang_awal_display']
                        : '0 menit');

                $totalLembur = isset($penggajian->totalLembur)
                    ? $penggajian->totalLembur
                    : (isset($dataAbsensi['total_lembur'])
                        ? $dataAbsensi['total_lembur']
                        : 0);

                $lemburHariBiasa = isset($penggajian->lemburHariBiasa)
                    ? $penggajian->lemburHariBiasa
                    : (isset($dataAbsensi['lembur_hari_biasa'])
                        ? $dataAbsensi['lembur_hari_biasa']
                        : 0);

                $lemburHariLibur = isset($penggajian->lemburHariLibur)
                    ? $penggajian->lemburHariLibur
                    : (isset($dataAbsensi['lembur_hari_libur'])
                        ? $dataAbsensi['lembur_hari_libur']
                        : 0);

                // Get absensi details
                $absensis = [];
                if (isset($dataAbsensi['absensi'])) {
                    $absensis = $dataAbsensi['absensi'];
                } elseif (isset($penggajian->dataAbsensi) && isset($penggajian->dataAbsensi['absensi'])) {
                    $absensis = $penggajian->dataAbsensi['absensi'];
                }

                // Get lembur details
                $lemburs = [];
                if (isset($dataAbsensi['lembur_disetujui'])) {
                    $lemburs = $dataAbsensi['lembur_disetujui'];
                } elseif (isset($penggajian->dataAbsensi) && isset($penggajian->dataAbsensi['lembur_disetujui'])) {
                    $lemburs = $penggajian->dataAbsensi['lembur_disetujui'];
                }

                // Filter absensis by status for quick access
                $nonHadirAbsensis = collect($absensis)->filter(function ($item) {
                    return isset($item->status) && $item->status != 'Hadir';
                });

                $terlambatAbsensis = collect($absensis)->filter(function ($item) {
                    return isset($item->keterlambatan) && $item->keterlambatan > 0;
                });
            @endphp

            <div class="slip-container">
                <!-- Watermark -->
                <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" class="watermark"
                    onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2250mm%22%20height%3D%2250mm%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23ffffff%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%20font-family%3D%22Arial%22%20font-size%3D%2212pt%22%20fill%3D%22%23000000%22%3ELOGO%3C%2Ftext%3E%3C%2Fsvg%3E'">

                <div class="slip-header">
                    <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" class="company-logo"
                        onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2218mm%22%20height%3D%228mm%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23ffffff%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%20font-family%3D%22Arial%22%20font-size%3D%226pt%22%20fill%3D%22%23000000%22%3ELOGO%3C%2Ftext%3E%3C%2Fsvg%3E'">
                    <div class="company-info">
                        <div class="company-name">PT Gading Gadjah Mada</div>
                        <div class="company-address">
                            Jl. Albisindo Raya No.09, Kel. Gondosari, Kec. Gebog, Kab. Kudus
                        </div>
                        <div class="slip-title">SLIP GAJI KARYAWAN</div>
                    </div>
                </div>

                <div class="slip-body">
                    <div class="employee-info">
                        <div class="info-left">
                            <div class="info-row">
                                <div class="info-label">Nama</div>
                                <div class="info-value">: {{ $karyawan->nama_karyawan ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">NIK</div>
                                <div class="info-value">: {{ $karyawan->nik_karyawan ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Departemen</div>
                                <div class="info-value">: {{ $detailDepartemen['departemen'] ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="info-right">
                            <div class="info-row">
                                <div class="info-label">Periode</div>
                                <div class="info-value">:
                                    {{ isset($penggajian->periode_awal) ? (is_string($penggajian->periode_awal) ? date('d/m/Y', strtotime($penggajian->periode_awal)) : $penggajian->periode_awal->format('d/m/Y')) : '-' }}
                                    s/d
                                    {{ isset($penggajian->periode_akhir) ? (is_string($penggajian->periode_akhir) ? date('d/m/Y', strtotime($penggajian->periode_akhir)) : $penggajian->periode_akhir->format('d/m/Y')) : '-' }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Jabatan</div>
                                <div class="info-value">: {{ $detailDepartemen['jabatan'] ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Status</div>
                                <div class="info-value">: {{ $karyawan->statuskaryawan ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="main-content">
                        <div class="salary-area">
                            <div class="salary-section">
                                <div class="section-title">PENDAPATAN</div>
                                <div class="component-list">
                                    <div class="component-item">
                                        <div class="component-name">Gaji Pokok</div>
                                        <div class="component-value">
                                            {{ 'Rp ' . number_format($penggajian->gaji_pokok, 0, ',', '.') }}</div>
                                    </div>

                                    @if (is_array($detailTunjangan))
                                        @foreach ($detailTunjangan as $tunjangan)
                                            @if (isset($tunjangan['nama']) && isset($tunjangan['nominal']))
                                                <div class="component-item">
                                                    <div class="component-name">{{ $tunjangan['nama'] }}</div>
                                                    <div class="component-value">
                                                        {{ 'Rp ' . number_format($tunjangan['nominal'], 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            @elseif(isset($tunjangan->nama) && isset($tunjangan->nominal))
                                                <div class="component-item">
                                                    <div class="component-name">{{ $tunjangan->nama }}</div>
                                                    <div class="component-value">
                                                        {{ 'Rp ' . number_format($tunjangan->nominal, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <div class="total-row">
                                    <div>Total Pendapatan</div>
                                    <div>
                                        {{ 'Rp ' . number_format($penggajian->gaji_pokok + $penggajian->tunjangan, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <div class="salary-section">
                                <div class="section-title">POTONGAN</div>
                                <div class="component-list">
                                    @if (is_array($detailPotongan) && count($detailPotongan) > 0)
                                        @foreach ($detailPotongan as $potongan)
                                            @if (isset($potongan['nama']) && isset($potongan['nominal']))
                                                <div class="component-item">
                                                    <div class="component-name">{{ $potongan['nama'] }}</div>
                                                    <div class="component-value">
                                                        {{ 'Rp ' . number_format($potongan['nominal'], 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            @elseif(isset($potongan->nama) && isset($potongan->nominal))
                                                <div class="component-item">
                                                    <div class="component-name">{{ $potongan->nama }}</div>
                                                    <div class="component-value">
                                                        {{ 'Rp ' . number_format($potongan->nominal, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="component-item">
                                            <div class="component-name">Tidak ada potongan</div>
                                            <div class="component-value">-</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="total-row">
                                    <div>Total Potongan</div>
                                    <div>{{ 'Rp ' . number_format($penggajian->potongan, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="net-salary">
                            GAJI BERSIH: {{ 'Rp ' . number_format($penggajian->gaji_bersih, 0, ',', '.') }}
                        </div>

                        <div class="attendance-area">
                            <div class="attendance-title">REKAP KEHADIRAN</div>
                            <div class="attendance-summary">
                                <div class="attendance-item">
                                    <span class="attendance-label">Hari Kerja</span>
                                    <span class="attendance-value">{{ $hariKerja }} hari</span>
                                </div>
                                <div class="attendance-item">
                                    <span class="attendance-label">Hadir</span>
                                    <span class="attendance-value">{{ $hariHadir }} hari</span>
                                </div>
                                <div class="attendance-item">
                                    <span class="attendance-label">Tidak Hadir</span>
                                    <span class="attendance-value">{{ $hariTidakHadir }} hari</span>
                                </div>
                                <div class="attendance-item">
                                    <span class="attendance-label">Izin/Cuti</span>
                                    <span class="attendance-value">{{ $hariIzin + $hariCuti + $izinCuti }} hari</span>
                                </div>
                                <div class="attendance-item">
                                    <span class="attendance-label">Keterlambatan</span>
                                    <span class="attendance-value">{{ $keterlambatanFormatted }}</span>
                                </div>
                                <div class="attendance-item">
                                    <span class="attendance-label">Total Lembur</span>
                                    <span class="attendance-value">{{ $totalLembur }} jam</span>
                                </div>
                            </div>

                            <div class="attendance-details">
                                <div class="detail-section">
                                    <div class="detail-title">Detail Ketidakhadiran</div>
                                    <div class="detail-content">
                                        @if ($nonHadirAbsensis->count() > 0)
                                            @php
                                                $absensCount = 0;
                                                $absensItems = [];
                                            @endphp

                                            @foreach ($nonHadirAbsensis as $absen)
                                                @php
                                                    $absensCount++;
                                                    if ($absensCount <= 15) {
                                                        $tanggal = isset($absen->tanggal)
                                                            ? $absen->tanggal->format('d/m/Y')
                                                            : '-';
                                                        $status = $absen->status ?? 'Tidak Hadir';
                                                        $absensItems[] = $tanggal . ' (' . $status . ')';
                                                    }
                                                @endphp
                                            @endforeach

                                            <span class="inline-details">{{ implode(', ', $absensItems) }}</span>
                                            @if ($absensCount > 15)
                                                <span>, dan {{ $absensCount - 15 }} lainnya</span>
                                            @endif
                                        @else
                                            <span class="inline-details">-</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="detail-section">
                                    <div class="detail-title">Detail Keterlambatan</div>
                                    <div class="detail-content">
                                        @if ($terlambatAbsensis->count() > 0)
                                            @php
                                                $terlambatCount = 0;
                                                $terlambatItems = [];
                                            @endphp

                                            @foreach ($terlambatAbsensis as $absen)
                                                @php
                                                    $terlambatCount++;
                                                    if ($terlambatCount <= 15) {
                                                        $tanggal = isset($absen->tanggal)
                                                            ? $absen->tanggal->format('d/m/Y')
                                                            : '-';
                                                        $keterlambatan = $absen->keterlambatan ?? 0;
                                                        $jamMasuk = isset($absen->jam_masuk)
                                                            ? $absen->jam_masuk
                                                            : (isset($absen->jam_in)
                                                                ? $absen->jam_in
                                                                : '-');

                                                        // Format for displaying both lateness duration and actual arrival time
                                                        $terlambatItems[] =
                                                            $tanggal .
                                                            ' (' .
                                                            $keterlambatan .
                                                            ' mnt, masuk: ' .
                                                            $jamMasuk .
                                                            ')';
                                                    }
                                                @endphp
                                            @endforeach

                                            <span class="inline-details">{{ implode(', ', $terlambatItems) }}</span>
                                            @if ($terlambatCount > 15)
                                                <span>, dan {{ $terlambatCount - 15 }} lainnya</span>
                                            @endif
                                        @else
                                            <span class="inline-details">-</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="detail-section">
                                    <div class="detail-title">Detail Lembur</div>
                                    <div class="detail-content">
                                        @if (count($lemburs) > 0)
                                            @php
                                                $lemburCount = 0;
                                                $lemburItems = [];
                                            @endphp

                                            @foreach ($lemburs as $lembur)
                                                @php
                                                    $lemburCount++;
                                                    if ($lemburCount <= 15) {
                                                        $tanggal = isset($lembur->tanggal_lembur)
                                                            ? (is_string($lembur->tanggal_lembur)
                                                                ? date('d/m/Y', strtotime($lembur->tanggal_lembur))
                                                                : $lembur->tanggal_lembur->format('d/m/Y'))
                                                            : '-';

                                                        $durasi = isset($lembur->durasi)
                                                            ? $lembur->durasi
                                                            : (isset($lembur->jam_mulai) && isset($lembur->jam_selesai)
                                                                ? \Carbon\Carbon::parse(
                                                                    $lembur->jam_mulai,
                                                                )->diffInHours(
                                                                    \Carbon\Carbon::parse($lembur->jam_selesai),
                                                                )
                                                                : 0);

                                                        $jenis =
                                                            isset($lembur->jenis_lembur) &&
                                                            $lembur->jenis_lembur == 'Hari Libur'
                                                                ? 'L'
                                                                : 'B';
                                                        $lemburItems[] =
                                                            $tanggal . ' (' . $durasi . 'j/' . $jenis . ')';
                                                    }
                                                @endphp
                                            @endforeach

                                            <span class="inline-details">{{ implode(', ', $lemburItems) }}</span>
                                            @if ($lemburCount > 15)
                                                <span>, dan {{ $lemburCount - 15 }} lainnya</span>
                                            @endif
                                        @else
                                            <span class="inline-details">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($index < count($penggajians) - 1)
                <div class="cutting-guide">
                    <span class="cutting-line"></span>
                    <span style="font-weight: bold; font-size: 7pt;">POTONG DISINI</span>
                    <span class="cutting-line"></span>
                </div>
            @endif

            @if ($slipCount % $slipsPerPage == 0 && $index < count($penggajians) - 1)
    </div>
    <div class="page-break"></div>
    <div class="page-container">
        @php $slipCount = 0; @endphp
        @endif
        @endforeach
    </div>
</body>

</html>
