<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FungsiKhususController extends Controller
{
    /**
     * Handle date input configuration including backdate functionality
     *
     * @param string $defaultValue Default date value if not specified
     * @return array Complete configuration for date input fields
     */
    public function AktifBackdate($defaultValue = null)
    {
        // Check if the authenticated user has the 'fungsi_khusus.backdate' permission
        $hasBackdatePermission = auth()->check() && auth()->user()->can('fungsi_khusus.AktifBackdate');

        // Set tomorrow as the default value if not specified
        if ($defaultValue === null) {
            $defaultValue = now()->addDay()->format('Y-m-d');
        }

        // Base configuration
        $config = [
            'defaultValue' => $defaultValue,
            'jsConfig' => [],
        ];

        if ($hasBackdatePermission) {
            // Configuration when backdate is allowed
            $config['min'] = '1999-01-01';
            $config['max'] = null;
            $config['html_attributes'] = '';
            $config['jsConfig']['validateMin'] = false;
        } else {
            // Configuration when backdate is not allowed (minimum = tomorrow)
            $tomorrow = now()->addDay()->format('Y-m-d');
            $config['min'] = $tomorrow;
            $config['max'] = null;
            $config['html_attributes'] = 'min="' . $tomorrow . '"';
            $config['jsConfig']['validateMin'] = true;
            $config['jsConfig']['minDate'] = $tomorrow;
        }

        // Add JS validation config
        $config['jsValidation'] = json_encode($config['jsConfig']);

        return $config;
    }
}
