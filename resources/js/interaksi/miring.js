/**
 * x-miring
 * Efek kartu sedikit miring mengikuti posisi mouse (3D tilt ringan),
 * dipakai pada kartu koleksi dan kartu statistik. Tidak aktif di layar
 * sentuh dan saat pengguna memilih "kurangi gerakan".
 *
 * Contoh: <article x-data x-miring>...</article>  atau  x-miring="6" (derajat maks)
 */
export default function miring(el, { expression }, { cleanup }) {
    const kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const layarSentuh = window.matchMedia('(hover: none)').matches;

    if (kurangiGerak || layarSentuh) {
        return;
    }

    const maks = parseFloat(expression) || 4;
    el.style.transition = 'transform 0.25s ease-out';
    el.style.transformStyle = 'preserve-3d';
    el.style.willChange = 'transform';

    const gerak = (event) => {
        const kotak = el.getBoundingClientRect();
        const x = (event.clientX - kotak.left) / kotak.width - 0.5;
        const y = (event.clientY - kotak.top) / kotak.height - 0.5;
        el.style.transform = `perspective(900px) rotateX(${(-y * maks).toFixed(2)}deg) rotateY(${(x * maks).toFixed(2)}deg) translateY(-4px)`;
    };

    const keluar = () => {
        el.style.transform = '';
    };

    el.addEventListener('pointermove', gerak);
    el.addEventListener('pointerleave', keluar);

    cleanup(() => {
        el.removeEventListener('pointermove', gerak);
        el.removeEventListener('pointerleave', keluar);
    });
}
