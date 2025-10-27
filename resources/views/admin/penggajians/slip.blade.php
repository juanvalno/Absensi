@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $periode->nama_periode ?? 'Periode Gaji' }}</title>
    <style>
        @page {
            size: F4 portrait;
            margin: 2mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            font-size: 5.5pt;
            line-height: 1;
            background-color: #fff;
        }

        .page-container {
            display: flex;
            flex-direction: column;
            position: relative;
            max-width: 190mm;
            margin: 0 auto;
        }

        .slip-row {
            display: flex;
            width: 100%;
            justify-content: space-around;
            margin-bottom: 6mm;
            position: relative;
            gap: 6mm;
        }

        .slip-container {
            width: 90mm;
            min-height: 135mm;
            height: auto;
            position: relative;
            page-break-inside: avoid;
            border: 1px solid #000;
            overflow: visible;
        }

        .slip-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1mm 2mm;
            border-bottom: 1px solid #000;
            height: 13mm;
        }

        .company-logo {
            height: 8mm;
            max-width: 16mm;
            filter: grayscale(100%);
        }

        .company-info {
            text-align: center;
            flex-grow: 1;
            padding: 0 1mm;
        }

        .company-name {
            font-size: 7pt;
            font-weight: bold;
            margin-bottom: 0.3mm;
        }

        .company-address {
            font-size: 4.5pt;
            line-height: 1.1;
        }

        .slip-title {
            font-size: 6.5pt;
            font-weight: bold;
            margin-top: 0.5mm;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.20;
            z-index: -1;
            width: 85%;
            height: auto;
            filter: grayscale(100%);
        }

        .employee-info {
            display: flex;
            padding: 1.5mm;
            border-bottom: 1px solid #000;
        }

        .info-row {
            display: flex;
            margin-bottom: 0.8mm;
        }

        .info-label {
            width: 22mm;
            font-weight: bold;
        }

        .info-value {
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 1mm 2mm;
        }

        .table th {
            background-color: #d3d3d3;
            font-weight: bold;
            text-align: center;
        }

        .table tr th[colspan="3"] {
            font-size: 8pt;
            padding: 1.5mm 2mm;
            background-color: #c0c0c0;
            text-transform: uppercase;
            text-align: left;
        }

        .table-currency {
            text-align: right;
        }

        .table-section {
            width: 100%;
            margin-bottom: 1.5mm;
        }

        .vertical-cutting-guide {
            width: 6mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            transform: translateX(-50%);
            z-index: 10;
            margin: 0;
        }

        .vertical-cutting-line {
            height: 80%;
            width: 0;
            border-left: 1px dashed #000;
            position: absolute;
        }

        .vertical-cutting-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 6.5pt;
            font-weight: bold;
            background-color: white;
            padding: 2mm 0;
            position: absolute;
            top: 50%;
            transform: translateY(-50%) rotate(180deg);
        }

        .page-break {
            page-break-after: always;
            height: 0;
            margin: 0;
            padding: 0;
        }

        @media print {
            .slip-container {
                break-inside: avoid;
            }

            .page-container {
                break-before: always;
            }
        }
    </style>
</head>

