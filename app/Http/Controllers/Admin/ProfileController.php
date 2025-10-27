<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Exception;
use DB;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index(): View
    {
        $user = auth()->user()->load('karyawan.departemen');
        return view('admin.profile.view', compact('user'));
    }

    /**
     * Display the user's profile edit form.
     */
    public function edit(): View
    {
        $user = auth()->user()->load('karyawan.departemen');
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            $user->fill($request->validated());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            DB::commit();
            return redirect()
                ->route('profile.index')
                ->with('success', 'Profil berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);

            $user = auth()->user();
            $user->password = Hash::make($request->password);
            $user->save();

            DB::commit();
            return redirect()
                ->route('profile.index')
                ->with('success', 'Password berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui password. Pastikan password lama Anda benar.');
        }
    }
}
