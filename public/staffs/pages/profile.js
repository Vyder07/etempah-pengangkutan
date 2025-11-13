 (function(){
      const logoutLink = document.querySelector('.logout-link a');
      if (!logoutLink) return;

      const modal = document.getElementById('logoutModal');
      const confirmBtn = document.getElementById('confirmLogout');
      const cancelBtn = document.getElementById('cancelLogout');
      const backdrop = modal.querySelector('.modal-backdrop');

      function openModal(href){
        modal.dataset.href = href || '';
        modal.setAttribute('aria-hidden','false');
        // focus confirm for keyboard users
        confirmBtn.focus();
      }

      function closeModal(){
        modal.setAttribute('aria-hidden','true');
        delete modal.dataset.href;
        logoutLink.focus();
      }

      logoutLink.addEventListener('click', function(e){
        e.preventDefault();
        openModal(this.getAttribute('href'));
      });

      confirmBtn.addEventListener('click', function(){
        const href = modal.dataset.href || logoutLink.getAttribute('href');
        if (href) {
          // clear client-side session if needed
          try { localStorage.clear(); sessionStorage.clear(); } catch(e){}
          window.location.href = href;
        } else closeModal();
      });

      cancelBtn.addEventListener('click', closeModal);
      backdrop.addEventListener('click', closeModal);
      document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closeModal();
      });
    })();