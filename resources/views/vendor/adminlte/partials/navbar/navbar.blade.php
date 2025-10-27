@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    <style>
        .main-header {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-bottom: none !important;
        }
        .navbar-light .navbar-nav .nav-link {
            color: #4b6cb7;
            transition: all 0.3s;
        }
        .navbar-light .navbar-nav .nav-link:hover {
            color: #182848;
            transform: translateY(-2px);
        }
        .navbar-light .navbar-nav .nav-link:focus {
            color: #182848;
        }
        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #4b6cb7;
        }
        .user-menu .dropdown-menu {
            min-width: 280px;
        }

        /* Dark mode toggle button styles */
        .dark-mode-toggle {
            cursor: pointer;
            padding: 8px 15px;
            display: flex;
            align-items: center;
        }
        .dark-mode-toggle i {
            font-size: 1.2rem;
            margin-right: 5px;
        }

        /* Dark mode styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #f8f9fa;
        }
        body.dark-mode .content-wrapper {
            background-color: #1a1a1a;
        }
        body.dark-mode .card {
            background-color: #2c2c2c;
            color: #f8f9fa;
        }
        body.dark-mode .navbar-light {
            background-color: #2c2c2c !important;
        }
        body.dark-mode .navbar-light .navbar-nav .nav-link {
            color: #f8f9fa;
        }
        body.dark-mode .main-sidebar {
            background: linear-gradient(180deg, #2c2c2c 0%, #1a1a1a 100%);
        }
        body.dark-mode .table {
            color: #f8f9fa;
        }
        body.dark-mode .table-hover tbody tr:hover {
            color: #f8f9fa;
            background-color: rgba(255, 255, 255, 0.075);
        }
    </style>

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Dark Mode Toggle Button --}}
        <li class="nav-item">
            <a class="nav-link dark-mode-toggle" id="darkModeToggle" href="#">
                <i class="fas fa-moon"></i> <span class="d-none d-md-inline-block">Mode Gelap</span>
            </a>
        </li>

        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- User menu link --}}
        @if(Auth::user())
            @if(config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>

{{-- Dark Mode Toggle Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        const icon = darkModeToggle.querySelector('i');
        const text = darkModeToggle.querySelector('span');

        // Check for saved dark mode preference
        const isDarkMode = localStorage.getItem('darkMode') === 'true';

        // Apply saved preference
        if (isDarkMode) {
            body.classList.add('dark-mode');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            text.textContent = 'Mode Terang';
        }

        // Toggle dark mode on click
        darkModeToggle.addEventListener('click', function(e) {
            e.preventDefault();

            body.classList.toggle('dark-mode');

            // Update icon and text
            if (body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                text.textContent = 'Mode Terang';
                localStorage.setItem('darkMode', 'true');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                text.textContent = 'Mode Gelap';
                localStorage.setItem('darkMode', 'false');
            }
        });
    });
</script>
