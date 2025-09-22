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

    // Add swiper classes right before init so Swiper recognizes structure
    container.classList.add('swiper');
    wrapper.classList.add('swiper-wrapper');
    slides.forEach((el) => el.classList.add('swiper-slide'));

    const prevEl = container.querySelector('.swiper-button-prev');
    const nextEl = container.querySelector('.swiper-button-next');
    const navWrap = container.querySelector('.swiper-nav');

    // Determine slidesPerView per breakpoint based on how many slides there are
    const spvLg = Math.min(3, Math.max(1, slideCount));
    const spvMd = slideCount < 2 ? 1 : Math.min(2, slideCount);
    const spvSm = slideCount < 3 ? 1 : 1.15; // stretch for 1–2 items

    const s = new Swiper(container, {
      modules: [Navigation],
      slidesPerView: spvLg,
      spaceBetween: 16,
      centeredSlides: false,
      loop: false,
      initialSlide: 0,
      navigation: prevEl && nextEl ? { prevEl, nextEl } : undefined,
      breakpoints: {
        0:    { slidesPerView: spvSm, slidesOffsetBefore: 16, slidesOffsetAfter: 16, spaceBetween: 12 },
        768:  { slidesPerView: slideCount >= 3 ? 2.15 : spvMd, slidesOffsetBefore: 24,  slidesOffsetAfter: 24, spaceBetween: 16 },
        1025: { slidesPerView: spvLg, slidesOffsetBefore: 24,  slidesOffsetAfter: 24, spaceBetween: 16 },
        1300: { slidesPerView: spvLg, slidesOffsetBefore: 0,  slidesOffsetAfter: 0, spaceBetween: 16 }
      },
      watchOverflow: true,
      observer: true,
      observeParents: true,
    });

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

  function boot(){ document.querySelectorAll('.wp-block-ceiba-testimonial-carousel .ceiba-tc__inner').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
})();
