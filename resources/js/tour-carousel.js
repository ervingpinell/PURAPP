// resources/js/tour-carousel.js

document.addEventListener('DOMContentLoaded', () => {
  const carouselEl = document.getElementById('tourCarousel')
  if (!carouselEl) return

  // Instancia de Bootstrap Carousel
  const bsCarousel = bootstrap.Carousel.getOrCreateInstance(carouselEl, {
    interval: 5000,  // cambia de slide cada 5 s
    pause: 'hover'   // pausa cuando pongas el puntero encima
  })

  // Miniaturas en desktop → clic para mover el slide
  const thumbs = carouselEl.querySelectorAll('.thumb-box img')
  thumbs.forEach((thumb, idx) => {
    thumb.addEventListener('click', () => {
      bsCarousel.to(idx)
    })
  })

  // Al cambiar de slide → actualiza la miniatura activa
  carouselEl.addEventListener('slide.bs.carousel', e => {
    thumbs.forEach(t => t.classList.remove('active'))
    thumbs[e.to].classList.add('active')
  })
})
