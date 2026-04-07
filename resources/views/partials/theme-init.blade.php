<meta name="theme-color" content="#0B1829" id="verlox-theme-color">
<script>
(function () {
    try {
        var t = localStorage.getItem('verlox-theme');
        if (t !== 'light' && t !== 'dark') {
            t = 'light';
        }
        document.documentElement.setAttribute('data-theme', t);
        document.documentElement.classList.toggle('dark', t === 'dark');
        var col = t === 'dark' ? '#0B1829' : '#f4f5fb';
        var m = document.getElementById('verlox-theme-color');
        if (m) {
            m.setAttribute('content', col);
        }
    } catch (e) {}
})();
</script>
