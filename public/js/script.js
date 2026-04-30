let hideTimer;
// Scroll reveal
const reveals = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
  entries.forEach((e, i) => {
    if (e.isIntersecting) {
      setTimeout(() => e.target.classList.add('visible'), i * 80);
      observer.unobserve(e.target);
    }
  });
}, { threshold: 0.12 });
reveals.forEach(el => observer.observe(el));

function fakeLogin() {
    localStorage.setItem('isLoggedIn', 'true');
    localStorage.setItem('userName', 'Adam');

    window.location.href = "/";
}

function renderNavbar() {
    const nav = document.getElementById('navActions');
    const isLoggedIn = localStorage.getItem('isLoggedIn');

    if (!nav) return;

    if (isLoggedIn === 'true') {
        const name = localStorage.getItem('userName') || 'User';

        nav.innerHTML = `
          <div class="nav__profile" id="navProfile">

            <a href="/profile" class="nav__avatar">
              ${name.charAt(0)}
            </a>

            <div class="nav__dropdown" id="profileDropdown">
              <div class="nav__dropdown-info">
                <strong>${name}</strong>
                <span>+62 812-xxxx-xxxx</span>
              </div>

              <a href="/profile" class="nav__profile-link">
                Profile
              </a>

              <button onclick="logout()" class="nav__logout">
                Logout
              </button>
            </div>

          </div>
        `;
    } else {
        nav.innerHTML = `
            <a href="/login" class="nav__btn-login">Login</a>
            <a href="/signup" class="nav__btn-signup">Sign Up</a>
        `;
    }
}

function togglePassword() {
    const input = document.getElementById('passwordField');

    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

function logout() {
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('userName');
    window.location.reload();
}

document.addEventListener('mouseover', function (e) {
    const profile = document.getElementById('navProfile');
    if (!profile) return;

    if (profile.contains(e.target)) {
        clearTimeout(hideTimer);
        profile.classList.add('show');
    }
});

document.addEventListener('mouseout', function (e) {
    const profile = document.getElementById('navProfile');
    if (!profile) return;

    if (!profile.contains(e.relatedTarget)) {
        hideTimer = setTimeout(() => {
            profile.classList.remove('show');
        }, 10000); // ⏱ 10 detik
    }
});

document.addEventListener('DOMContentLoaded', function () {
    renderNavbar();
});

