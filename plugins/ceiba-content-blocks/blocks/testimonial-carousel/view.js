import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';

(function(){
  function init(root){
    if (!root || root.dataset.tcInit) return;
    root.dataset.tcInit = '1';
    const container = root; // swiper container on inner wrapper
    const wrapper = container.querySelector('.ceiba-tc__track');
    if (!wrapper) return;

    const slides = Array.from(container.querySelectorAll('.ceiba-tc__slide'));
    const slideCount = slides.length;

    if (slideCount <= 3) {
      // Ensure no stray swiper classes are present when not initialized
      container.classList.remove('swiper');
      wrapper.classList.remove('swiper-wrapper');
      slides.forEach((el) => el.classList.remove('swiper-slide'));
      return;
    }

    // Add swiper classes right before init so Swiper recognizes structure
    container.classList.add('swiper');
    wrapper.classList.add('swiper-wrapper');
    slides.forEach((el) => el.classList.add('swiper-slide'));

    const prevEl = container.querySelector('.swiper-button-prev');
    const nextEl = container.querySelector('.swiper-button-next');

    new Swiper(container, {
      modules: [Navigation],
      slidesPerView: 3,
      spaceBetween: 12,
      centeredSlides: false,
      loop: false,
      grabCursor: true,
      initialSlide: 0,
      navigation: prevEl && nextEl ? { prevEl, nextEl } : undefined,
      breakpoints: {
        0:    { slidesPerView: 1.15, centeredSlides: true,  slidesOffsetBefore: 16, slidesOffsetAfter: 16, spaceBetween: 12 },
        1025: { slidesPerView: 3,    centeredSlides: false, slidesOffsetBefore: 0,  slidesOffsetAfter: 0 }
      },
      watchOverflow: true,
      observer: true,
      observeParents: true,
    });
  }

  function boot(){ document.querySelectorAll('.wp-block-ceiba-testimonial-carousel .ceiba-tc__inner').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
})();
