import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';

(function(){
  function init(container){
    if (!container || container.dataset.cbTcInit) return;
    container.dataset.cbTcInit = '1';

    const wrapper = container.querySelector('.ceiba-ic__track');
    if (!wrapper) return;

    const slides = Array.from(container.querySelectorAll('.ceiba-ic__slide'));
    const slideCount = slides.length;

    container.classList.add('swiper');
    wrapper.classList.add('swiper-wrapper');
    slides.forEach((el) => el.classList.add('swiper-slide'));

    const prevEl = container.querySelector('.swiper-button-prev');
    const nextEl = container.querySelector('.swiper-button-next');

    const s = new Swiper(container, {
      modules: [Navigation],
      spaceBetween: 16,
      centeredSlides: false,
      loop: false,
      slidesPerGroup: 1,
      navigation: prevEl && nextEl ? { prevEl, nextEl } : undefined,
      breakpoints: {
        0:    { slidesPerView: 1.15, slidesOffsetBefore: 16, slidesOffsetAfter: 16, spaceBetween: 12 },
        768:  { slidesPerView: 2.15, slidesOffsetBefore: 24,  slidesOffsetAfter: 24, spaceBetween: 16 },
        1025: { slidesPerView: 3, slidesOffsetBefore: 24,  slidesOffsetAfter: 24, spaceBetween: 16 },
        1300: { slidesPerView: 3, slidesOffsetBefore: 0,  slidesOffsetAfter: 0, spaceBetween: 16 }
      },
      watchOverflow: true,
      autoHeight: true,
      observer: true,
      observeParents: true,
      loopAdditionalSlides: 0,
    });

    const navWrap = container.querySelector('.swiper-nav');
    function updateNavVisibility(){
      if (!navWrap) return;
      const spv = typeof s.params.slidesPerView === 'number' ? s.params.slidesPerView : 1;
      const show = slideCount > spv;
      navWrap.style.display = show ? '' : 'none';
    }
    s.on('init', updateNavVisibility);
    s.on('breakpoint', updateNavVisibility);
    s.on('resize', updateNavVisibility);
    updateNavVisibility();
  }

  function boot(){ document.querySelectorAll('.wp-block-ceiba-case-studies-carousel .ceiba-ic__inner').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
})();

import './view.scss';
