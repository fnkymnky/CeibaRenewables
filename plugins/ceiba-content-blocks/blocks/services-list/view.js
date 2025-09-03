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
    var mql = window.matchMedia('(max-width: 639px)');

    var wrapper = container.querySelector('.ceiba-services-grid');
    function mount(Sw){
      if (instance || !mql.matches) return;
      // Add Swiper classes dynamically
      container.classList.add('swiper');
      if (wrapper) wrapper.classList.add('swiper-wrapper');
      container.querySelectorAll('.ceiba-service-card').forEach(function(card){ card.classList.add('swiper-slide'); });
      instance = new Sw(container, {
        slidesPerView: 1,
        spaceBetween: 12,
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
      // Remove Swiper classes to restore grid layout
      container.classList.remove('swiper');
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

  function boot(){ document.querySelectorAll('.ceiba-pl.swiper').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
})();
