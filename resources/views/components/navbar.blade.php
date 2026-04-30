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
               Home
            </a>
        </li>

        <li>
            <a href="{{ route('rooms.index') }}"
               class="nav__link {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
               Rooms
            </a>
        </li>

        <li>
            <a href="{{ route('home') }}#facilities" class="nav__link">
               About
            </a>
        </li>

        <li>
            <a href="{{ route('gallery') }}" 
               class="nav__link {{ request()->routeIs('gallery') ? 'active' : '' }}">
               Gallery
            </a>
        </li>

        <li>
            <a href="{{ route('contact') }}" 
               class="nav__link {{ request()->routeIs('contact') ? 'active' : '' }}">
               Contact
            </a>
        </li>
    </ul>

    <div class="nav__actions" id="navActions">
    <a href="{{ route('login') }}" class="nav__btn-login">Login</a>
    <a href="{{ route('signup') }}" class="nav__btn-signup">Sign Up</a>
    </div>
</nav>