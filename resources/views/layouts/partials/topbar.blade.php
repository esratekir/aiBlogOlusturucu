<header class="app-topbar" id="header">
    <div class="page-container topbar-menu">
        <div class="d-flex align-items-center gap-2">
            <!-- Brand Logo -->
            <a href="{{ route('articles.index') }}" class="logo">
                <span class="logo-light">
                    <span class="logo-lg"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></span>
                    <span class="logo-sm"><img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo"></span>
                </span>
                <span class="logo-dark">
                    <span class="logo-lg"><img src="{{ asset('assets/images/logo-dark.png') }}" alt="dark logo"></span>
                    <span class="logo-sm"><img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo"></span>
                </span>
            </a>

            <!-- Sidebar Menu Toggle Button -->
            <button class="sidenav-toggle-button">
                <i class="ri-menu-5-line fs-24"></i>
            </button>

            <!-- Topbar Page Title -->
            <div class="topbar-item d-none d-md-flex px-2">
                <div>
                    <h4 class="page-title fs-20 fw-semibold mb-0">@yield('page-title', 'AI İçerik CMS')</h4>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Light/Dark Mode Button -->
            <div class="topbar-item">
                <button class="topbar-link" id="light-dark-mode" type="button">
                    <i class="ri-moon-line light-mode-icon fs-22"></i>
                    <i class="ri-sun-line dark-mode-icon fs-22"></i>
                </button>
            </div>

            <!-- Theme Settings -->
            <div class="topbar-item">
                <button class="topbar-link" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" type="button">
                    <i class="ri-settings-4-line fs-22"></i>
                </button>
            </div>
        </div>
    </div>
</header>