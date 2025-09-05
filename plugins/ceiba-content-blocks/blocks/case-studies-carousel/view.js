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
      // Infinite loop with centered slides and peeking neighbors
      loop: true,
      centeredSlides: true,
      spaceBetween: 16,
      slidesPerGroup: 1,
      navigation: prevEl && nextEl ? { prevEl, nextEl } : undefined,
      breakpoints: {
        0:   { slidesPerView: 1.15 },
        721: { slidesPerView: 3.15 }
      },
      watchOverflow: true,
      autoHeight: true,
      observer: true,
      observeParents: true,
      loopAdditionalSlides: 6,
    });

    setTimeout(() => s.update(), 0);
  }

  function boot(){ document.querySelectorAll('.cb-tc.swiper').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
})();

import './view.scss';

