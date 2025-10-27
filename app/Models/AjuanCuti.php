public function karyawan()
{
    return $this->belongsTo(Karyawan::class);
}

public function kuotaCutiTahunan()
{
    return $this->belongsTo(KuotaCutiTahunan::class);
}