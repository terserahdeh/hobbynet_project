/* HobbyNet: polesan UI ringan. Semua fitur opsional; halaman tetap jalan tanpa file ini. */
(function () {
    'use strict';

    // 1) Riak halus saat tombol ditekan
    var SELECTOR = '.btn, .rm-btn, .btn-pink, .btn-dark, .btn-outline, .account__out, .pl-btn, .nav__plus, .ai-fab';
    document.addEventListener('pointerdown', function (e) {
        var btn = e.target.closest && e.target.closest(SELECTOR);
        if (!btn || btn.disabled || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        var rect = btn.getBoundingClientRect();
        var size = Math.max(rect.width, rect.height);
        var dot = document.createElement('span');
        dot.className = 'hn-ripple';
        dot.style.width = dot.style.height = size + 'px';
        dot.style.left = (e.clientX - rect.left - size / 2) + 'px';
        dot.style.top = (e.clientY - rect.top - size / 2) + 'px';
        btn.appendChild(dot);
        setTimeout(function () { dot.remove(); }, 600);
    }, { passive: true });

    // 2) Kemajuan checklist di halaman langkah roadmap
    var list = document.getElementById('rm-checklist');
    if (list) {
        var boxes = list.querySelectorAll('input[type=checkbox]');
        if (boxes.length) {
            var wrap = document.createElement('div');
            wrap.className = 'hn-checkprog';
            wrap.setAttribute('aria-live', 'polite');
            wrap.innerHTML = '<span class="hn-checkprog__label"></span><span class="hn-checkprog__bar"><span></span></span>';
            list.parentNode.insertBefore(wrap, list.nextSibling);

            var label = wrap.querySelector('.hn-checkprog__label');
            var fill = wrap.querySelector('.hn-checkprog__bar span');

            var update = function () {
                var done = 0;
                boxes.forEach(function (b) { if (b.checked) done++; });
                var all = done === boxes.length;
                label.textContent = all ? 'Semua selesai. Siap lanjut!' : done + ' dari ' + boxes.length + ' selesai';
                fill.style.width = Math.round(done / boxes.length * 100) + '%';
                wrap.classList.toggle('is-done', all);
            };

            boxes.forEach(function (b) { b.addEventListener('change', function () { setTimeout(update, 0); }); });
            update();
            // checkbox bisa dikembalikan (rollback) setelah request gagal, jadi hitung ulang sebentar kemudian
            setInterval(update, 1200);
        }
    }
})();
