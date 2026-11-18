/**
 * x-geser
 * Membuat daftar horizontal (galeri foto, slot jadwal, kartu koleksi) bisa
 * digeser dengan cara klik lalu tarik memakai mouse. Di layar sentuh tetap
 * memakai scroll bawaan browser.
 *
 * Contoh: <div x-data x-geser class="flex overflow-x-auto">...</div>
 */
export default function geser(el, {}, { cleanup }) {
    el.classList.add('bisa-digeser');

    let sedangTekan = false;
    let sudahBergeser = false;
    let mulaiX = 0;
    let scrollAwal = 0;

    const tekan = (event) => {
        if (event.pointerType !== 'mouse' || event.button !== 0) return;
        sedangTekan = true;
        sudahBergeser = false;
        mulaiX = event.clientX;
        scrollAwal = el.scrollLeft;
    };

    const gerak = (event) => {
        if (!sedangTekan) return;
        const jarak = event.clientX - mulaiX;

        // Baru dianggap menggeser kalau mouse sudah pindah lebih dari 5px,
        // supaya klik biasa pada link di dalamnya tetap jalan
        if (!sudahBergeser && Math.abs(jarak) > 5) {
            sudahBergeser = true;
            el.classList.add('sedang-digeser');
        }

        if (sudahBergeser) {
            el.scrollLeft = scrollAwal - jarak;
        }
    };

    const lepas = () => {
        sedangTekan = false;
        el.classList.remove('sedang-digeser');
    };

    // Batalkan klik yang terjadi tepat setelah menggeser
    const cegahKlik = (event) => {
        if (sudahBergeser) {
            event.preventDefault();
            event.stopPropagation();
            sudahBergeser = false;
        }
    };

    el.addEventListener('pointerdown', tekan);
    window.addEventListener('pointermove', gerak);
    window.addEventListener('pointerup', lepas);
    el.addEventListener('click', cegahKlik, true);

    cleanup(() => {
        el.removeEventListener('pointerdown', tekan);
        window.removeEventListener('pointermove', gerak);
        window.removeEventListener('pointerup', lepas);
        el.removeEventListener('click', cegahKlik, true);
    });
}
