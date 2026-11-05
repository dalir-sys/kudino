/* Kudino — progressive enhancement only; every control works without it. */
(function () {
	'use strict';

	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
	var reduced = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Header: a firmer rule once the page scrolls. */
	var header = $('#kd-header');
	if (header) {
		var onScroll = function () { header.classList.toggle('is-stuck', window.scrollY > 4); };
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/* "/" focuses search. */
	document.addEventListener('keydown', function (e) {
		if (e.key !== '/' || /input|textarea|select/i.test(document.activeElement.tagName)) return;
		var input = $('#kd-search-input');
		if (input && input.offsetParent) { e.preventDefault(); input.focus(); }
	});

	/* Drawer. */
	var drawer = $('#kd-drawer');
	var opener = $('[data-kd-drawer-open]');
	function setDrawer(open) {
		if (!drawer) return;
		drawer.classList.toggle('is-open', open);
		drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
		if (opener) opener.setAttribute('aria-expanded', open ? 'true' : 'false');
		document.documentElement.style.overflow = open ? 'hidden' : '';
		if (open) {
			var first = $('.kd-drawer__body input, .kd-drawer__body a', drawer);
			if (first) first.focus();
		} else if (opener) {
			opener.focus();
		}
	}
	document.addEventListener('click', function (e) {
		if (e.target.closest('[data-kd-drawer-open]')) setDrawer(true);
		else if (e.target.closest('[data-kd-drawer-close]')) setDrawer(false);
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && drawer && drawer.classList.contains('is-open')) setDrawer(false);
	});

	/* Shelf filter (underline tabs). */
	$$('[data-kd-filter]').forEach(function (tabs) {
		var grid = $(tabs.getAttribute('data-kd-filter'));
		tabs.addEventListener('click', function (e) {
			var tab = e.target.closest('.kd-tab');
			if (!tab || !grid) return;
			$$('.kd-tab', tabs).forEach(function (t) { t.setAttribute('aria-selected', t === tab ? 'true' : 'false'); });
			var g = tab.getAttribute('data-group');
			$$('[data-group]', grid).forEach(function (item) { item.hidden = !!g && item.getAttribute('data-group') !== g; });
		});
	});

	/* Almanac: one crop open at a time; the hero finder drives it too. */
	var cal = $('.kd-cal');
	function openCrop(key, scroll) {
		if (!cal) return false;
		var row = $('tr[data-crop="' + key + '"]', cal);
		if (!row) return false;
		$$('tbody tr', cal).forEach(function (tr) {
			var on = tr === row;
			tr.classList.toggle('is-active', on);
			var b = $('.kd-cal__crop', tr);
			b.setAttribute('aria-expanded', on ? 'true' : 'false');
			var panel = document.getElementById(b.getAttribute('aria-controls'));
			if (panel) panel.hidden = !on;
		});
		$$('#kd-finder-crop, #kd-rx-crop').forEach(function (s) { s.value = key; });
		if (scroll) $('#kd-almanac').scrollIntoView({ behavior: reduced ? 'auto' : 'smooth' });
		return true;
	}
	if (cal) {
		/* On narrow screens, bring the current month into view inside the scroller. */
		var scroller = cal.parentElement;
		var now = $('thead .is-now', cal);
		if (now && scroller.scrollWidth > scroller.clientWidth) {
			var sb = scroller.getBoundingClientRect(), nb = now.getBoundingClientRect();
			scroller.scrollLeft += (nb.left + nb.width / 2) - (sb.left + sb.width / 2);
		}
		cal.addEventListener('click', function (e) {
			var b = e.target.closest('.kd-cal__crop');
			if (b) openCrop(b.closest('tr').getAttribute('data-crop'), false);
		});
	}
	var finder = $('[data-kd-finder]');
	if (finder) {
		finder.addEventListener('submit', function (e) {
			var key = $('#kd-finder-crop', finder).value;
			if (openCrop(key, true)) {
				e.preventDefault();
				try { history.replaceState(null, '', '?crop=' + encodeURIComponent(key) + '#kd-almanac'); } catch (err) { /* ignore */ }
			}
		});
	}

	/* Forms without a backend yet: confirm in place instead of reloading. */
	$$('[data-kd-fake-submit]').forEach(function (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var status = $('[data-kd-status]', form);
			var btn = $('button[type="submit"]', form);
			if (btn) { btn.disabled = true; btn.textContent = 'ثبت شد'; }
			if (status) status.textContent = 'درخواستت ثبت شد. کارشناس ما در ساعت کاری تماس می‌گیرد.';
		});
	});

	/* Wishlist toggle (visual; persist with a wishlist plugin). */
	document.addEventListener('click', function (e) {
		var fav = e.target.closest('.kd-item__fav');
		if (!fav) return;
		var on = !fav.classList.contains('is-on');
		fav.classList.toggle('is-on', on);
		fav.setAttribute('aria-pressed', on ? 'true' : 'false');
	});
})();
