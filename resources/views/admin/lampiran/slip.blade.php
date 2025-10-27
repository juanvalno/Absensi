<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji Karyawan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 9pt;
            line-height: 1.3;
        }

        .page-container {
            width: 210mm;
            height: 297mm;
            position: relative;
            padding: 5mm;
            box-sizing: border-box;
        }

        .slip {
            border: 1px solid #000;
            margin-bottom: 3mm;
            padding: 5mm;
            position: relative;
            height: 90mm;
            /* A4 height (297mm) / 3 slips = ~99mm, minus margins */
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        .slip-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #000;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }

        .company-logo {
            height: 12mm;
        }

        .company-info {
            text-align: center;
            flex-grow: 1;
        }

        .company-name {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }

        .company-address {
            font-size: 8pt;
        }

        .slip-title {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3mm;
        }

        .employee-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3mm;
        }

        .info-column {
            width: 48%;
        }

        .info-row {
            display: flex;
            margin-bottom: 1mm;
        }

        .info-label {
            width: 30mm;
            font-weight: bold;
        }

        .info-value {
            flex: 1;
        }

        .salary-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            font-size: 8pt;
        }

        .salary-details th,
        .salary-details td {
            border: 1px solid #ddd;
            padding: 1.5mm;
            text-align: left;
        }

        .salary-details th {
            background-color: #f2f2f2;
            font-size: 8pt;
            text-align: center;
        }

        .amount {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
            border-top: 1.5px solid #000;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 4mm;
            font-size: 8pt;
        }

        .signature-box {
            width: 25mm;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            height: 10mm;
            margin-bottom: 1mm;
        }

        .watermark {
            text-align: center;
            color: #999;
            font-style: italic;
            font-size: 7pt;
            margin-top: 1mm;
        }

        .cut-line {
            border-top: 1px dashed #000;
            position: relative;
            margin: 2mm 0;
            height: 5mm;
            text-align: center;
        }

        .cut-line::after {
            content: "✂ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -";
            position: absolute;
            top: -2.5mm;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #777;
        }

        .page-break {
            page-break-after: always;
        }

        .slip-number {
            font-size: 8pt;
            color: #999;
            text-align: right;
            position: absolute;
            right: 5mm;
            bottom: 2mm;
        }
    </style>
