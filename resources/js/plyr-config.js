// Import Plyr
import Plyr from 'plyr';
import 'plyr/dist/plyr.css';

// Initialize Plyr on all video players
document.addEventListener('DOMContentLoaded', () => {
    const players = Array.from(document.querySelectorAll('.plyr-video')).map(
        (player) => new Plyr(player, {
            controls: [
                'play-large',
                'play',
                'progress',
                'current-time',
                'mute',
                'volume',
                'captions',
                'settings',
                'pip',
                'airplay',
                'fullscreen',
            ],
            settings: ['captions', 'quality', 'speed'],
            quality: {
                default: 1080,
                options: [4320, 2880, 2160, 1440, 1080, 720, 576, 480, 360, 240],
            },
            speed: {
                selected: 1,
                options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2],
            },
            i18n: {
                restart: 'Reiniciar',
                rewind: 'Retroceder {seektime}s',
                play: 'Reproducir',
                pause: 'Pausar',
                fastForward: 'Adelantar {seektime}s',
                seek: 'Buscar',
                seekLabel: '{currentTime} de {duration}',
                played: 'Reproducido',
                buffered: 'Almacenado',
                currentTime: 'Tiempo actual',
                duration: 'Duración',
                volume: 'Volumen',
                mute: 'Silenciar',
                unmute: 'Activar sonido',
                enableCaptions: 'Activar subtítulos',
                disableCaptions: 'Desactivar subtítulos',
                download: 'Descargar',
                enterFullscreen: 'Pantalla completa',
                exitFullscreen: 'Salir de pantalla completa',
                frameTitle: 'Reproductor de {title}',
                captions: 'Subtítulos',
                settings: 'Configuración',
                pip: 'Picture-in-Picture',
                menuBack: 'Volver al menú anterior',
                speed: 'Velocidad',
                normal: 'Normal',
                quality: 'Calidad',
                loop: 'Repetir',
            },
        })
    );
});

export default Plyr;
