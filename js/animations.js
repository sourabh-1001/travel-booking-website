document.addEventListener('DOMContentLoaded', () => {
  const nodes = document.querySelectorAll('[data-animate]');
  if (nodes.length === 0 || !('IntersectionObserver' in window)) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) entry.target.classList.add('show');
    });
  }, { threshold: 0.12 });

  nodes.forEach((node) => observer.observe(node));
});
