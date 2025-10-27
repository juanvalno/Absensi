<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Str;

class ActionButton extends Component
{
    /**
     * Specific action untuk button
     */
    public $action;

    /**
     * URL atau route button
     */
    public $href;

    /**
     * Class tambahan untuk button
     */
    public $class;

    /**
     * Icon untuk button
     */
    public $icon;

    /**
     * Teks tooltip
     */
    public $tooltip;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($action = null, $href = '#', $class = '', $icon = '', $tooltip = '')
    {
        $this->action = $action;
        $this->href = $href;
        $this->class = $class;
        $this->icon = $icon;
        $this->tooltip = $tooltip;
    }

    /**
     * Cek apakah user memiliki permission
     */
    public function userCan()
    {
        if (!auth()->check()) {
            return false;
        }

        // Super admin selalu punya izin
        if (auth()->user()->hasRole('super-admin')) {
            return true;
        }

        // Jika action diberikan langsung, gunakan itu
        if ($this->action && auth()->user()->can($this->action)) {
            return true;
        }

        // Auto-detect permission dari route jika action tidak diberikan
        if (!$this->action) {
            $route = request()->route();
            if (!$route || !isset($route->action['controller'])) {
                return false;
            }

            // Parse controller dan method
            $routeAction = $route->action['controller'];
            list($controller, $method) = explode('@', $routeAction);

            // Format nama controller untuk permission
            $name = class_basename($controller);
            $name = str_replace('Controller', '', $name);
            $kebabName = Str::kebab($name);

            // Tentukan module name
            if (!Str::contains($kebabName, '-')) {
                $module = Str::plural($kebabName);
            } else {
                $module = str_replace('-', '_', $kebabName);
            }

            // Map method ke permission action
            $actionMap = [
                'index' => 'view',
                'show' => 'view',
                'create' => 'create',
                'store' => 'create',
                'edit' => 'edit',
                'update' => 'edit',
                'destroy' => 'delete'
            ];

            $permissionAction = null;
            foreach ($actionMap as $actionName => $permissionName) {
                if ($method === $actionName || Str::startsWith($method, $actionName)) {
                    $permissionAction = $permissionName;
                    break;
                }
            }

            // Jika tidak ditemukan, gunakan method name sebagai permissionAction
            if (!$permissionAction) {
                $permissionAction = $method;
            }

            // Build permission name
            $permissionName = $module . '.' . $permissionAction;

            return auth()->user()->can($permissionName);
        }

        return false;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.action-button');
    }
}
