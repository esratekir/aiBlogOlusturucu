<div class="sidenav-menu">
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

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-sm-hover">
        <i class="ri-circle-line align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-fullsidebar">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div data-simplebar>
        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-title">Ana Menü</li>

            <li class="side-nav-item">
                <a href="{{ route('articles.index') }}" class="side-nav-link {{ request()->routeIs('articles.index') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-article"></i></span>
                    <span class="menu-text">Makaleler</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('articles.create') }}" class="side-nav-link {{ request()->routeIs('articles.create') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-pencil-plus"></i></span>
                    <span class="menu-text">Yeni Makale</span>
                </a>
            </li>
        </ul>

        <!-- Help Box -->
        <div class="help-box text-center">
            <h5 class="fw-semibold fs-16">🤖 AI Destekli</h5>
            <p class="mb-3 text-muted">Google Gemini AI ile güçlendirilmiş içerik yönetimi</p>
        </div>

        <div class="clearfix"></div>
    </div>
</div>