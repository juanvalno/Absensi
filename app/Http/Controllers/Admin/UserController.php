<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'karyawan'])->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $departemen = Departemen::orderBy('name_departemen')->get();

        // Ambil semua karyawan dan kelompokkan berdasarkan departemen
        $karyawanByDepartemen = [];
        $karyawans = Karyawan::whereNull('user_id')
            ->select('id', 'id_departemen', 'nik_karyawan', 'nama_karyawan')
            ->orderBy('nama_karyawan')
            ->get();

        foreach ($karyawans as $karyawan) {
            $karyawanByDepartemen[$karyawan->id_departemen][] = $karyawan;
        }

        return view('admin.users.create', compact('roles', 'departemen', 'karyawanByDepartemen'));
    }



    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'required|array',
            'user_type' => 'required|in:owner,karyawan',
            'karyawan_id' => 'required_if:user_type,karyawan|uuid|exists:karyawans,id',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign roles
        $user->assignRole($request->roles);

        // If user_type is karyawan, link to the selected karyawan
        if ($request->user_type == 'karyawan' && $request->karyawan_id) {
            // Update the karyawan to link to this user
            Karyawan::where('id', $request->karyawan_id)
                ->update(['user_id' => $user->id]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $departemen = Departemen::orderBy('name_departemen')->get();

        // Cek apakah user adalah owner atau karyawan
        $userType = 'owner'; // Default
        $karyawan = Karyawan::where('user_id', $user->id)->first();

        // Ambil semua karyawan yang belum punya user_id atau karyawan yang sedang diedit
        $karyawanByDepartemen = [];
        $karyawans = Karyawan::where(function ($query) use ($user) {
            $query->whereNull('user_id')
                ->orWhere('user_id', $user->id);
        })
            ->select('id', 'id_departemen', 'nik_karyawan', 'nama_karyawan')
            ->orderBy('nama_karyawan')
            ->get();

        foreach ($karyawans as $k) {
            $karyawanByDepartemen[$k->id_departemen][] = $k;
        }

        if ($karyawan) {
            $userType = 'karyawan';
        }

        return view('admin.users.edit', compact('user', 'roles', 'departemen', 'userType', 'karyawan', 'karyawanByDepartemen'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'nullable|array',
            'user_type' => 'required|in:owner,karyawan',
            'departemen_id' => 'required_if:user_type,karyawan|exists:departemens,id',
            'karyawan_id' => 'required_if:user_type,karyawan|exists:karyawans,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        if ($request->password) {
            $request->validate(['password' => 'min:8']);
            $user->update(['password' => bcrypt($request->password)]);
        }

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }

        // Tangani perubahan tipe user
        $oldKaryawan = Karyawan::where('user_id', $user->id)->first();

        if ($request->user_type === 'karyawan') {
            // Pastikan karyawan ada di departemen yang dipilih
            $karyawan = Karyawan::findOrFail($request->karyawan_id);


            //selidiki kode
            // if ($karyawan->departemen_id != $request->departemen_id) {
            //     return back()->withInput()->with('error', 'Karyawan tidak berada dalam departemen yang dipilih.');
            // }

            // Jika tipe user adalah karyawan
            if ($oldKaryawan && $oldKaryawan->id != $request->karyawan_id) {
                // Jika sudah ada karyawan terkait dan berganti
                $oldKaryawan->user_id = null;
                $oldKaryawan->save();

                $karyawan->user_id = $user->id;
                $karyawan->save();
            } elseif (!$oldKaryawan) {
                // Jika belum ada karyawan terkait
                $karyawan->user_id = $user->id;
                $karyawan->save();
            }
        } elseif ($request->user_type === 'owner' && $oldKaryawan) {
            // Jika user diubah dari karyawan menjadi owner, lepaskan keterkaitan
            $oldKaryawan->user_id = null;
            $oldKaryawan->save();
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak bisa menghapus user yang sedang login');
        }

        // Lepaskan relasi ke karyawan jika ada
        Karyawan::where('user_id', $user->id)->update(['user_id' => null]);

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Get karyawan by departemen ID (for AJAX request)
     */
    public function getKaryawanByDepartemen(Request $request)
    {
        $request->validate([
            'departemen_id' => 'required|exists:departemens,id'
        ]);

        // Perbaiki nama kolom id_departemen sesuai dengan model Karyawan
        $karyawans = Karyawan::where('id_departemen', $request->departemen_id)
            ->whereNull('user_id') // Hanya ambil karyawan yang belum terhubung dengan user
            ->orderBy('nama_karyawan')
            ->select('id', 'nik_karyawan', 'nama_karyawan')
            ->get();

        return response()->json($karyawans);
    }

    /**
     * Reset user password to default (12345678)
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(User $user)
    {
        // Reset the password to 12345678
        $user->password = Hash::make('12345678');
        $user->save();

        return redirect()->route('users.index')
            ->with('success', "Password untuk pengguna {$user->name} telah berhasil direset menjadi 12345678");
    }
}
