// Case Study (Single) front-end runtime hook
// Matches Insights slide structure for consistent visuals
import './view.scss';

(function(){
  function init(root){
    if (!root || root.dataset.csSingleInit) return;
    root.dataset.csSingleInit = '1';
    // Currently no JS behavior needed; placeholder for future enhancements
  }

  function boot(){ document.querySelectorAll('.wp-block-ceiba-case-study').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  new MutationObserver(boot).observe(document.documentElement, { childList: true, subtree: true });
})();

