/* =====================================================================
   PM9 Trade Manager — Promo Page Language Config  (config.js)
   =====================================================================
   WHAT THIS FILE DOES
   - Forces the promo page to load in ENGLISH by default (LTR layout).
   - Does NOT touch any price or text you edited inside index.html.
   - If a visitor manually switches to Persian with the «فا» button,
     their choice is remembered and respected on the next visit.

   HOW TO USE ON GITHUB  —  two cases:

   CASE 1) Your repository has NO config.js next to index.html
           -> Just upload this file (Add file > Upload files) in the
              SAME folder as index.html. Commit. Done.

   CASE 2) Your repository ALREADY has a config.js that contains your
           edited prices/texts (window.PM9_CONFIG = {...})
           -> DO NOT REPLACE IT (you would lose your edits).
              Instead: open it, and PASTE the whole block below
              (from BEGIN to END) at the very END of that file.

   WHY A FILE AND NOT AN HTML EDIT?
   The bilingual screenshots (default src) are the PERSIAN versions;
   only the swap logic in this block switches them to the English
   ones — so editing index.html alone would leave Persian images.
   ===================================================================== */

/* ================= BEGIN — force English default ================= */
(function(){
  var LANG_KEY = 'pm9promo-lang';

  function pm9DefaultEN(){
    try{
      // If the visitor explicitly picked Persian before, respect that choice.
      var saved = null;
      try{ saved = localStorage.getItem(LANG_KEY); }catch(e){}
      if(saved === 'fa') return;

      var root = document.getElementById('pm9-promo');
      if(!root) return;

      if(root.getAttribute('data-lang') !== 'en'){
        // Text spans are toggled by CSS through the data-lang attribute.
        root.setAttribute('data-lang', 'en');
        root.setAttribute('dir', 'ltr');
        document.documentElement.setAttribute('lang', 'en');
        document.documentElement.setAttribute('dir', 'ltr');

        // The language button always shows the OTHER language.
        var btn = document.getElementById('pLangBtn');
        if(btn) btn.textContent = '\u0641\u0627'; /* «فا» */

        // Swap every bilingual screenshot to its English version.
        var imgs = root.querySelectorAll('img.bimg');
        for(var i = 0; i < imgs.length; i++){
          var s = imgs[i].getAttribute('data-en-src');
          if(s) imgs[i].setAttribute('src', s);
        }
        // Re-run menu edge-fade listeners for the new direction.
        try{ window.dispatchEvent(new Event('resize')); }catch(e){}
      }
      // Remember English so the next load starts in English instantly.
      try{ localStorage.setItem(LANG_KEY, 'en'); }catch(e){}
    }catch(e){}
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', pm9DefaultEN);
  }else{
    pm9DefaultEN();
  }
  window.addEventListener('load', pm9DefaultEN);
})();
/* ================== END — force English default ================== */
