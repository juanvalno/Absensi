<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna diizinkan untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true; // Semua pengguna diizinkan untuk login
    }

    /**
     * Mendapatkan aturan validasi yang berlaku untuk permintaan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'], // Email harus diisi, berupa string, dan format email valid
            'password' => ['required', 'string'], // Password harus diisi dan berupa string
        ];
    }

    /**
     * Mencoba mengautentikasi kredensial dari permintaan.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited(); // Pastikan tidak melebihi batas percobaan login

        // Coba login dengan email dan password
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey()); // Catat percobaan gagal

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'), // Tampilkan pesan error
            ]);
        }

        RateLimiter::clear($this->throttleKey()); // Bersihkan catatan percobaan jika berhasil
    }

    /**
     * Memastikan permintaan login tidak melebihi batas percobaan.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Jika belum melebihi 5 percobaan, lanjutkan
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this)); // Kirim event Lockout

        $seconds = RateLimiter::availableIn($this->throttleKey()); // Dapatkan waktu tunggu dalam detik

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60), // Konversi detik ke menit
            ]),
        ]);
    }

    /**
     * Mendapatkan kunci throttle untuk pembatasan percobaan login.
     */
    public function throttleKey(): string
    {
        // Buat kunci unik berdasarkan email dan IP address
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
