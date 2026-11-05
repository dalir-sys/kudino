/* Kudino — progressive enhancement only; every control works without it. */
(function () {
	'use strict';

	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
	var reduced = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
	var fa = function (n) { return String(n).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; }); };

	/* Header shadow once the page scrolls. */
	var header = $('#kd-header');
	if (header) {
		var onScroll = function () { header.classList.toggle('is-stuck', window.scrollY > 8); };
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/* "/" focuses search. */
	document.addEventListener('keydown', function (e) {
		if (e.key !== '/' || /input|textarea|select/i.test(document.activeElement.tagName)) return;
		var input = $('#kd-search-input');
		if (input && input.offsetParent) { e.preventDefault(); input.focus(); }
	});

	/* Category mega menu: click / keyboard toggle (hover handled in CSS). */
	var cats = $('.kd-nav__cats');
	if (cats) {
		var catBtn = $('button', cats);
		catBtn.addEventListener('click', function () {
			var open = !cats.classList.contains('is-open');
			cats.classList.toggle('is-open', open);
			catBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
		document.addEventListener('click', function (e) {
			if (!cats.contains(e.target)) { cats.classList.remove('is-open'); catBtn.setAttribute('aria-expanded', 'false'); }
		});
	}

	/* Drawer. */
	var drawer = $('#kd-drawer');
	var opener = $('[data-kd-drawer-open]');
	function setDrawer(open) {
		if (!drawer) return;
		drawer.classList.toggle('is-open', open);
		drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
		if (opener) opener.setAttribute('aria-expanded', open ? 'true' : 'false');
		document.documentElement.style.overflow = open ? 'hidden' : '';
		if (open) { var f = $('.kd-drawer__body a', drawer); if (f) f.focus(); }
		else if (opener) opener.focus();
	}
	document.addEventListener('click', function (e) {
		if (e.target.closest('[data-kd-drawer-open]')) setDrawer(true);
		else if (e.target.closest('[data-kd-drawer-close]')) setDrawer(false);
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && drawer && drawer.classList.contains('is-open')) setDrawer(false);
	});

	/* Hero slider: dots, arrows, swipe (native scroll not needed), autoplay that pauses on hover/focus. */
	$$('[data-kd-slider]').forEach(function (slider) {
		var track = $('.kd-slider__track', slider);
		var slides = $$('.kd-slide', track);
		var dots = $('[data-kd-dots]', slider);
		var i = 0, timer = null;
		if (slides.length < 2) return;
		slides.forEach(function (s, n) {
			var b = document.createElement('button');
			b.type = 'button';
			b.setAttribute('aria-label', 'اسلاید ' + fa(n + 1));
			b.addEventListener('click', function () { go(n); restart(); });
			dots.appendChild(b);
		});
		function go(n) {
			i = (n + slides.length) % slides.length;
			track.style.transform = 'translateX(' + (i * 100) + '%)'; /* RTL: positive moves to the next slide */
			slides.forEach(function (s, k) { s.setAttribute('aria-hidden', k === i ? 'false' : 'true'); s.inert = k !== i; });
			$$('button', dots).forEach(function (d, k) { d.setAttribute('aria-current', k === i ? 'true' : 'false'); });
		}
		function restart() {
			clearInterval(timer);
			if (!reduced) timer = setInterval(function () { go(i + 1); }, 6500);
		}
		$('[data-kd-next]', slider).addEventListener('click', function () { go(i + 1); restart(); });
		$('[data-kd-prev]', slider).addEventListener('click', function () { go(i - 1); restart(); });
		slider.addEventListener('mouseenter', function () { clearInterval(timer); });
		slider.addEventListener('mouseleave', restart);
		slider.addEventListener('focusin', function () { clearInterval(timer); });
		var x0 = null;
		slider.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
		slider.addEventListener('touchend', function (e) {
			if (x0 === null) return;
			var dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 40) { go(i + (dx > 0 ? 1 : -1)); restart(); }
			x0 = null;
		});
		go(0);
		restart();
	});

	/* Rails: arrow buttons scroll by one card. */
	function railStep(rail) {
		var card = rail.firstElementChild;
		return card ? card.getBoundingClientRect().width + 16 : rail.clientWidth;
	}
	document.addEventListener('click', function (e) {
		var b = e.target.closest('[data-kd-rail-next], [data-kd-rail-prev]');
		if (!b) return;
		var next = b.hasAttribute('data-kd-rail-next');
		var rail = $(b.getAttribute(next ? 'data-kd-rail-next' : 'data-kd-rail-prev'));
		if (!rail) return;
		/* RTL: "next" moves toward the end, i.e. negative scrollLeft. */
		rail.scrollBy({ left: (next ? -1 : 1) * railStep(rail), behavior: reduced ? 'auto' : 'smooth' });
	});

	/* Filter chips. */
	$$('[data-kd-filter]').forEach(function (bar) {
		var grid = $(bar.getAttribute('data-kd-filter'));
		bar.addEventListener('click', function (e) {
			var chip = e.target.closest('.kd-chip');
			if (!chip || !grid) return;
			$$('.kd-chip', bar).forEach(function (c) { c.setAttribute('aria-selected', c === chip ? 'true' : 'false'); });
			var g = chip.getAttribute('data-group');
			$$('[data-group]', grid).forEach(function (item) { item.hidden = !!g && item.getAttribute('data-group') !== g; });
			grid.scrollLeft = 0;
		});
	});

	/* Countdowns. */
	var counters = $$('[data-kd-countdown]');
	if (counters.length) {
		var tick = function () {
			counters.forEach(function (el) {
				var left = Math.max(0, Math.floor((Date.parse(el.getAttribute('data-kd-countdown')) - Date.now()) / 1000));
				var parts = [Math.floor(left / 86400), Math.floor(left % 86400 / 3600), Math.floor(left % 3600 / 60), left % 60];
				$$('b', el).forEach(function (b, k) { b.textContent = fa(String(parts[k]).padStart(2, '0')); });
			});
		};
		tick();
		setInterval(tick, 1000);
	}

	/* Almanac: one crop open at a time. */
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
		var sel = $('#kd-c-crop'); if (sel) sel.value = key;
		if (scroll) $('#kd-almanac').scrollIntoView({ behavior: reduced ? 'auto' : 'smooth' });
		return true;
	}
	if (cal) {
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

	/* Forms without a backend yet: confirm in place. */
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
		var fav = e.target.closest('.kd-card__fav');
		if (!fav) return;
		var on = !fav.classList.contains('is-on');
		fav.classList.toggle('is-on', on);
		fav.setAttribute('aria-pressed', on ? 'true' : 'false');
	});
})();
