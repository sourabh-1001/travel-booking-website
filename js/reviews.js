function renderLocalReviews(){
  const m=document.getElementById('local-reviews');
  if(!m) return;
  const d=[
    {name:'Anita S.',rating:5,text:'Golden Triangle 5-day was perfectly managed.'},
    {name:'Marco R.',rating:5,text:'Excellent guide and seamless WhatsApp support.'},
    {name:'Priya K.',rating:4,text:'Great itinerary and very responsive team.'}
  ];
  m.innerHTML=d.map(r=>'<article class="review-widget"><div class="review-stars">'+'★'.repeat(r.rating)+'☆'.repeat(5-r.rating)+'</div><p>'+r.text+'</p><small>— '+r.name+'</small></article>').join('');
}
document.addEventListener('DOMContentLoaded',renderLocalReviews);
