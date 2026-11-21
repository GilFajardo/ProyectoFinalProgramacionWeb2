/**
 * Gráficas de la página pública de estadísticas (Chart.js).
 * Cada <canvas data-grafica="tipo" data-etiquetas='[...]' data-valores='[...]'> se dibuja aquí;
 * los datos los inyecta el servidor como JSON escapado.
 */
import {
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    Chart,
    DoughnutController,
    Filler,
    Legend,
    LinearScale,
    LineController,
    LineElement,
    PointElement,
    Tooltip,
} from 'chart.js';

Chart.register(
    ArcElement, BarController, BarElement, CategoryScale, DoughnutController, Filler,
    Legend, LinearScale, LineController, LineElement, PointElement, Tooltip,
);

const MARCA = '#0b3c7a';
const ESTATUS = ['#f59e0b', '#10b981', '#ef4444'];

export function iniciarGraficas() {
    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.color = '#475569';

    document.querySelectorAll('canvas[data-grafica]').forEach((canvas) => {
        const tipo = canvas.dataset.grafica;
        const etiquetas = JSON.parse(canvas.dataset.etiquetas);
        const valores = JSON.parse(canvas.dataset.valores);

        const configuraciones = {
            barras: {
                type: 'bar',
                data: { labels: etiquetas, datasets: [{ label: canvas.dataset.titulo, data: valores, backgroundColor: MARCA, borderRadius: 6 }] },
                options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } },
            },
            dona: {
                type: 'doughnut',
                data: { labels: etiquetas, datasets: [{ data: valores, backgroundColor: ESTATUS, borderWidth: 0 }] },
                options: { cutout: '62%', plugins: { legend: { position: 'bottom' } } },
            },
            linea: {
                type: 'line',
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: canvas.dataset.titulo,
                        data: valores,
                        borderColor: MARCA,
                        backgroundColor: 'rgba(11, 60, 122, 0.12)',
                        fill: true,
                        tension: 0.35,
                    }],
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
            },
        };

        new Chart(canvas, { ...configuraciones[tipo], options: { responsive: true, maintainAspectRatio: false, ...configuraciones[tipo].options } });
    });
}
