(function(){
  function init(root){
    if (!root || root.dataset.cbAccInit) return;
    root.dataset.cbAccInit = '1';
    var button = root.querySelector('.cb-accordion__trigger');
    var panel = root.querySelector('.cb-accordion__panel');
    if (!button || !panel) return;
    button.addEventListener('click', function(){
      var expanded = button.getAttribute('aria-expanded') === 'true';
      button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      panel.hidden = expanded;
      root.classList.toggle('is-open', !expanded);
    });
  }
  function boot(){
    document.querySelectorAll('.cb-accordion').forEach(init);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
