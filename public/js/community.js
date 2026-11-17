/* Community enhancements: progressive enhancement, ordinary forms still work without JS. */
(() => {
  const bell = document.querySelector('.hn-bell[data-unread-url]');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  const notify = (message, error = false) => {
    let toast = document.querySelector('.cm-toast');
    if (!toast) { toast = document.createElement('div'); toast.className = 'cm-toast'; toast.setAttribute('role','status'); document.body.appendChild(toast); }
    toast.textContent = message; toast.classList.toggle('cm-toast-error', error); toast.classList.add('cm-toast-visible');
    clearTimeout(toast._timeout); toast._timeout = setTimeout(() => toast.classList.remove('cm-toast-visible'), 3300);
  };
  const updateBell = async () => {
    if (!bell || document.hidden) return;
    try {
      const response = await fetch(bell.dataset.unreadUrl, {headers:{Accept:'application/json'}, credentials:'same-origin', cache:'no-store'});
      if (!response.ok) return;
      const {count} = await response.json();
      const badge = bell.querySelector('[data-unread-count]');
      if (badge) { badge.hidden = !count; badge.textContent = count > 99 ? '99+' : count; }
      bell.setAttribute('aria-label', count ? `Notifikasi (${count} belum dibaca)` : 'Notifikasi');
    } catch (_) { /* Connectivity loss should not disrupt other features. */ }
  };
  async function send(form) {
    const button = form.querySelector('button[type="submit"]');
    if (button?.disabled) return;
    if (button) button.disabled = true;
    try {
      const response = await fetch(form.action, {method:'POST', body:new FormData(form), credentials:'same-origin', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest', ...(csrf ? {'X-CSRF-TOKEN':csrf} : {})}});
      if (!response.ok) {
        if (response.status === 419) throw Error('Sesi berakhir. Muat ulang halaman dan coba lagi.');
        if (response.status === 429) throw Error('Terlalu banyak aktivitas. Coba lagi sebentar.');
        if (response.status === 422) { const data = await response.json(); throw Error(Object.values(data.errors || {}).flat()[0] || 'Periksa kembali komentar kamu.'); }
        throw Error('Belum berhasil. Silakan coba lagi.');
      }
      const data = await response.json();
      const kind = form.dataset.communityAction;
      if (kind === 'like' || kind === 'save') {
        const post = form.closest('[data-community-post]');
        const action = form.querySelector('button');
        action.classList.toggle('cm-on', data.active);
        action.setAttribute('aria-pressed', String(data.active));
        if (kind === 'like') post?.querySelector('[data-like-count]')?.replaceChildren(document.createTextNode(String(data.count)));
        if (kind === 'save') { const label = action.querySelector('[data-save-label]'); if (label) label.textContent = data.active ? 'Tersimpan' : 'Simpan'; notify(data.active ? 'Postingan masuk ke Tersimpan di profilmu.' : 'Postingan dihapus dari Tersimpan.'); }
      } else if (kind === 'comment') {
        const list = document.querySelector('[data-comments-list]');
        if (list && data.html) {
          const fragment = document.createElement('template'); fragment.innerHTML = data.html.trim();
          const node = fragment.content.firstElementChild;
          if (data.parent_id) {
            const parent = [...list.querySelectorAll('[data-comment-id]')].find(el => el.dataset.commentId === String(data.parent_id));
            const replies = parent?.querySelector(':scope > .cm-replies');
            (replies || list).appendChild(node);
          } else { list.querySelector('[data-empty-comments]')?.remove(); list.appendChild(node); }
          document.querySelectorAll('[data-comment-count],[data-detail-comment-count]').forEach(el => el.textContent = data.count);
          form.reset();
          const details = form.closest('details'); if (details) details.open = false;
          notify(data.parent_id ? 'Balasan terkirim!' : 'Komentar terkirim!');
        }
      }
    } catch (error) { notify(error.message || 'Terjadi kesalahan jaringan.', true); }
    finally { if (button) button.disabled = false; }
  }
  document.addEventListener('submit', event => {
    const form = event.target.closest('form[data-community-action]');
    if (!form) return;
    event.preventDefault(); send(form);
  });
  updateBell();
  document.addEventListener('visibilitychange', () => { if (!document.hidden) updateBell(); });
  setInterval(updateBell, 15000);
})();
