<table>
    <tr>
        <td colspan="5">PT Gading Gadjah Mada</td>
    </tr>
    <tr>
        <td colspan="5">Jl. Albisindo Raya No.09, Kel. Gondosari, Kec. Gebog, Kab. Kudus</td>
    </tr>
    <tr><td colspan="5"></td></tr>
    <tr>
        <td colspan="5">LAPORAN GAJI KARYAWAN HARIAN</td>
    </tr>
    <tr>
        <td colspan="5">Periode : Gaji Bulanan {{ $keuangan->periode->tanggal_mulai->format('d M Y') }} - {{ $keuangan->periode->tanggal_selesai->format('d M Y') }}</td>
    </tr>
    <tr>
        <td colspan="5">Tgl : {{ $keuangan->created_at->format('d-m-Y') }}</td>
    </tr>
    <tr><td colspan="5"></td></tr>
    <tr>
        <td>No</td>
        <td>Uraian</td>
        <td>Jumlah Total Upah</td>
        <td>Potongan Kedisiplinan</td>
        <td>Pengajuan</td>
    </tr>
    @php
        $total_upah = 0;
        $total_potongan = 0;
        $total_pengajuan = 0;
    @endphp
    @foreach($keuangan->penggajians->groupBy('karyawan.departemen.name_departemen') as $dept => $penggajians)
        @php
            $dept_upah = $penggajians->sum('gaji_pokok');
            $dept_potongan = $penggajians->sum('potongan');
            $dept_pengajuan = $penggajians->sum('gaji_bersih');

            $total_upah += $dept_upah;
            $total_potongan += $dept_potongan;
            $total_pengajuan += $dept_pengajuan;
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ strtoupper($dept) }}</td>
            <td>{{ $dept_upah }}</td>
            <td>{{ $dept_potongan }}</td>
            <td>{{ $dept_pengajuan }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="2">TOTAL</td>
        <td>{{ $total_upah }}</td>
        <td>{{ $total_potongan }}</td>
        <td>{{ $total_pengajuan }}</td>
    </tr>
    <tr>
        <td colspan="2">Terbilang:</td>
        <td colspan="3">{{ ucwords(Terbilang::make($total_pengajuan)) }} Rupiah</td>
    </tr>
    <tr><td colspan="5"></td></tr>
    <tr><td colspan="5"></td></tr>
    <tr>
        <td colspan="2" style="text-align: center">Pemohon</td>
        <td></td>
        <td colspan="2" style="text-align: center">Mengetahui</td>
    </tr>
    <tr><td colspan="5"></td></tr>
    <tr><td colspan="5"></td></tr>
    <tr><td colspan="5"></td></tr>
    <tr>
        <td colspan="2" style="text-align: center">RENNY</td>
        <td></td>
        <td colspan="2" style="text-align: center">LILIS SETYANI</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center">HR</td>
        <td></td>
        <td colspan="2" style="text-align: center">Ka.Dept HRD</td>
    </tr>
</table>