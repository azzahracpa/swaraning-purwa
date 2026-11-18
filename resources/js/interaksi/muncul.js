/**
 * x-muncul
 * Elemen muncul perlahan (fade + naik sedikit) saat masuk ke layar.
 * Bisa diberi jeda dalam milidetik: x-muncul="150"
 */
export default function muncul(el, { expression }, { cleanup }) {
    const kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (kurangiGerak || !('IntersectionObserver' in window)) {
        return;
    }

    const jeda = parseInt(expression, 10) || 0;
    el.classList.add('belum-muncul');

    const pengamat = new IntersectionObserver(
        (entri) => {
            entri.forEach((item) => {
                if (item.isIntersecting) {
                    el.style.transitionDelay = `${jeda}ms`;
                    el.classList.add('sudah-muncul');
                    el.classList.remove('belum-muncul');
                    pengamat.unobserve(el);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
    );

    pengamat.observe(el);
    cleanup(() => pengamat.disconnect());
}
