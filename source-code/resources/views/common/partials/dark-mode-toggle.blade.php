{{-- Dark Mode Toggle — pill switch with sun/moon icons --}}
<div class="d-inline-flex align-items-center ms-1">
    <button type="button"
            id="darkModeToggle"
            class="btn-theme-toggle"
            onclick="toggleDarkMode()"
            title="Toggle theme"
            aria-label="Toggle dark/light mode">
        <span class="toggle-track">
            <span class="toggle-sun"><i class="ri-sun-line"></i></span>
            <span class="toggle-moon"><i class="ri-moon-line"></i></span>
            <span class="toggle-thumb"></span>
        </span>
    </button>
</div>

<style>
/* ---- Theme Toggle Pill ---- */
.btn-theme-toggle {
    background: none;
    border: none;
    padding: 4px;
    cursor: pointer;
    outline: none;
    display: inline-flex;
    align-items: center;
}
.btn-theme-toggle:focus-visible .toggle-track {
    box-shadow: 0 0 0 3px rgba(59,130,246,0.35);
}
.toggle-track {
    position: relative;
    display: inline-flex;
    align-items: center;
    width: 52px;
    height: 26px;
    border-radius: 13px;
    background: #e2e8f0;
    border: 1px solid #cbd5e1;
    transition: background 0.25s, border-color 0.25s;
    overflow: hidden;
}
.toggle-sun,
.toggle-moon {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    font-size: 12px;
    transition: opacity 0.2s, transform 0.25s;
    z-index: 1;
}
.toggle-sun  { left: 4px;  color: #f59e0b; opacity: 1;   transform: scale(1);   }
.toggle-moon { right: 4px; color: #94a3b8; opacity: 0.4; transform: scale(0.8); }
.toggle-thumb {
    position: absolute;
    left: 3px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #ffffff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.18);
    transition: transform 0.25s cubic-bezier(.4,0,.2,1), background 0.25s;
    z-index: 2;
}
/* — Dark state — */
body[data-bs-theme="dark"] .toggle-track {
    background: rgba(59,130,246,0.2);
    border-color: rgba(59,130,246,0.35);
}
body[data-bs-theme="dark"] .toggle-thumb {
    transform: translateX(26px);
    background: #3b82f6;
    box-shadow: 0 0 8px rgba(59,130,246,0.5);
}
body[data-bs-theme="dark"] .toggle-sun  { opacity: 0.3; transform: scale(0.8); }
body[data-bs-theme="dark"] .toggle-moon { opacity: 1;   transform: scale(1);   color: #93c5fd; }
</style>

<script>
function toggleDarkMode() {
    var body   = document.body;
    var isDark = body.getAttribute('data-bs-theme') === 'dark';
    if (isDark) {
        body.removeAttribute('data-bs-theme');
        localStorage.setItem('theme', 'light');
    } else {
        body.setAttribute('data-bs-theme', 'dark');
        localStorage.setItem('theme', 'dark');
    }
}
</script>
