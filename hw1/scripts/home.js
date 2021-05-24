//Carousel
window.onload = function () {
  const slides = document.getElementsByClassName('carousel-item'),
    addActive = function (slide) { slide.classList.add('active') },
    removeActive = function (slide) { slide.classList.remove('active') };
  if (slides.length <= 1)
    return;

  addActive(slides[0]);

  setInterval(function () {
    for (let i = 0; i < slides.length; i++) {
      if (i + 1 === slides.length) {
        addActive(slides[0]);
        slides[0].style.zIndex = 100;
        setTimeout(removeActive, 350, slides[i]);
        break;
      }
      if (slides[i].classList.contains('active')) {
        slides[i].removeAttribute('style');
        setTimeout(removeActive, 350, slides[i]);
        addActive(slides[i + 1]);
        break;
      }
    }
  }, 3000);
}
