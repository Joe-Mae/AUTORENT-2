// Le CSS des icônes passe par JS (et non par le SASS) : c'est ce qui
// permet à Vite d'émettre les fichiers de police dans public/build.
import 'bootstrap-icons/font/bootstrap-icons.css';

import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// Les sections [data-reveal] ne sont masquées que si le JS tourne :
// sans JS (ou en cas d'erreur), tout le contenu reste visible.
document.documentElement.classList.add('js-anim');

// --- Révélation des sections au défilement ---

const revealObserver = new IntersectionObserver((entries) => {
    for (const entry of entries) {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            revealObserver.unobserve(entry.target);
        }
    }
}, { threshold: 0.15 });

document.querySelectorAll('[data-reveal]').forEach((element) => {
    revealObserver.observe(element);
});

// --- Compteurs animés (éléments [data-counter]) ---

const easeOut = (t) => 1 - Math.pow(1 - t, 3);

const animateCounter = (element) => {
    const target = Number(element.dataset.counter);
    const duration = 1600;
    const start = performance.now();

    const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        element.textContent = Math.round(easeOut(progress) * target).toLocaleString('fr-FR');
        if (progress < 1) requestAnimationFrame(tick);
    };

    requestAnimationFrame(tick);
};

const counterObserver = new IntersectionObserver((entries) => {
    for (const entry of entries) {
        if (entry.isIntersecting) {
            animateCounter(entry.target);
            counterObserver.unobserve(entry.target);
        }
    }
}, { threshold: 0.4 });

document.querySelectorAll('[data-counter]').forEach((element) => {
    counterObserver.observe(element);
});

// --- Barre de navigation : ombre renforcée après le haut de page ---

const navbar = document.querySelector('.navbar-public');

if (navbar) {
    const toggleShadow = () => navbar.classList.toggle('is-scrolled', window.scrollY > 12);
    toggleShadow();
    window.addEventListener('scroll', toggleShadow, { passive: true });
}
