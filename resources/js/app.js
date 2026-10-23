// Le CSS des icônes passe par JS (et non par le SASS) : c'est ce qui
// permet à Vite d'émettre les fichiers de police dans public/build.
import 'bootstrap-icons/font/bootstrap-icons.css';

import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// Les sections [data-reveal] ne sont masquées que si le JS tourne :
// sans JS (ou en cas d'erreur), tout le contenu reste visible.
document.documentElement.classList.add('js-anim');

// --- Révélation des sections au défilement ---
//
// Déclenchée par l'événement « scroll » + getBoundingClientRect, sans
// IntersectionObserver ni requestAnimationFrame : ces API se mettent en
// pause dans certains navigateurs embarqués (webviews), ce qui laissait
// les sections invisibles.

const revealElements = [...document.querySelectorAll('[data-reveal]')];

const revealNearViewport = () => {
    const limit = window.innerHeight + 150;

    for (const element of revealElements) {
        if (element.classList.contains('revealed')) continue;

        if (element.getBoundingClientRect().top < limit) {
            element.classList.add('revealed');
        }
    }
};

window.addEventListener('scroll', revealNearViewport, { passive: true });
window.addEventListener('resize', revealNearViewport, { passive: true });
setTimeout(revealNearViewport, 50);

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

const counterElements = [...document.querySelectorAll('[data-counter]')];

const launchVisibleCounters = () => {
    for (const element of counterElements) {
        if (element.dataset.done) continue;

        if (element.getBoundingClientRect().top < window.innerHeight + 100) {
            element.dataset.done = '1';
            animateCounter(element);
        }
    }
};

window.addEventListener('scroll', launchVisibleCounters, { passive: true });
window.addEventListener('resize', launchVisibleCounters, { passive: true });
setTimeout(launchVisibleCounters, 50);

// --- Barre de navigation : ombre renforcée après le haut de page ---

const navbar = document.querySelector('.navbar-public');

if (navbar) {
    const toggleShadow = () => navbar.classList.toggle('is-scrolled', window.scrollY > 12);
    toggleShadow();
    window.addEventListener('scroll', toggleShadow, { passive: true });
}
