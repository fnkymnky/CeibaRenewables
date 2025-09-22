import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';

(function(){
  function init(root){
    if (!root || root.dataset.icInit) return;
    root.dataset.icInit = '1';
    const container = root; // swiper container on inner wrapper
    const wrapper = container.querySelector('.ceiba-ic__track');
    if (!wrapper) return;

    const slides = Array.from(container.querySelectorAll('.ceiba-ic__slide'));
    const slideCount = slides.length;

    container.classList.add('swiper');
    wrapper.classList.add('swiper-wrapper');
    slides.forEach((el) => el.classList.add('swiper-slide'));

    const prevEl = container.querySelector('.swiper-button-prev');
    const nextEl = container.querySelector('.swiper-button-next');

    // Determine slidesPerView per breakpoint based on how many slides there are
    const spvLg = Math.min(3, Math.max(1, slideCount));
    const spvMd = slideCount < 2 ? 1 : Math.min(2, slideCount);
    const spvSm = slideCount < 3 ? 1 : 1.15; // stretch for 1–2 items

    const s = new Swiper(container, {
      modules: [Navigation],
      spaceBetween: 16,
      centeredSlides: false,
      loop: false,
      slidesPerGroup: 1,
      navigation: prevEl && nextEl ? { prevEl, nextEl } : undefined,
      breakpoints: {
        0:    { slidesPerView: spvSm, slidesOffsetBefore: 16, slidesOffsetAfter: 16, spaceBetween: 12 },
        768:  { slidesPerView: slideCount >= 3 ? 2.15 : spvMd, slidesOffsetBefore: 24,  slidesOffsetAfter: 24, spaceBetween: 16 },
        1025: { slidesPerView: spvLg, slidesOffsetBefore: 24,  slidesOffsetAfter: 24, spaceBetween: 16 },
        1300: { slidesPerView: spvLg, slidesOffsetBefore: 0,  slidesOffsetAfter: 0, spaceBetween: 16 }
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

  function boot(){ document.querySelectorAll('.wp-block-ceiba-insights-carousel .ceiba-ic__inner').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
})();
