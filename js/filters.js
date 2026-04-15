document.addEventListener('DOMContentLoaded', () => {
  const destination = document.getElementById('destination');
  const duration = document.getElementById('duration');
  const price = document.getElementById('price');
  const cards = document.querySelectorAll('#tour-grid .card');

  const missing = [];
  if (!destination) missing.push('#destination');
  if (!duration) missing.push('#duration');
  if (!price) missing.push('#price');
  if (cards.length === 0) missing.push('#tour-grid .card');
  if (missing.length > 0) {
    console.warn('Tour filters are not initialized. Missing:', missing.join(', '));
    return;
  }

  const runFilter = () => {
    cards.forEach((card) => {
      const matchDestination = !destination.value || card.dataset.tour === destination.value;
      const matchDuration = !duration.value || card.dataset.duration === duration.value;
      const matchPrice = !price.value || card.dataset.price === price.value;
      card.style.display = matchDestination && matchDuration && matchPrice ? 'block' : 'none';
    });
  };

  [destination, duration, price].forEach((node) => node.addEventListener('change', runFilter));
});
