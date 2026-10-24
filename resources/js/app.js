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

// --- Formulaire de réservation : étapes et prix indicatif en direct ---
//
// Le prix affiché n'est qu'indicatif : le serveur recalcule tout à la
// validation (seule source de vérité, spécification §5.4).

const bookingForm = document.getElementById('booking-form');

if (bookingForm) {
    const steps = [...bookingForm.querySelectorAll('.booking-step')];
    const nextButton = document.querySelector('[data-step-next]');
    const prevButton = document.querySelector('[data-step-prev]');
    let currentStep = 1;

    const vehicleDaily = parseFloat(document.querySelector('[data-vehicle-price]')?.dataset.vehiclePrice ?? '0');
    const driverDaily = parseFloat(bookingForm.dataset.driverPrice ?? '0');
    const currency = bookingForm.dataset.currency ?? 'FCFA';

    const formatAmount = (amount) => `${Math.round(amount).toLocaleString('fr-FR')} ${currency}`;

    const refreshSummary = () => {
        const pickupDate = bookingForm.pickup_date.value;
        const returnDate = bookingForm.return_date.value;
        const time = bookingForm.pickup_time.value || '10:00';
        const withDriver = bookingForm.type.value === 'vehicle_with_driver';

        const summary = (name) => bookingForm.querySelector(`[data-summary="${name}"]`);

        if (!pickupDate || !returnDate) return;

        const start = new Date(`${pickupDate}T${time}`);
        const end = new Date(`${returnDate}T${time}`);
        const days = Math.max(1, Math.ceil((end - start) / 86_400_000));
        const total = (vehicleDaily + (withDriver ? driverDaily : 0)) * days;

        summary('dates').textContent = `${start.toLocaleDateString('fr-FR')} → ${end.toLocaleDateString('fr-FR')}`;
        summary('days').textContent = days;
        summary('vehicle-price').textContent = `${formatAmount(vehicleDaily)} / j`;
        summary('driver-price').textContent = `${formatAmount(withDriver ? driverDaily : 0)} / j`;
        summary('total').textContent = formatAmount(total);

        bookingForm.querySelector('[data-summary-row="driver-price"]').classList.toggle('d-none', !withDriver);
    };

    const showStep = (step) => {
        currentStep = step;
        steps.forEach((element) => { element.hidden = Number(element.dataset.step) !== step; });
        nextButton.hidden = step === steps.length;
        prevButton.hidden = step === 1;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    nextButton.addEventListener('click', () => {
        // Validation navigateur des champs visibles avant de continuer.
        const fields = steps[currentStep - 1].querySelectorAll('input, select, textarea');
        for (const field of fields) {
            if (!field.checkValidity()) {
                field.reportValidity();
                return;
            }
        }
        showStep(Math.min(currentStep + 1, steps.length));
    });

    prevButton.addEventListener('click', () => showStep(Math.max(currentStep - 1, 1)));

    // Affiche le nom du fichier choisi à côté de chaque upload.
    bookingForm.querySelectorAll('input[type="file"]').forEach((input) => {
        input.addEventListener('change', () => {
            const label = bookingForm.querySelector(`[data-file-label="${input.name}"]`);
            if (label) label.textContent = input.files[0]?.name ?? label.dataset.default ?? '';
        });
    });

    ['pickup_date', 'return_date', 'pickup_time'].forEach((name) => {
        bookingForm[name].addEventListener('change', refreshSummary);
    });
    bookingForm.querySelectorAll('input[name="type"]').forEach((radio) => {
        radio.addEventListener('change', refreshSummary);
    });

    // En cas d'erreur serveur, toutes les étapes sont affichées pour
    // que le client voie les champs concernés.
    if (bookingForm.querySelector('.is-invalid, .text-danger')) {
        steps.forEach((element) => { element.hidden = false; });
        nextButton.hidden = true;
        prevButton.hidden = true;
    }

    refreshSummary();
}
