/**
 * Punto de entrada de JavaScript del sitio público y los portales.
 * - Alpine.js: menús responsivos, acordeones, carrusel, formularios dinámicos del CV.
 * - Validación en cliente: mensajes en español sobre la validación nativa de HTML5
 *   (la validación definitiva siempre ocurre en el servidor con Form Requests).
 */
import Alpine from 'alpinejs';
import { iniciarValidacion } from './validacion';

window.Alpine = Alpine;

/**
 * Carrusel del banner principal: avanza solo y se pausa al pasar el cursor.
 */
Alpine.data('carrusel', (total, intervalo = 6000) => ({
    actual: 0,
    total,
    temporizador: null,
    iniciar() {
        this.reanudar();
    },
    ir(indice) {
        this.actual = (indice + this.total) % this.total;
    },
    siguiente() {
        this.ir(this.actual + 1);
    },
    anterior() {
        this.ir(this.actual - 1);
    },
    pausar() {
        clearInterval(this.temporizador);
    },
    reanudar() {
        this.pausar();
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.temporizador = setInterval(() => this.siguiente(), intervalo);
        }
    },
}));

/**
 * Contador de caracteres para textareas con maxlength.
 * Recibe el texto inicial para que x-model no borre el contenido existente.
 */
Alpine.data('contador', (maximo, inicial = '') => ({
    texto: inicial ?? '',
    maximo,
    get restantes() {
        return this.maximo - this.texto.length;
    },
}));

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    iniciarValidacion();

    // Las gráficas solo se cargan en la página de estadísticas (import dinámico = menos JS en el resto).
    if (document.querySelector('[data-grafica]')) {
        import('./estadisticas').then(({ iniciarGraficas }) => iniciarGraficas());
    }
});
