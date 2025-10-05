document.addEventListener("DOMContentLoaded", function() {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target); // only animate once
      }
    });
  }, { threshold: 0.1 });

  // Observe both fade-up and fade-in
  document.querySelectorAll(".fade-up, .fade-in").forEach(el => {
    observer.observe(el);
  });
});
