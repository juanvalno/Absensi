<?php

namespace App\Helpers;

class Terbilang
{
    private static $bilangan = [
        '',
        'Satu',
        'Dua',
        'Tiga',
        'Empat',
        'Lima',
        'Enam',
        'Tujuh',
        'Delapan',
        'Sembilan',
        'Sepuluh',
        'Sebelas'
    ];

    public static function format($number)
    {
        if ($number < 12) {
            return static::$bilangan[$number];
        } elseif ($number < 20) {
            return static::$bilangan[$number - 10] . ' Belas';
        } elseif ($number < 100) {
            return static::$bilangan[(int)($number / 10)] . ' Puluh ' . static::$bilangan[$number % 10];
        } elseif ($number < 200) {
            return 'Seratus ' . static::format($number - 100);
        } elseif ($number < 1000) {
            return static::$bilangan[(int)($number / 100)] . ' Ratus ' . static::format($number % 100);
        } elseif ($number < 2000) {
            return 'Seribu ' . static::format($number - 1000);
        } elseif ($number < 1000000) {
            return static::format((int)($number / 1000)) . ' Ribu ' . static::format($number % 1000);
        } elseif ($number < 1000000000) {
            return static::format((int)($number / 1000000)) . ' Juta ' . static::format($number % 1000000);
        } elseif ($number < 1000000000000) {
            return static::format((int)($number / 1000000000)) . ' Milyar ' . static::format($number % 1000000000);
        }

        return static::format((int)($number / 1000000000000)) . ' Trilyun ' . static::format($number % 1000000000000);
    }
}