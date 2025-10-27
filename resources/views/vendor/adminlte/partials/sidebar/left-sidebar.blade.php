<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    {{-- Sidebar brand logo --}}
    @if (config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar menu --}}
    <div class="sidebar">
        <nav class="pt-2">


            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu"
                @if (config('adminlte.sidebar_nav_animation_speed') != 300) data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}" @endif
                @if (!config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif>
                {{-- Configured sidebar links --}}
                @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')
            </ul>
        </nav>
    </div>

    <style>
        .sidebar-dark-primary {
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
        }

        .nav-sidebar .nav-item>.nav-link {
            margin-bottom: 5px;
            border-radius: 5px;
            transition: all 0.3s ease;
            color: #f8f9fa;
        }

        .nav-sidebar .nav-item>.nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
            color: #ffc107;
        }

        .nav-sidebar .nav-item.menu-open>.nav-link {
            background-color: #ffc107;
            color: #1e3c72;
        }

        .nav-sidebar .nav-item.menu-open>.nav-link i {
            color: #1e3c72;
        }

        .sidebar .nav-link p {
            font-weight: 500;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            color: #f8f9fa;
        }

        .nav-sidebar .nav-item.menu-open .nav-treeview .nav-link {
            color: #f8f9fa;
        }

        .nav-sidebar .nav-item.menu-open .nav-treeview .nav-link:hover {
            color: #ffc107;
        }

        .nav-sidebar .nav-item.menu-open .nav-treeview .nav-link.active {
            background-color: rgba(255, 193, 7, 0.3);
            color: #ffc107;
        }

        .user-panel {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-panel .info small {
            display: block;
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .brand-text {
            color: #ffc107 !important;
            font-weight: 600 !important;
        }
    </style>
</aside>
