// ==========================================================================
// WEBSITE 1ST — MAIN SCRIPT
// Handles: dark mode, mobile menu toggle, login/signup modal open/close,
// tab switching, logo lightbox, and demo form handling
// ==========================================================================

document.addEventListener('DOMContentLoaded', function () {

  // ---- Language switcher ----
function applyLanguage(lang) {
  const dict = window.translations && window.translations[lang];
  if (!dict) return;

  document.querySelectorAll('[data-i18n]').forEach(function (el) {
    const key = el.getAttribute('data-i18n');
    if (dict[key]) el.innerHTML = dict[key];
  });

  document.querySelectorAll('[data-i18n-placeholder]').forEach(function (el) {
    const key = el.getAttribute('data-i18n-placeholder');
    if (dict[key]) el.setAttribute('placeholder', dict[key]);
  });

  document.querySelectorAll('.lang-select').forEach(function (select) {
    select.value = lang;
  });

  localStorage.setItem('lang', lang);
}

const savedLang = localStorage.getItem('lang') || 'en';
applyLanguage(savedLang);

document.querySelectorAll('.lang-select').forEach(function (select) {
  select.addEventListener('change', function () {
    applyLanguage(this.value);
  });
});

  // ---- Mobile dashboard sidebar toggle ----
const dashMobileToggle = document.getElementById('dashMobileToggle');
const dashSidebarOverlay = document.getElementById('dashSidebarOverlay');

if (dashMobileToggle && dashSidebarOverlay) {
  dashMobileToggle.addEventListener('click', function () {
    document.body.classList.toggle('dash-sidebar-open');
  });

  dashSidebarOverlay.addEventListener('click', function () {
    document.body.classList.remove('dash-sidebar-open');
  });

  document.querySelectorAll('.dash-nav a').forEach(function (link) {
    link.addEventListener('click', function () {
      document.body.classList.remove('dash-sidebar-open');
    });
  });
}
  // Validate required fields and highlight empty ones in red
function validateRequiredFields(form) {
  let valid = true;
  form.querySelectorAll('input[required]').forEach(function (input) {
    if (!input.value.trim()) {
      input.classList.add('input-error');
      valid = false;
    } else {
      input.classList.remove('input-error');
    }
    input.addEventListener('input', function () {
      this.classList.remove('input-error');
    });
  });
  return valid;
}

document.querySelectorAll('.modal-form[data-form="signup"], .modal-form[data-form="login"]').forEach(function (form) {
  form.addEventListener('submit', function (e) {
    if (!validateRequiredFields(form)) {
      e.preventDefault();
      return;
    }
    const btn = form.querySelector('button[type="submit"]');
    if (btn) {
      btn.disabled = true;
      btn.textContent = form.dataset.form === 'signup' ? 'Creating Account...' : 'Logging In...';
    }
  });
});

  // Update flag image when WhatsApp country code changes
const whatsappCountry = document.getElementById('whatsappCountry');
const whatsappFlag = document.getElementById('whatsappFlag');
if (whatsappCountry && whatsappFlag) {
  whatsappCountry.addEventListener('change', function () {
    const code = this.options[this.selectedIndex].getAttribute('data-flag');
    whatsappFlag.src = `https://flagcdn.com/24x18/${code}.png`;
  });
}

    // ---- 0. Dark mode toggle ----
  const themeToggles = document.querySelectorAll('.theme-toggle');
  const htmlRoot = document.documentElement;

  const savedTheme = localStorage.getItem('theme');

  if (savedTheme === 'dark') {
    htmlRoot.setAttribute('data-theme', 'dark');
  }

  themeToggles.forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      const isDark = htmlRoot.getAttribute('data-theme') === 'dark';

      if (isDark) {
        htmlRoot.removeAttribute('data-theme');
        localStorage.setItem('theme', 'light');
      } else {
        htmlRoot.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
      }
    });
  });

  // ---- 1. Mobile hamburger menu ----
  const navToggle = document.querySelector('.nav-toggle');
  const navLinks = document.querySelector('.nav-links');

  if (navToggle && navLinks) {
    navToggle.addEventListener('click', function () {
      navLinks.classList.toggle('open');
    });
  }

  // ---- 2. Login/Signup modal open + close ----
  const modalOverlay = document.getElementById('authModal');
  const openModalButtons = document.querySelectorAll('[data-open-modal]');
  const closeModalButton = document.querySelector('.modal-close');

  function openModal(startTab) {
    if (!modalOverlay) return;

    modalOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    if (startTab) {
      switchTab(startTab);
    }
  }

  function closeModal() {
    if (!modalOverlay) return;

    modalOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  openModalButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      const startTab = button.getAttribute('data-open-modal');
      openModal(startTab);
    });
  });

  if (closeModalButton) {
    closeModalButton.addEventListener('click', closeModal);
  }

  if (modalOverlay) {
    modalOverlay.addEventListener('click', function (e) {
      if (e.target === modalOverlay) {
        closeModal();
      }
    });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeModal();
    }
  });

  // ---- 3. Switching between Login and Sign Up tabs ----
  const tabs = document.querySelectorAll('.modal-tab');
  const forms = document.querySelectorAll('.modal-form');

  function switchTab(tabName) {
    tabs.forEach(function (tab) {
      tab.classList.toggle('active', tab.dataset.tab === tabName);
    });

    forms.forEach(function (form) {
      form.classList.toggle('active', form.dataset.form === tabName);
    });
  }

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      switchTab(tab.dataset.tab);
    });
  });

  // ---- 3b. Logo click-to-enlarge popup ----
  const logoImg = document.getElementById('logoImg');
  const logoLightbox = document.getElementById('logoLightbox');

  if (logoImg && logoLightbox) {
    logoImg.addEventListener('click', function (e) {
      e.preventDefault();
      logoLightbox.classList.add('active');
    });

    logoLightbox.addEventListener('click', function () {
      logoLightbox.classList.remove('active');
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        logoLightbox.classList.remove('active');
      }
    });
  }

  // ---- 4. Placeholder form handling (Login and Sign Up now submit for real) ----
  document.querySelectorAll('.modal-form, .contact-form').forEach(function (form) {
  if (form.dataset.form === 'signup' || form.dataset.form === 'login' || form.classList.contains('contact-form')) return;
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    alert('Thanks! This is a working demo form. It will send real messages once the backend is connected.');
  });
});

  // ---- 5. Handle signup error redirects from signup-process.php ----
  const urlParams = new URLSearchParams(window.location.search);

const contactSuccess = urlParams.get('contact_success');
if (contactSuccess === '1') {
  alert('Thank you! Your message has been sent. We will get back to you soon.');
}
const contactError = urlParams.get('contact_error');
if (contactError === '1') {
  alert('Please fill in all required fields before sending your message.');
}

const signupError = urlParams.get('signup_error');

  if (signupError === 'duplicate') {
    openModal('signup');
    alert('That email is already registered. Please log in instead.');
  } else if (signupError === 'general') {
    openModal('signup');
    alert('Something went wrong creating your account. Please try again.');
  }

  const loginError = urlParams.get('login_error');
  if (loginError === 'wrong_password') {
    openModal('login');
    alert('Incorrect password. Please try again.');
  } else if (loginError === 'notfound') {
    openModal('login');
    alert('No account found with that email.');
  } else if (loginError === 'blocked') {
    openModal('login');
    alert('Your account has been blocked. Please contact Level Up Educational Centre for assistance.');
  }

});
