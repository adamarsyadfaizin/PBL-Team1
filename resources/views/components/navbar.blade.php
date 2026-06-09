<nav class="nav">
    <a href="{{ route('home') }}" class="nav__logo">
        <img src="{{ asset('images/logo.png') }}" alt="Berlima Logo" class="nav__logo-img" onerror="this.style.display='none'">
        <span class="nav__logo-text">Berlima</span>
        <span class="nav__logo-dot"></span>
    </a>

    <ul class="nav__links">
        <li>
            <a href="{{ route('home') }}" 
               class="nav__link {{ request()->routeIs('home') ? 'active' : '' }}">
               Beranda
            </a>
        </li>

        <li>
            <a href="{{ route('rooms.index') }}"
               class="nav__link {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
               Kamar
            </a>
        </li>

        <li>
            <a href="{{ route('about') }}"
               class="nav__link {{ request()->routeIs('about') ? 'active' : '' }}">
               Tentang
            </a>
        </li>

        <li>
            <a href="{{ route('contact') }}" 
               class="nav__link {{ request()->routeIs('contact') ? 'active' : '' }}">
               Kontak
            </a>
        </li>
    </ul>

    <div class="nav__actions" id="navActions" data-authenticated="{{ auth()->check() ? 'true' : 'false' }}" data-user-name="{{ auth()->user()?->name }}">
        @auth
        <div class="nav__profile" id="navProfile">
            <a href="{{ route('profile') }}" class="nav__avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </a>

            <div class="nav__dropdown" id="profileDropdown">
                <div class="nav__dropdown-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ auth()->user()->phone ?? '+62 812-xxxx-xxxx' }}</span>
                </div>

                <a href="{{ route('profile') }}" class="nav__profile-link">
                    Profil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav__logout">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
        @else
        <a href="{{ route('login') }}" class="nav__btn-login">Masuk</a>
        <a href="{{ route('signup') }}" class="nav__btn-signup">Daftar</a>
        @endauth
    </div>
</nav>
