(function(){
  function whenSwiperReady(cb){
    if (window.Swiper) return cb(window.Swiper);
    var tries = 0;
    (function wait(){
      if (window.Swiper) return cb(window.Swiper);
      if (tries++ > 120) return; // ~3s cap
      setTimeout(wait, 25);
    })();
  }

  function init(container){
    if (!container || container.dataset.plInit) return;
    container.dataset.plInit = '1';
    var instance = null;
    var mql = window.matchMedia('(max-width: 425px)');
    var wrapper = container.querySelector('.ceiba-services-grid');
    function mount(Sw){
      if (instance || !mql.matches) return;
      // Activate Swiper on mobile: add classes Swiper expects
      container.classList.add('swiper');
      if (wrapper) wrapper.classList.add('swiper-wrapper');
      container.querySelectorAll('.ceiba-service-card').forEach(function(card){ card.classList.add('swiper-slide'); });
      instance = new Sw(container, {
        // Infinite, centered, with a hint of adjacent slides visible
        loop: true,
        centeredSlides: true,
        slidesPerView: 1.15,
        spaceBetween: 12,
        pagination: { el: container.querySelector('.swiper-pagination'), clickable: true },
        watchOverflow: true,
        observer: true,
        observeParents: true,
        loopAdditionalSlides: 4,
      });
      container.classList.add('is-swiper-init');
    }
    function unmount(){
      if (!instance) return;
      try { instance.destroy(true, true); } catch(e){}
      instance = null;
      container.classList.remove('is-swiper-init');
      container.classList.remove('swiper');
      // Remove Swiper classes to prevent Swiper CSS from affecting desktop grid
      if (wrapper) wrapper.classList.remove('swiper-wrapper');
      container.querySelectorAll('.ceiba-service-card').forEach(function(card){ card.classList.remove('swiper-slide'); });
    }

    function sync(){
      if (mql.matches) {
        whenSwiperReady(mount);
      } else {
        unmount();
      }
    }

    sync();
    mql.addEventListener ? mql.addEventListener('change', sync) : window.addEventListener('resize', sync);
  }

  // Initialize on all Page List containers; JS will add/remove
  // Swiper classes based on viewport, ensuring slider only on mobile.
  function boot(){ document.querySelectorAll('.ceiba-pl').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
})();