</head>
<body>
    @php
    $slipCount = 0;
    $totalSlips = count($penggajians);
    @endphp

    @foreach($penggajians as $index => $penggajian)
        @php $slipCount++; @endphp

        <div class="slip">
            <div class="slip-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Perusahaan" class="company-logo">
                <div class="company-info">
                    <div class="company-name">{{ config('app.company_name', 'PT. NAMA PERUSAHAAN') }}</div>
                    <div class="company-address">{{ config('app.company_address', 'Jl. Alamat Perusahaan No. 123, Kota, Indonesia') }}</div>
                    <div class="slip-title">SLIP GAJI KARYAWAN</div>
                </div>
            </div>

            <div class="employee-info">
                <div class="info-column">
                    <div class="info-row">
                        <div class="info-label">Nama</div>
                        <div class="info-value">: {{ $penggajian->karyawan->nama_karyawan }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">NIK</div>
                        <div class="info-value">: {{ $penggajian->karyawan->nik_karyawan }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Departemen</div>
                        <div class="info-value">: {{ $penggajian->karyawan->departemen->name_departemen ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Jabatan</div>
                        <div class="info-value">: {{ $penggajian->karyawan->jabatan->name_jabatan ?? '-' }}</div>
                    </div>
                </div>

                <div class="info-column">
                    <div class="info-row">
                        <div class="info-label">Periode</div>
                        <div class="info-value">: {{ \Carbon\Carbon::parse($penggajian->periode->tanggal_awal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($penggajian->periode->tanggal_akhir)->format('d/m/Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nama Periode</div>
                        <div class="info-value">: {{ $penggajian->periode->nama_periode }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div class="info-value">: {{ $penggajian->karyawan->statuskaryawan }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tgl. Cetak</div>
                        <div class="info-value">: {{ now()->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>

            <table class="salary-details">
                <tr>
                    <th width="24%">KETERANGAN</th>
                    <th width="24%">JUMLAH</th>
                    <th width="24%">POTONGAN</th>
                    <th width="24%">JUMLAH</th>
                </tr>

                @php
                // Inisialisasi variabel
                $totalPendapatan = $penggajian->gaji_pokok;
                $totalPotongan = 0;
                
                // Decode detail tunjangan dan potongan
                $detailTunjangan = json_decode($penggajian->detail_tunjangan, true) ?? [];
                $detailPotongan = json_decode($penggajian->detail_potongan, true) ?? [];
                
                // Hitung potongan wajib
                $potonganWajib = [
                    'BPJS Kesehatan (1%)' => $penggajian->potongan_bpjs_kesehatan ?? ($penggajian->gaji_pokok * 0.01),
                    'BPJS TK (2%)' => $penggajian->potongan_bpjs_tk ?? ($penggajian->gaji_pokok * 0.02),
                    'PPh 21' => $penggajian->potongan_pph21 ?? 0
                ];
                
                // Tambahkan potongan wajib ke total
                foreach ($potonganWajib as $nominal) {
                    $totalPotongan += $nominal;
                }
                
                // Tambahkan potongan lain ke total
                foreach ($detailPotongan as $nominal) {
                    $totalPotongan += $nominal;
                }
                
                // Hitung tunjangan
                $tunjanganJabatan = $penggajian->tunjangan_jabatan ?? ($penggajian->karyawan->jabatan->tunjangan_jabatan ?? 0);
                $tunjanganProfesi = $penggajian->tunjangan_profesi ?? ($penggajian->karyawan->profesi->tunjangan_profesi ?? 0);
                
                // Tambahkan tunjangan ke total pendapatan
                $totalPendapatan += $tunjanganJabatan + $tunjanganProfesi;
                
                // Tambahkan tunjangan lain ke total
                foreach ($detailTunjangan as $nominal) {
                    $totalPendapatan += $nominal;
                }
                
                // Siapkan array pendapatan
                $pendapatan = [
                    'Gaji Pokok' => $penggajian->gaji_pokok,
                    'Tunjangan Jabatan' => $tunjanganJabatan
                ];
                
                if ($tunjanganProfesi > 0) {
                    $pendapatan['Tunjangan Profesi'] = $tunjanganProfesi;
                }
                
                // Tambahkan detail tunjangan ke array pendapatan
                foreach ($detailTunjangan as $nama => $nominal) {
                    $pendapatan[$nama] = $nominal;
                }
                
                // Tambahkan detail potongan ke array potongan
                foreach ($detailPotongan as $nama => $nominal) {
                    $potonganWajib[$nama] = $nominal;
                }
                
                // Siapkan iterasi untuk display
                $maxRows = max(count($pendapatan), count($potonganWajib));
                $pendapatanKeys = array_keys($pendapatan);
                $potonganKeys = array_keys($potonganWajib);
                @endphp

                @for($i = 0; $i < $maxRows; $i++)
                    <tr>
                        @if(isset($pendapatanKeys[$i]))
                            <td>{{ $pendapatanKeys[$i] }}</td>
                            <td class="amount">Rp {{ number_format($pendapatan[$pendapatanKeys[$i]], 0, ',', '.') }}</td>
                        @else
                            <td></td>
                            <td></td>
                        @endif

                        @if(isset($potonganKeys[$i]))
                            <td>{{ $potonganKeys[$i] }}</td>
                            <td class="amount">Rp {{ number_format($potonganWajib[$potonganKeys[$i]], 0, ',', '.') }}</td>
                        @else
                            <td></td>
                            <td></td>
                        @endif
                    </tr>
                @endfor

                <tr class="total-row">
                    <td>Total Pendapatan</td>
                    <td class="amount">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                    <td>Total Potongan</td>
                    <td class="amount">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" style="text-align: right; padding-right: 5mm;">GAJI BERSIH</td>
                    <td colspan="2" class="amount" style="font-size: 10pt;">Rp {{ number_format($penggajian->gaji_bersih, 0, ',', '.') }}</td>
                </tr>
            </table>

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>HRD</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>KEUANGAN</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>KARYAWAN</div>
                </div>
            </div>

            <div class="watermark">
                Slip gaji ini dibuat secara otomatis dan sah tanpa tanda tangan.
            </div>

            <div class="slip-number">{{ $index + 1 }}/{{ $totalSlips }}</div>
        </div>

        @if($slipCount < $totalSlips)
            <div class="cut-line"></div>
        @endif

        @if($slipCount % 3 == 0 && $slipCount < $totalSlips)
            <div class="page-break"></div>
            @php $slipCount = 0; @endphp
        @endif
    @endforeach
</body>
</html>
