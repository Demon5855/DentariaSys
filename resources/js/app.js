import './bootstrap';

import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es.js';
import 'flatpickr/dist/flatpickr.css';

window.Alpine = Alpine;

Alpine.start();

/**
 * Fuerza el formato dd/mm/aaaa en TODOS los campos de fecha del sistema,
 * sin importar el idioma/región configurado en el navegador del usuario.
 *
 * Por qué: un <input type="date"> nativo muestra su calendario en el
 * formato del sistema operativo/navegador (mm/dd/aaaa en un Windows en
 * inglés, dd/mm/aaaa en uno en español) — el atributo `lang="es"` del
 * documento NO alcanza para forzar esto en Chrome/Edge. flatpickr toma
 * control total de la UI del calendario, así que el formato es siempre
 * el mismo sin importar cómo esté configurado el equipo del usuario.
 *
 * El input original (con su `name` de siempre, incluso los generados
 * dinámicamente por Alpine en el formulario de consulta) sigue enviando
 * la fecha en formato Y-m-d al backend — flatpickr solo cambia lo que
 * el usuario VE y escribe (dd/mm/aaaa vía `altInput`), no lo que se
 * envía en el POST. `updateValue()` de flatpickr dispara los eventos
 * 'input'/'change' nativos sobre el input original, así que un
 * `x-model` de Alpine (como en las fechas de tratamiento) sigue
 * sincronizándose sin cambios adicionales.
 */
Spanish.firstDayOfWeek = 1; // la semana empieza en lunes
flatpickr.localize(Spanish);

function inicializarSelectorDeFecha(input) {
    if (input.dataset.flatpickrInit) {
        return;
    }
    input.dataset.flatpickrInit = 'true';

    // Los <input type="date"> nativos ignoran cualquier formato de
    // visualización que le pidamos — flatpickr necesita un input de
    // texto normal para poder mostrar dd/mm/aaaa de verdad.
    input.type = 'text';
    input.setAttribute('autocomplete', 'off');
    if (!input.placeholder) {
        input.placeholder = 'dd/mm/aaaa';
    }

    flatpickr(input, {
        dateFormat: 'Y-m-d', // lo que se envía al servidor, sin cambios
        altInput: true,
        altFormat: 'd/m/Y', // lo que ve y escribe el usuario
        allowInput: true,
        disableMobile: true, // en móvil el calendario propio es más consistente que el del SO
    });
}

function inicializarSelectoresDeFechaEn(raiz) {
    if (raiz.matches?.('input[type="date"]')) {
        inicializarSelectorDeFecha(raiz);
    }
    raiz.querySelectorAll?.('input[type="date"]').forEach(inicializarSelectorDeFecha);
}

document.addEventListener('DOMContentLoaded', () => inicializarSelectoresDeFechaEn(document));

// El formulario de consulta agrega tratamientos (con sus propios campos
// de fecha) dinámicamente vía Alpine después de la carga inicial — este
// observer los detecta y les aplica el mismo selector de fecha.
new MutationObserver((mutaciones) => {
    for (const mutacion of mutaciones) {
        mutacion.addedNodes.forEach((nodo) => {
            if (nodo.nodeType === Node.ELEMENT_NODE) {
                inicializarSelectoresDeFechaEn(nodo);
            }
        });
    }
}).observe(document.body, { childList: true, subtree: true });