<body>
    <div class="page-container">
        @php
            $slipsPerPage = 2;
            $slipCount = 0;
        @endphp

        @foreach ($penggajians as $index => $penggajian)
            @php
                $slipCount++;
                $isEven = $slipCount % 2 == 0;
                $isPair = $slipCount % 2 == 1;

                $karyawan = $penggajian->karyawan;
                $periode = $penggajian->periodeGaji;

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

                $totalLembur = isset($penggajian->totalLembur)
                    ? $penggajian->totalLembur
                    : (isset($dataAbsensi['total_lembur'])
                        ? $dataAbsensi['total_lembur']
                        : 0);
            @endphp

            @if ($isPair)
                <div class="slip-row">
            @endif

            <div class="slip-container">
                <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" class="watermark">
                <div class="slip-header">
                    <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" class="company-logo">
                    <div class="company-info">
                        <div class="company-name">PT Gading Gadjah Mada</div>
                        <div class="company-address">
                            Jl. Albisindo Raya No.09, Kel. Gondosari, Kec. Gebog, Kab. Kudus
                        </div>
                        <div class="slip-title">SLIP GAJI {{ strtoupper($periode->nama_periode ?? 'PERIODE GAJI') }}
                        </div>
                    </div>
                </div>

                <div class="employee-info">
                    <div style="width: 100%;">
                        <div class="info-row">
                            <div class="info-label">Nama Lengkap</div>
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
                        <div class="info-row">
                            <div class="info-label">Status</div>
                            <div class="info-value">: {{ $karyawan->statuskaryawan ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div style="padding: 1.5mm;">
                    <table class="table table-section">
                        <tr>
                            <th colspan="3">A. GAJI / UPAH</th>
                        </tr>
                        <tr>
                            <td width="58%">Pokok</td>
                            <td width="32%" class="table-currency">
                                Rp. {{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>

                    <table class="table table-section">
                        <tr>
                            <th colspan="3">B. TUNJANGAN</th>
                        </tr>
                        @if (is_array($detailTunjangan) && count($detailTunjangan) > 0)
                            @foreach ($detailTunjangan as $tunjangan)
                                @if (isset($tunjangan['nama']) &&
                                        isset($tunjangan['nominal']) &&
                                        !in_array($tunjangan['nama'], ['Lembur Hari Kerja', 'Lembur Hari Libur']))
                                    <tr>
                                        <td width="58%">{{ $tunjangan['nama'] }}</td>
                                        <td width="32%" class="table-currency">
                                            Rp. {{ number_format($tunjangan['nominal'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <td width="58%">-</td>
                                <td width="32%" class="table-currency">-</td>
                            </tr>
                        @endif
                    </table>

                    <table class="table table-section">
                        <tr>
                            <th colspan="3">C. CUTI PREMI / UANG TUNGGU</th>
                        </tr>
                        <tr>
                            <td width="58%">Kompensasi</td>
                            <td width="32%" class="table-currency">-</td>
                        </tr>
                        <tr>
                            <td width="58%">Uang Tunggu</td>
                            <td width="32%" class="table-currency">
                                Rp. {{ number_format($penggajian->uang_tunggu ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>

                    <table class="table table-section">
                        <tr>
                            <th colspan="3">D. LEMBUR</th>
                        </tr>
                        @php
                            $lemburKerja = collect($detailTunjangan)->firstWhere('nama', 'Lembur Hari Kerja');
                            $lemburLibur = collect($detailTunjangan)->firstWhere('nama', 'Lembur Hari Libur');
                        @endphp
                        <tr>
                            <td width="58%">Hari Kerja</td>
                            <td width="32%" class="table-currency">
                                {{ $lemburKerja ? 'Rp. ' . number_format($lemburKerja['nominal'], 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td width="58%">Hari Libur</td>
                            <td width="32%" class="table-currency">
                                {{ $lemburLibur ? 'Rp. ' . number_format($lemburLibur['nominal'], 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    </table>

                    <table class="table table-section">
                        <tr>
                            <th colspan="3">E. POTONGAN</th>
                        </tr>
                        @if (is_array($detailPotongan) && count($detailPotongan) > 0)
                            @foreach ($detailPotongan as $potongan)
                                @if (isset($potongan['nama']) && isset($potongan['nominal']))
                                    <tr>
                                        <td width="58%">{{ $potongan['nama'] }}</td>
                                        <td width="32%" class="table-currency">
                                            Rp. {{ number_format($potongan['nominal'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <td width="58%">-</td>
                                <td width="32%" class="table-currency">-</td>
                            </tr>
                        @endif
                    </table>

                    <table class="table table-section" style="font-weight: bold;">
                        <tr>
                            <th colspan="3">Uang Yang Diterima (A+B+C+D-E)</th>
                        </tr>
                        <tr>
                            <td width="58%">Total</td>
                            <td width="32%" class="table-currency">
                                Rp. {{ number_format($penggajian->gaji_bersih, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>

                    <table class="table table-section">
                        <tr>
                            <td width="25%">Kehadiran</td>
                            <td width="25%">{{ $hariHadir }} Hari</td>
                            <td width="25%">Lembur Biasa</td>
                            <td width="25%">
                                @php
                                    $totalLemburBiasa = 0;
                                    $countLemburBiasa = 0;
                                    $lemburBiasa = \App\Models\Lembur::where('karyawan_id', $penggajian->karyawan->id)
                                        ->whereBetween('tanggal_lembur', [
                                            Carbon::parse($periode->tanggal_mulai)->format('Y-m-d'),
                                            Carbon::parse($periode->tanggal_akhir)->format('Y-m-d'),
                                        ])
                                        ->where('jenis_lembur', 'Hari Kerja')
                                        ->where('status', 'Disetujui')
                                        ->get();

                                    foreach ($lemburBiasa as $lembur) {
                                        $countLemburBiasa++;
                                        $durasi = $lembur->lembur_disetujui ?? $lembur->total_lembur;
                                        if (!empty($durasi)) {
                                            preg_match('/(\d+)\s*jam\s*(\d*)\s*menit?/', $durasi, $matches);
                                            $jam = isset($matches[1]) ? intval($matches[1]) : 0;
                                            $menit = isset($matches[2]) ? intval($matches[2]) : 0;
                                            $totalLemburBiasa += $jam + $menit / 60;
                                        }
                                    }

                                    $lemburBiasaJam = floor($totalLemburBiasa);
                                    $lemburBiasaMenit = round(($totalLemburBiasa - $lemburBiasaJam) * 60);
                                @endphp
                                {{ $countLemburBiasa }}x ({{ $lemburBiasaJam }} jam
                                {{ $lemburBiasaMenit > 0 ? $lemburBiasaMenit . ' menit' : '' }})
                            </td>
                        </tr>
                        <tr>
                            <td>Total Hari</td>
                            <td>{{ $hariKerja }} Hari</td>
                            <td>Lembur Hari Libur</td>
                            <td>
                                @php
                                    $totalLemburLibur = 0;
                                    $countLemburLibur = 0;
                                    $lemburLibur = \App\Models\Lembur::where('karyawan_id', $penggajian->karyawan->id)
                                        ->whereBetween('tanggal_lembur', [
                                            Carbon::parse($periode->tanggal_mulai)->format('Y-m-d'),
                                            Carbon::parse($periode->tanggal_akhir)->format('Y-m-d'),
                                        ])
                                        ->where('jenis_lembur', 'Hari Libur')
                                        ->where('status', 'Disetujui')
                                        ->get();

                                    foreach ($lemburLibur as $lembur) {
                                        $countLemburLibur++;
                                        $durasi = $lembur->lembur_disetujui ?? $lembur->total_lembur;
                                        if (!empty($durasi)) {
                                            preg_match('/(\d+)\s*jam\s*(\d*)\s*menit?/', $durasi, $matches);
                                            $jam = isset($matches[1]) ? intval($matches[1]) : 0;
                                            $menit = isset($matches[2]) ? intval($matches[2]) : 0;
                                            $totalLemburLibur += $jam + $menit / 60;
                                        }
                                    }

                                    $lemburLiburJam = floor($totalLemburLibur);
                                    $lemburLiburMenit = round(($totalLemburLibur - $lemburLiburJam) * 60);
                                @endphp
                                {{ $countLemburLibur }}x ({{ $lemburLiburJam }} jam
                                {{ $lemburLiburMenit > 0 ? $lemburLiburMenit . ' menit' : '' }})
                            </td>
                        </tr>
                        <tr>
                            <td>Cuti</td>
                            <td>{{ $hariCuti }} Hari</td>
                            <td>Terlambat</td>
                            <td>{{ $penggajian->keterlambatan ?? 0 }} Menit</td>
                        </tr>
                        <tr>
                            <td>Izin</td>
                            <td>{{ $hariIzin }} Hari</td>
                            <td>Pulang Awal</td>
                            <td>{{ $penggajian->pulang_awal ?? 0 }} Menit</td>
                        </tr>
                        <tr>
                            <td>Pulang Cepat</td>
                            <td>{{ $penggajian->pulang_cepat ?? 0 }} Menit</td>
                            <td>Tidak Hadir</td>
                            <td>{{ $hariKerja - $hariHadir - $hariCuti - $hariIzin }} Hari</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($isPair && $index < count($penggajians) - 1)
                <div class="vertical-cutting-guide">
                    <div class="vertical-cutting-line"></div>
                    <div class="vertical-cutting-text">POTONG DISINI</div>
                </div>
            @endif

            @if ($isEven || $index == count($penggajians) - 1)
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
