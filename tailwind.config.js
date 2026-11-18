import forms from '@tailwindcss/forms';

/**
 * Tema Tailwind diambil dari variables desain Figma "Royal Heritage".
 * Kalau warna atau ukuran teks di Figma berubah, cukup ubah di file ini.
 *
 * @type {import('tailwindcss').Config}
 */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        // Grid desain: konten maksimal 1280px, margin 80 (desktop), 40 (tablet), 20 (ponsel)
        screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            '2xl': '1440px',
        },

        extend: {
            colors: {
                putih: '#FFFFFF',
                ivory: '#F7F3EB',
                maroon: {
                    DEFAULT: '#702F36',
                    hover: '#5A242A',
                    lembut: '#F3E6E3',
                },
                cokelat: {
                    DEFAULT: '#3B2927',
                    600: '#6B5552',
                },
                emas: {
                    DEFAULT: '#C6A15B',
                    // emas/700 untuk teks di atas Ivory (kontras 5,3:1)
                    700: '#7E6029',
                },
                garis: {
                    DEFAULT: '#E2D7C6',
                    kuat: '#CDBFAA',
                },
                lembut: '#EFE7DA',
                foto: '#E6DCCB',
                'di-gelap': '#E9DFD0',
                status: {
                    'menunggu-teks': '#6A4F1E',
                    'menunggu-latar': '#F5ECD8',
                    'disetujui-teks': '#3F6B45',
                    'disetujui-latar': '#E6EFE4',
                    'ditolak-teks': '#A23A32',
                    'ditolak-latar': '#F8E4E1',
                    'dibatalkan-teks': '#6B5552',
                    'dibatalkan-latar': '#EFE7DA',
                },
            },

            fontFamily: {
                display: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },

            // Gaya teks Figma: [ukuran, { tinggi baris, jarak huruf }]
            fontSize: {
                'display-xl': ['128px', { lineHeight: '108px', letterSpacing: '-0.01em' }],
                'display-xl-ponsel': ['56px', { lineHeight: '50px', letterSpacing: '-0.01em' }],
                'display-l': ['72px', { lineHeight: '68px', letterSpacing: '-0.5px' }],
                'display-l-ponsel': ['40px', { lineHeight: '40px', letterSpacing: '-0.5px' }],
                h1: ['52px', { lineHeight: '56px' }],
                'h1-ponsel': ['34px', { lineHeight: '38px' }],
                h2: ['36px', { lineHeight: '42px' }],
                'h2-ponsel': ['28px', { lineHeight: '34px' }],
                h3: ['26px', { lineHeight: '30px' }],
                'h3-ponsel': ['22px', { lineHeight: '26px' }],
                angka: ['44px', { lineHeight: '44px' }],
                'angka-ponsel': ['36px', { lineHeight: '36px' }],
                'angka-sedang': ['24px', { lineHeight: '24px' }],
                lead: ['18px', { lineHeight: '30px' }],
                'lead-ponsel': ['17px', { lineHeight: '28px' }],
                isi: ['16px', { lineHeight: '26px' }],
                kecil: ['14px', { lineHeight: '21px' }],
                label: ['14px', { lineHeight: '18px' }],
                'label-kecil': ['12px', { lineHeight: '16px', letterSpacing: '0.01em' }],
                tombol: ['15px', { lineHeight: '16px', letterSpacing: '0.005em' }],
            },

            borderRadius: {
                kecil: '4px',
                input: '6px',
                kartu: '14px',
                banner: '24px',
                // Lengkung relung, hanya untuk foto koleksi
                relung: '999px 999px 14px 14px',
            },

            maxWidth: {
                konten: '1280px',
            },

            boxShadow: {
                dialog: '0 24px 28px rgba(59, 41, 38, 0.22)',
                angkat: '0 18px 32px -18px rgba(59, 41, 38, 0.45)',
            },

            backgroundImage: {
                kawung: "url('/images/ui/pola-kawung.png')",
                tumpal: "url('/images/ui/strip-tumpal.svg')",
            },

            keyframes: {
                'muncul-naik': {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'geser-masuk': {
                    '0%': { opacity: '0', transform: 'translateX(24px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
            },
            animation: {
                'muncul-naik': 'muncul-naik 0.5s ease-out both',
                'geser-masuk': 'geser-masuk 0.35s ease-out both',
            },
        },
    },

    plugins: [forms],
};
