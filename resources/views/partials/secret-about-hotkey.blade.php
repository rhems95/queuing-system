{{-- Secret About hotkey: Ctrl + Alt + Shift + A (works offline; no menu link) --}}
<script>
    (function () {
        if (window.__pecitAboutHotkeyBound) return;
        window.__pecitAboutHotkeyBound = true;

        var aboutUrl = @json(route('about'));

        document.addEventListener('keydown', function (e) {
            if (!(e.ctrlKey && e.altKey && e.shiftKey)) return;
            var key = String(e.key || '').toLowerCase();
            var code = String(e.code || '');
            if (key !== 'a' && code !== 'KeyA') return;

            e.preventDefault();
            if (window.location.pathname.replace(/\/$/, '') === new URL(aboutUrl, window.location.origin).pathname.replace(/\/$/, '')) {
                return;
            }
            window.location.href = aboutUrl;
        }, true);
    })();
</script>
