import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';

(function(){
  function init(root){
    if (!root || root.dataset.cbTcInit) return;
    root.dataset.cbTcInit = '1';

    const prevEl = root.querySelector('.swiper-button-prev');
    const nextEl = root.querySelector('.swiper-button-next');

    const s = new Swiper(root, {
      modules: [Navigation],
      slidesPerView: 3,
      spaceBetween: 16,
      slidesPerGroup: 1,
      navigation: prevEl && nextEl ? { prevEl, nextEl } : undefined,
      breakpoints: { 0:{ slidesPerView: 1 }, 721:{ slidesPerView: 3 } },
      watchOverflow: true,
      autoHeight: true,
      observer: true,
      observeParents: true
    });

    setTimeout(() => s.update(), 0);
  }

  function boot(){ document.querySelectorAll('.cb-tc.swiper').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
})();
