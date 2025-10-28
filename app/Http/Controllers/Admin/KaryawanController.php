<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Bagian;
use App\Models\Jabatan;
use App\Models\Profesi;
use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Support\Str;
use App\Models\CutiKaryawan;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{
    /**
     * Menampilkan daftar karyawan
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['getAllActive', 'search']); // Biarkan yang ini tanpa auth
    }
    /**
     * Menampilkan daftar karyawan
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search', '');
        $filter = $request->input('filter', 'all');
        $departemen_id = $request->input('departemen_id');
        $bagian_id = $request->input('bagian_id');

        // Get the base query with eager loading
        $query = Karyawan::with(['departemen', 'bagian', 'jabatan', 'profesi', 'programStudi']);

        // Get all departments for filters
        $departemens = Departemen::orderBy('name_departemen')->get();
        
        // Get bagians based on selected departemen
        $bagians = !empty($departemen_id) 
            ? Bagian::where('id_departemen', $departemen_id)->orderBy('name_bagian')->get()
            : collect();

        // Apply search if provided
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'LIKE', "%{$search}%")
                    ->orWhere('nik_karyawan', 'LIKE', "%{$search}%")
                    ->orWhere('no_hp', 'LIKE', "%{$search}%");
            });
        }

        // Apply departemen and bagian filters
        if (!empty($departemen_id)) {
            $query->where('id_departemen', $departemen_id);
            
            if (!empty($bagian_id)) {
                // Verify bagian belongs to selected departemen
                $bagian = Bagian::where('id', $bagian_id)
                    ->where('id_departemen', $departemen_id)
                    ->first();
                    
                if ($bagian) {
                    $query->where('id_bagian', $bagian_id);
                }
            }
        }

        // Apply status filter if provided
        if ($filter !== 'all') {
            if ($filter === 'Resign') {
                $query->whereNotNull('tahun_keluar');
            } else {
                $query->where('statuskaryawan', $filter)
                    ->whereNull('tahun_keluar');
            }
        }

        // Order by department name first, then by employee name
        $query->join('departemens', 'karyawans.id_departemen', '=', 'departemens.id')
            ->orderBy('departemens.name_departemen')
            ->orderBy('karyawans.nama_karyawan');

        // Get paginated results
        $karyawans = $query->select('karyawans.*')->paginate($perPage);

        // Get static counts for sidebar
        $allCount = Karyawan::count();
        $bulananCount = Karyawan::where('statuskaryawan', 'Bulanan')->whereNull('tahun_keluar')->count();
        $harianCount = Karyawan::where('statuskaryawan', 'Harian')->whereNull('tahun_keluar')->count();
        $boronganCount = Karyawan::where('statuskaryawan', 'Borongan')->whereNull('tahun_keluar')->count();
        $resignCount = Karyawan::whereNotNull('tahun_keluar')->count();

        return view('admin.karyawans.index', compact(
            'karyawans',
            'allCount',
            'bulananCount',
            'harianCount',
            'boronganCount',
            'resignCount',
            'departemens',
            'bagians'
        ));
    }

    /**
     * Menampilkan form tambah karyawan
     */
    public function create()
    {
        $departemens        = Departemen::orderBy('name_departemen')->get();
        $bagians            = Bagian::orderBy('name_bagian')->get();
        $jabatans           = Jabatan::orderBy('name_jabatan')->get();
        $profesis           = Profesi::orderBy('name_profesi')->get();
        $programStudis      = ProgramStudi::orderBy('name_programstudi')->get();
        $statusKaryawan     = ['Bulanan', 'Harian', 'Borongan'];
        $statusKawin        = ['Lajang', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
        $pendidikanTerakhir = ['SD/MI', 'SMP/MTS', 'SMA/SMK/MA', 'S1', 'S2', 'S3', 'Lainnya'];
        $ukuranKemeja       = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        $nikKaryawan        = $this->generateNikKaryawan();
        return view('admin.karyawans.create', compact(
            'departemens',
            'bagians',
            'jabatans',
            'profesis',
            'programStudis',
            'statusKaryawan',
            'statusKawin',
            'pendidikanTerakhir',
            'ukuranKemeja',
            'nikKaryawan'
        ));
    }

    /**
     * Menyimpan data karyawan baru
     */
    public function store(Request $request)
    {
        // Get validation rules with conditional bagian validation
        $rules     = $this->getValidationRules($request);
        $messages  = $this->getValidationMessages();
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Set id_bagian to null if department doesn't have bagians
        if ($request->id_departemen) {
            $departemen = Departemen::find($request->id_departemen);
            if (!$departemen || !$departemen->hasBagians()) {
                $data['id_bagian'] = null;
            }
        } else {
            $data['id_bagian'] = null;
        }

        // Handle foto_karyawan upload
        if ($request->hasFile('foto_karyawan')) {
            $fotoKaryawan = $request->file('foto_karyawan');
            $fotoName     = Str::slug($request->nama_karyawan) . '-' . time() . '.' . $fotoKaryawan->getClientOriginalExtension();
            $fotoKaryawan->storeAs('public/karyawan/foto', $fotoName);
            $data['foto_karyawan'] = $fotoName;
        }

        // Handle upload_ktp upload
        if ($request->hasFile('upload_ktp')) {
            $uploadKtp = $request->file('upload_ktp');
            $ktpName = 'ktp-' . Str::slug($request->nama_karyawan) . '-' . time() . '.' . $uploadKtp->getClientOriginalExtension();
            $uploadKtp->storeAs('public/karyawan/ktp', $ktpName);
            $data['upload_ktp'] = $ktpName;
        }

        Karyawan::create($data);

        return redirect()->route('karyawans.index')
            ->with('success', 'Karyawan berhasil ditambahkan');
    }

    /**
     * Menampilkan detail karyawan
     */
    public function show(Karyawan $karyawan)
    {
        $karyawan->load(['departemen', 'bagian', 'jabatan', 'profesi', 'programStudi']);
        return view('admin.karyawans.show', compact('karyawan'));
    }

    /**
     * Menampilkan form edit karyawan
     */
    public function edit(Karyawan $karyawan)
    {
        $departemens        = Departemen::orderBy('name_departemen')->get();
        $bagians            = Bagian::orderBy('name_bagian')->get();
        $jabatans           = Jabatan::orderBy('name_jabatan')->get();
        $profesis           = Profesi::orderBy('name_profesi')->get();
        $programStudis      = ProgramStudi::orderBy('name_programstudi')->get();
        $statusKaryawan     = ['Bulanan', 'Harian', 'Borongan'];
        $statusKawin        = ['Lajang', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
        $pendidikanTerakhir = ['SD/MI', 'SMP/MTS', 'SMA/SMK/MA', 'S1', 'S2', 'S3', 'Lainnya'];
        $ukuranKemeja       = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

        return view('admin.karyawans.edit', compact(
            'karyawan',
            'departemens',
            'bagians',
            'jabatans',
            'profesis',
            'programStudis',
            'statusKaryawan',
            'statusKawin',
            'pendidikanTerakhir',
            'ukuranKemeja'
        ));
    }

    /**
     * Menyimpan perubahan data karyawan
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        // Modify rules for update (including the current ID in unique check)
        $rules     = $this->getValidationRules($request, $karyawan->id);
        $messages  = $this->getValidationMessages();
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Set id_bagian to null if department doesn't have bagians
        if ($request->id_departemen) {
            $departemen = Departemen::find($request->id_departemen);
            if (!$departemen || !$departemen->hasBagians()) {
                $data['id_bagian'] = null;
            }
        } else {
            $data['id_bagian'] = null;
        }

        // Handle foto_karyawan upload
        // Handle foto_karyawan upload
if ($request->hasFile('foto_karyawan')) {
    // Hapus file lama bila ada
    if (!empty($karyawan->foto_karyawan) && Storage::disk('public')->exists('karyawan/foto/' . $karyawan->foto_karyawan)) {
        Storage::disk('public')->delete('karyawan/foto/' . $karyawan->foto_karyawan);
    }

    $foto      = $request->file('foto_karyawan');
    $ext       = $foto->getClientOriginalExtension();
    $fotoName  = Str::slug($request->nama_karyawan) . '-' . time() . '.' . $ext;

    // Simpan dengan nama + ekstensi yang konsisten
    Storage::disk('public')->putFileAs('karyawan/foto', $foto, $fotoName);

    // Simpan ke kolom
    $data['foto_karyawan'] = $fotoName;
}

// Handle upload_ktp upload
if ($request->hasFile('upload_ktp')) {
    if (!empty($karyawan->upload_ktp) && Storage::disk('public')->exists('karyawan/ktp/' . $karyawan->upload_ktp)) {
        Storage::disk('public')->delete('karyawan/ktp/' . $karyawan->upload_ktp);
    }

    $ktp      = $request->file('upload_ktp');
    $extKtp   = $ktp->getClientOriginalExtension();
    $ktpName  = 'ktp-' . Str::slug($request->nama_karyawan) . '-' . time() . '.' . $extKtp;

    Storage::disk('public')->putFileAs('karyawan/ktp', $ktp, $ktpName);

    $data['upload_ktp'] = $ktpName;
}


        $karyawan->update($data);

        // Redirect ke index dengan query string filter yang sama (ambil dari referer jika ada)
        $params = [];
        foreach(['departemen_id','bagian_id','filter','search','per_page'] as $param) {
            if ($request->filled($param)) {
                $params[$param] = $request->input($param);
            }
        }
        return redirect()->route('karyawans.index', $params)
            ->with('success', 'Karyawan berhasil diupdate');
    }

    /**
     * Menghapus data karyawan
     */
    public function destroy(Karyawan $karyawan)
    {
        // Delete associated files
        if ($karyawan->foto_karyawan) {
            Storage::delete('public/karyawan/foto/' . $karyawan->foto_karyawan);
        }

        if ($karyawan->upload_ktp) {
            Storage::delete('public/karyawan/ktp/' . $karyawan->upload_ktp);
        }

        $karyawan->delete();

        // Ambil filter dari query string jika ada
        $params = request()->only(['departemen_id','bagian_id','filter','search','per_page']);
        return redirect()->route('karyawans.index', $params)
            ->with('success', 'Karyawan berhasil dihapus');
    }

    /**
     * Mendapatkan bagian berdasarkan departemen (untuk AJAX)
     */
    public function getBagiansByDepartemen($id_departemen)
    {
        $bagians = Bagian::where('id_departemen', $id_departemen)
            ->orderBy('name_bagian')
            ->get(['id', 'name_bagian']);

        return response()->json([
            'success' => true,
            'data' => $bagians,
            'has_bagians' => $bagians->count() > 0
        ]);
    }

    /**
     * Rules validasi untuk karyawan, dengan validasi kondisional untuk bagian
     */
    protected function getValidationRules(Request $request, $id = null)
    {
        $uniqueNikRule = 'required|string|max:255|unique:karyawans,nik_karyawan';

        // If updating existing record, exclude current ID from unique check
        if ($id) {
            $uniqueNikRule .= ',' . $id;
        }

        $rules = [
            'nik_karyawan'   => $uniqueNikRule,
            'nama_karyawan'  => 'required|string|max:255',
            'foto_karyawan'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'statuskaryawan' => 'required|in:Bulanan,Harian,Borongan',
            'id_departemen'  => 'nullable|exists:departemens,id',
            'tgl_awalmmasuk' => 'required|date',
            'tahun_keluar'   => 'nullable|date',
            'id_jabatan'     => 'nullable|exists:jabatans,id',
            'id_profesi'     => 'nullable|exists:profesis,id',
            'nik'            => [
                'required',
                'numeric',
                'digits_between:1,16',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value == $request->kk) {
                        $fail('NIK dan Nomor KK tidak boleh sama.');
                    }
                },
            ],
            'kk' => [
                'required',
                'numeric',
                'digits_between:1,16',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value == $request->nik) {
                        $fail('Nomor KK dan NIK tidak boleh sama.');
                    }
                },
            ],
            'statuskawin'         => 'required|in:Lajang,Kawin,Cerai Hidup,Cerai Mati',
            'pendidikan_terakhir' => 'required|in:SD/MI,SMP/MTS,SMA/SMK/MA,S1,S2,S3,Lainnya',
            'id_programstudi'     => 'nullable|exists:program_studis,id',
            'no_hp'               => 'required|string|max:16',
            'ortu_bapak'          => 'required|string|max:255',
            'ortu_ibu'            => 'required|string|max:255',
            'ukuran_kemeja'       => 'required|in:S,M,L,XL,XXL,XXXL',
            'ukuran_celana'       => 'required|string|max:5',
            'ukuran_sepatu'       => 'required|string|max:5',
            'jml_anggotakk'       => 'required|string|max:5',
            'upload_ktp'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nama_bank'             => 'nullable|string|max:100',
            'nomor_rekening'        => 'nullable|string|max:50',
            'nama_pemilik_rekening' => 'nullable|string|max:255',
            'iskontrak'             => 'nullable|boolean',
        ];

        // Validasi kondisional untuk id_bagian
        if ($request->id_departemen) {
            $departemen = Departemen::find($request->id_departemen);
            if ($departemen && $departemen->hasBagians()) {
                $rules['id_bagian'] = 'required|exists:bagians,id';
            } else {
                $rules['id_bagian'] = 'nullable|exists:bagians,id';
            }
        } else {
            $rules['id_bagian'] = 'nullable|exists:bagians,id';
        }

        return $rules;
    }

    /**
     * Pesan validasi dalam bahasa Indonesia
     */
    protected function getValidationMessages()
    {
        return [
            'nik_karyawan.required'        => 'NIK Karyawan wajib diisi',
            'nik_karyawan.unique'          => 'NIK Karyawan sudah digunakan',
            'nik_karyawan.max'             => 'NIK Karyawan maksimal 255 karakter',
            'nama_karyawan.required'       => 'Nama Karyawan wajib diisi',
            'nama_karyawan.max'            => 'Nama Karyawan maksimal 255 karakter',
            'foto_karyawan.image'          => 'Foto harus berupa gambar',
            'foto_karyawan.mimes'          => 'Format foto harus jpeg, png, atau jpg',
            'foto_karyawan.max'            => 'Ukuran foto maksimal 2MB',
            'statuskaryawan.required'      => 'Status Karyawan wajib dipilih',
            'statuskaryawan.in'            => 'Status Karyawan tidak valid',
            'id_departemen.exists'         => 'Departemen tidak valid',
            'id_bagian.required'           => 'Bagian wajib dipilih untuk departemen ini',
            'id_bagian.exists'             => 'Bagian tidak valid',
            'tgl_awalmmasuk.required'      => 'Tanggal Mulai Masuk wajib diisi',
            'tgl_awalmmasuk.date'          => 'Format Tanggal Mulai Masuk tidak valid',
            'tahun_keluar.date'            => 'Format Tanggal Keluar tidak valid',
            'id_jabatan.exists'            => 'Jabatan tidak valid',
            'id_profesi.exists'            => 'Profesi tidak valid',
            'nik.required'                 => 'NIK (KTP) wajib diisi',
            'nik.numeric'                  => 'NIK (KTP) harus berupa angka',
            'nik.digits_between'           => 'NIK (KTP) harus antara 1-16 digit angka',
            'kk.required'                  => 'Nomor KK wajib diisi',
            'kk.numeric'                   => 'Nomor KK harus berupa angka',
            'kk.digits_between'            => 'Nomor KK harus antara 1-16 digit angka',
            'statuskawin.required'         => 'Status Perkawinan wajib dipilih',
            'statuskawin.in'               => 'Status Perkawinan tidak valid',
            'pendidikan_terakhir.required' => 'Pendidikan Terakhir wajib dipilih',
            'pendidikan_terakhir.in'       => 'Pendidikan Terakhir tidak valid',
            'id_programstudi.exists'       => 'Program Studi tidak valid',
            'no_hp.required'               => 'Nomor HP wajib diisi',
            'no_hp.max'                    => 'Nomor HP maksimal 16 karakter',
            'ortu_bapak.required'          => 'Nama Ayah wajib diisi',
            'ortu_bapak.max'               => 'Nama Ayah maksimal 255 karakter',
            'ortu_ibu.required'            => 'Nama Ibu wajib diisi',
            'ortu_ibu.max'                 => 'Nama Ibu maksimal 255 karakter',
            'ukuran_kemeja.required'       => 'Ukuran Kemeja wajib dipilih',
            'ukuran_kemeja.in'             => 'Ukuran Kemeja tidak valid',
            'ukuran_celana.required'       => 'Ukuran Celana wajib diisi',
            'ukuran_celana.max'            => 'Ukuran Celana maksimal 5 karakter',
            'ukuran_sepatu.required'       => 'Ukuran Sepatu wajib diisi',
            'ukuran_sepatu.max'            => 'Ukuran Sepatu maksimal 5 karakter',
            'jml_anggotakk.required'       => 'Jumlah Anggota KK wajib diisi',
            'jml_anggotakk.max'            => 'Jumlah Anggota KK maksimal 5 karakter',
            'upload_ktp.image'             => 'File KTP harus berupa gambar',
            'upload_ktp.mimes'             => 'Format KTP harus jpeg, png, atau jpg',
            'upload_ktp.max'               => 'Ukuran KTP maksimal 2MB',
        ];
    }

    /**
     * Generate NIK Karyawan otomatis dengan format YYYYMM###
     */
    protected function generateNikKaryawan()
    {
        // Get current year and month
        $prefix = Carbon::now()->format('Ym'); // Format: YYYYMM

        try {
            // Start a database transaction to ensure uniqueness
            return DB::transaction(function () use ($prefix) {
                // Find the highest sequence number for the current year/month
                $lastSequence = Karyawan::where('nik_karyawan', 'like', $prefix . '%')
                    ->orderByRaw('LENGTH(nik_karyawan) DESC, nik_karyawan DESC')
                    ->value('nik_karyawan');

                if ($lastSequence) {
                    // Extract the sequence part (last 3 digits)
                    $sequence = (int) substr($lastSequence, -3);
                    $sequence++;
                } else {
                    $sequence = 1;
                }

                // Find next available sequence by checking if numbers are taken
                $isUnique = false;
                while (!$isUnique) {
                    $newNik = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);

                    // Check if this NIK already exists
                    $exists = Karyawan::where('nik_karyawan', $newNik)->exists();

                    if (!$exists) {
                        $isUnique = true;
                    } else {
                        $sequence++;
                    }
                }

                return $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            });
        } catch (\Exception $e) {
            // In case of error, return a fallback
            return $prefix . '001';
        }
    }

    /**
     * API endpoint untuk mendapatkan NIK Karyawan baru
     */
    public function getNik()
    {
        // Generate a new NIK following your format: YYYYMM###
        $yearMonth = date('Ym');

        // Find the last sequential number used for this year/month
        $lastKaryawan = Karyawan::where('nik_karyawan', 'like', $yearMonth . '%')
            ->orderBy('nik_karyawan', 'desc')
            ->first();

        if ($lastKaryawan) {
            // Extract and increment the sequential number
            $lastNumber = intval(substr($lastKaryawan->nik_karyawan, -3));
            $newNumber = $lastNumber + 1;
        } else {
            // First employee for this year/month
            $newNumber = 1;
        }

        // Format the new NIK (ensure 3 digits with leading zeros)
        $newNik = $yearMonth . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return response()->json(['nik_karyawan' => $newNik]);
    }

    public function search(Request $request)
    {
        // $query = $request->input('q');
        // console . log($query);
        // $karyawans = Karyawan::where(function ($queryBuilder) use ($query) {
        //     $queryBuilder->where('nama_karyawan', 'like', "%{$query}%")
        //         ->orWhere('nik', 'like', "%{$query}%")
        //         ->orWhere('nik_karyawan', 'like', "%{$query}%");
        // })
        //     // ->whereNull('tahun_keluar') // Only active employees
        //     ->with('bagian') // Include bagian relationship
        //     ->limit(10)
        //     ->get();

        // return response()->json([
        //     'success' => true,
        //     'data' => $karyawans
        // ]);
    }

    public function getAllActive()
    {
        try {
            $karyawans = Karyawan::whereNull('tahun_keluar')
                ->orderBy('id', 'asc')
                ->get(['id', 'nik_karyawan', 'nama_karyawan']);

            return response()->json([
                'success' => true,
                'data' => $karyawans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approval(CutiKaryawan $cuti_karyawan)
    {
        // Your approval logic here
        return view('admin.cuti_karyawans.approval', compact('cuti_karyawan'));
    }

    public function resign(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'tanggal_resign' => 'required|date',
        ]);

        try {
            // Use update method to directly update the record in the database
            $updated = $karyawan->update([
                'tahun_keluar' => $request->tanggal_resign
            ]);

            if (!$updated) {
                throw new \Exception("Gagal mengupdate data karyawan");
            }

            return redirect()->route('karyawans.index')
                ->with('success', 'Status karyawan berhasil diubah menjadi resign.');
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            // Log::error('Failed to update karyawan resign status: ' . $e->getMessage());

            // Redirect back with the actual error message in the alert
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengubah status karyawan: ' . $e->getMessage());
        }
    }


    public function getSupervisorsByDepartemen($id)
    {
        try {
            $supervisors = Karyawan::whereHas('user', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'supervisor');
                });
            })
                ->where('id_departemen', $id)
                ->whereNull('tahun_keluar')
                ->get(['id', 'nik_karyawan', 'nama_karyawan']);

            return response()->json([
                'success' => true,
                'data' => $supervisors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatus($id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);
            $status = $karyawan->statuskaryawan->value;

            return response()->json([
                'status' => $status,
                'is_bulanan' => $status === 'Bulanan' ? 1 : 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Karyawan not found'
            ], 404);
        }
    }
}