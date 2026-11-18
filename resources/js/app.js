import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

import geser from './interaksi/geser';
import muncul from './interaksi/muncul';
import miring from './interaksi/miring';
import konfirmasi from './interaksi/konfirmasi';

window.Alpine = Alpine;

// Plugin focus dipakai untuk menjebak fokus keyboard di dalam dialog
Alpine.plugin(focus);

// Directive buatan sendiri untuk interaksi ringan
Alpine.directive('geser', geser);
Alpine.directive('muncul', muncul);
Alpine.directive('miring', miring);

// Store global untuk dialog konfirmasi (hapus, setujui, tolak, batalkan)
Alpine.store('konfirmasi', konfirmasi);

Alpine.start();
