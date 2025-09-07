import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';

(function(){
  function init(container){
    if (!container || container.dataset.insightsInit) return;
    container.dataset.insightsInit = '1';
    let instance = null;
    const mql = window.matchMedia('(max-width: 720px)');
    const grid = container.querySelector('.ceiba-insights-grid');

    function mount(){
      if (instance || !mql.matches) return;
      container.classList.add('swiper');
      if (grid) grid.classList.add('swiper-wrapper');
      container.querySelectorAll('.ceiba-insight-card').forEach((el) => el.classList.add('swiper-slide'));
      const slideCount = container.querySelectorAll('.ceiba-insight-card').length;
      if (slideCount < 2) return; // no controls or swiper when fewer than 2
      const prevEl = container.querySelector('.swiper-button-prev');
      const nextEl = container.querySelector('.swiper-button-next');
      instance = new Swiper(container, {
        modules: [Navigation, Pagination],
        navigation: prevEl && nextEl ? { prevEl, nextEl } : undefined,
        loop: false,
        initialSlide: 0,
        centeredSlides: false,
        slidesPerView: 1,
        spaceBetween: 12,
        pagination: { el: container.querySelector('.swiper-pagination'), clickable: true },
        allowTouchMove: true,
        watchOverflow: true,
        observer: true,
        observeParents: true,
      });
      container.classList.add('is-swiper-init');
    }

    function unmount(){
      if (!instance) return;
      try { instance.destroy(true, true); } catch(e){}
      instance = null;
      container.classList.remove('is-swiper-init');
      container.classList.remove('swiper');
      if (grid) grid.classList.remove('swiper-wrapper');
      container.querySelectorAll('.ceiba-insight-card').forEach((el) => el.classList.remove('swiper-slide'));
    }

    function sync(){
      if (mql.matches) mount();
      else unmount();
    }

    sync();
    mql.addEventListener ? mql.addEventListener('change', sync) : window.addEventListener('resize', sync);
  }

  function boot(){ document.querySelectorAll('.wp-block-ceiba-insights-grid .ceiba-insights__inner').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
})();
