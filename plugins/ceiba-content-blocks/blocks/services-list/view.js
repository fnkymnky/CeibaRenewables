import Swiper from 'swiper';
import { Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';

(function () {
  const mql = window.matchMedia('(max-width: 767px)');

  function mount(root) {
    const container = root; // Swiper container is the inner wrapper
    const wrapper = container.querySelector('.ceiba-services-grid');
    if (!wrapper) return null;

    const slides = Array.from(container.querySelectorAll('.ceiba-service-card'));
    if (!slides.length) return null;

    container.classList.add('swiper');
    wrapper.classList.add('swiper-wrapper');
    slides.forEach((el) => el.classList.add('swiper-slide'));

    const s = new Swiper(container, {
      modules: [Pagination],
      slidesPerView: 1.15,
      slidesOffsetBefore: 16,
      slidesOffsetAfter: 16,
      spaceBetween: 12,
      centeredSlides: false,
      loop: false,
      pagination: { el: container.querySelector('.swiper-pagination'), clickable: true },
      watchOverflow: true,
      observer: true,
      observeParents: true,
    });
    return s;
  }

  function unmount(root, instance) {
    if (instance && instance.destroy) {
      instance.destroy(true, true);
    }
    const container = root;
    const wrapper = container.querySelector('.ceiba-services-grid');
    const slides = Array.from(container.querySelectorAll('.ceiba-service-card'));
    container.classList.remove('swiper', 'swiper-initialized', 'swiper-horizontal');
    if (wrapper) wrapper.classList.remove('swiper-wrapper');
    slides.forEach((el) => {
      el.classList.remove('swiper-slide');
      el.removeAttribute('style');
    });
    if (wrapper) wrapper.removeAttribute('style');
  }

  function ensureState(root) {
    if (!root) return;
    const mobile = mql.matches;
    const has = !!root._swiper;
    if (mobile && !has) {
      root._swiper = mount(root) || undefined;
    } else if (!mobile && has) {
      unmount(root, root._swiper);
      root._swiper = undefined;
    }
  }

  function init(root) {
    if (!root || root.dataset.servicesInit) return;
    root.dataset.servicesInit = '1';
    ensureState(root);
  }

  function boot() {
    document
      .querySelectorAll('.wp-block-ceiba-page-list .ceiba-pl')
      .forEach((el) => init(el));
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
  mql.addEventListener('change', () => {
    document
      .querySelectorAll('.wp-block-ceiba-page-list .ceiba-pl')
      .forEach((el) => ensureState(el));
  });
})();
