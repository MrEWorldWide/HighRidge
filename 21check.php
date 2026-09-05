<script> 
/* Minimal 21+ gate with Shadow DOM — no global CSS leakage (no flash) */
(function () {
  // --- config ---
  var cookieName = '21plus';
  var rememberDays = 30;
  var under21Destinations = [
    'https://kitten.academy',
    'https://www.tinykittens.com',
    'https://cfa.org',
    'https://icatcare.org',
    'https://pusheen.com',
    'https://cats.com',
    'https://www.cathouseonthekings.com',
    'https://en.wikipedia.org/wiki/Cat',
    'https://commons.wikimedia.org/wiki/Category:Cats',
    'https://icatcare.org',
    'https://www.alleycat.org',
    'https://www.aspca.org',
    'https://www.humaneworld.org',
    'https://www.cats.org.uk',
    'https://www.vet.cornell.edu/departments-centers-and-institutes/cornell-feline-health-center',
    'https://www.tica.org',
    'https://cfa.org',
    'https://bestfriends.org',
    'https://www.cathouseonthekings.com',
    'https://www.si.edu/kids',
    'https://www.rspca.org.uk/adviceandwelfare/pets/cats',
    'https://www.pdsa.org.uk/pet-help-and-advice/pet-health-hub/cats',
    'https://www.bluecross.org.uk/pet-advice/cat',
    'https://www.battersea.org.uk/pet-advice/cat',
    'https://www.sheltermedicine.com',
    'https://catvets.com',
    'https://icatcare.org/isfm',
    'https://www.avma.org/resources/pet-owners/petcare/cats',
    'https://education.cats.org.uk',
    'https://commons.wikimedia.org/wiki/Category:Domestic_cats',
    'https://en.wikipedia.org/wiki/List_of_cat_breeds',
    'https://www.akc.org/dog-breeds/',
    'https://www.aspca.org/pet-care/dog-care',
    'https://www.humanesociety.org/resources/dog-care',
    'https://www.petmd.com/',
    'https://www.vetstreet.com/dogs/',
    'https://www.cesarsway.com/',
    'https://www.thedodo.com/dogs/',
    'https://www.thedogclinic.com/',
    'https://www.dogster.com/',
    'https://www.rover.com/blog/dog/'
  ];

  // helpers
  function hasCookie(n){ return document.cookie.split(';').some(function(c){ return c.trim().indexOf(n+'=')===0; }); }
  function setCookie(n,v,days){
    var d = new Date(); d.setTime(d.getTime()+days*24*60*60*1000);
    document.cookie = n + "=" + encodeURIComponent(v) + "; expires=" + d.toUTCString() + "; path=/";
  }

  if (hasCookie(cookieName)) return; // already verified; bail early

  // ---- PREHIDE immediately to avoid flash ----
  // (we hide only the page content; the overlay is opaque and will show)
  var prehide = document.createElement('style');
  prehide.id = 'age-prehide';
  prehide.textContent = 'html{overflow:hidden !important} body{visibility:hidden !important}';
  (document.head || document.documentElement).appendChild(prehide);

  // Create an isolated overlay (no page CSS leakage)
  var host = document.createElement('div');
  host.setAttribute('aria-hidden','true');

  // Try to mount ASAP (if body exists now, mount now; otherwise on DOM ready)
  if (document.body) {
    mount();
  } else {
    document.addEventListener('DOMContentLoaded', mount);
  }

  function mount(){
    // Ensure body exists
    if (!document.body) return document.addEventListener('DOMContentLoaded', mount);

    document.body.appendChild(host);
    var root = host.attachShadow({mode:'open'});
    root.innerHTML = '\
      <style>\
        :host { all: initial; }\
        .overlay {\
          position: fixed; inset: 0;\
          background: #fff; color: #111;\
          display: grid; place-items: center;\
          z-index: 2147483647;\
          font-family: Arial, sans-serif; line-height: 1.6;\
          text-align: center; padding: 24px;\
        }\
        .box { width: min(520px, 92vw); }\
        h1 { margin: 0 0 8px; font-size: 1.35rem; }\
        p { margin: 0 0 12px; }\
        .row { display: inline-flex; gap: 12px; flex-wrap: wrap; }\
        button {\
          background: #b1ff00; border: 0; color: #fff;\
          padding: 10px 16px; font-size: 16px; border-radius: 8px; cursor: pointer;\
        }\
        button:hover { filter: brightness(1.08); }\
        .secondary { background: #23262d; }\
        @media (prefers-color-scheme: dark){\
          .overlay { background: #0e0f12; color: #eee; }\
          .secondary { background: #2b2f39; }\
        }\
      </style>\
      <div class="overlay" role="dialog" aria-modal="true" aria-labelledby="age-title" aria-describedby="age-desc">\
        <div class="box">\
          <h1 id="age-title">Are you 21 years or older?</h1>\
          <p id="age-desc">You must be 21+ to enter.</p>\
          <div class="row">\
            <button id="yes" type="button" style="color:#000;">Yes, I\'m 21+</button>\
</div>\
<br>\
<br>\
<br>\
<div class="row">\
            <button id="no" type="button" class="secondary">No</button>\
          </div>\
        </div>\
      </div>';

    var yes = root.getElementById('yes');
    var no  = root.getElementById('no');

    // Now that overlay exists, reveal the document (overlay is opaque anyway)
    try { prehide.remove(); } catch(e) { if (prehide && prehide.parentNode) prehide.parentNode.removeChild(prehide); }

    yes.addEventListener('click', function(){
      setCookie(cookieName,'1',rememberDays);
      cleanup();
    });

    no.addEventListener('click', function(){
      var url = under21Destinations[Math.floor(Math.random()*under21Destinations.length)];
      window.location.replace(url);
    });
  }

  function cleanup(){
    // Restore scroll and remove overlay
    document.documentElement.style.overflow = '';
    host.remove();
  }
})();
</script>
