<footer class="footer">
    <div class="container footer__inner">
        <p>© <span id="year"></span> {{ $settings->company_name }}. All rights reserved.</p>
        <p class="footer__right">
            Co-executed with <a href="https://veloxpsi.com/" target="_blank" rel="noreferrer">Velox PSI</a> partner teams
            where applicable.
        </p>
    </div>
    @if ($settings->footer_legal_html)
        <div class="container footer__legal">{!! $settings->footer_legal_html !!}</div>
    @endif
</footer>
