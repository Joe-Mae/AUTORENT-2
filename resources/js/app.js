// Le CSS des icônes passe par JS (et non par le SASS) : c'est ce qui
// permet à Vite d'émettre les fichiers de police dans public/build.
import 'bootstrap-icons/font/bootstrap-icons.css';

import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// --- Compteurs animés (éléments [data-counter]) ---
//
// La valeur finale est déjà rendue côté serveur : sans JavaScript (ou si
// le déclenchement échoue dans un navigateur embarqué), le bon chiffre
// s'affiche. L'animation n'est qu'un embellissement au défilement.

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
setTimeout(launchVisibleCounters, 200);

// --- Barre de navigation : ombre renforcée après le haut de page ---

const navbar = document.querySelector('.navbar-public');

if (navbar) {
    const toggleShadow = () => navbar.classList.toggle('is-scrolled', window.scrollY > 12);
    toggleShadow();
    window.addEventListener('scroll', toggleShadow, { passive: true });
}
