{{-- Bootstrap modal smart search; trigger: #smartSearchTrigger in shell-chrome. JSON: { categories: [{ title, items: [{ label, meta, url }] }] } --}}
<div class="modal fade" id="smartSearchModal" tabindex="-1" aria-labelledby="smartSearchModalLabel" aria-hidden="true" data-bs-theme="light">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="input-group input-group-lg flex-grow-1 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="smartSearchInput" placeholder="Search MIS - leads, clients, invoices, bookings…" autocomplete="off" aria-label="Search MIS">
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div id="smartSearchResults" class="smart-search-results"></div>
                <div id="smartSearchEmpty" class="text-center text-muted py-4 small" data-default-msg="Type 2+ characters (or a numeric ID) to search leads, clients, invoices, bookings, and more." data-noresults-msg="No matches. Try another name, email, or ID.">Type 2+ characters (or a numeric ID) to search leads, clients, invoices, bookings, and more.</div>
                <div id="smartSearchLoading" class="text-center text-muted py-3 d-none"><span class="spinner-border spinner-border-sm me-2"></span> Searching…</div>
                <div id="smartSearchError" class="alert alert-danger py-2 d-none"></div>
            </div>
        </div>
    </div>
</div>

<style>
/*
 * MIS body uses dark:text-[#E5E7EB] - that color inherits into this modal while Bootstrap
 * keeps a light panel, so hint / results / errors look “empty”. Force readable light-theme text.
 */
#smartSearchModal .modal-content {
    color: #1e293b;
    background-color: #fff;
}
#smartSearchModal .modal-header .form-control,
#smartSearchModal .modal-header .input-group-text {
    color: #1e293b;
    background-color: #fff;
}
#smartSearchModal .modal-body .text-muted {
    color: #64748b !important;
}
#smartSearchModal .alert-danger {
    color: #842029;
}
.smart-search-results { max-height: 55vh; overflow-y: auto; }
.smart-search-results .search-category { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6c757d; margin-top: 0.75rem; margin-bottom: 0.35rem; }
.smart-search-results .search-category:first-child { margin-top: 0; }
.smart-search-results .search-item { display: flex; align-items: center; padding: 0.5rem 0.6rem; border-radius: 8px; text-decoration: none; color: #1e293b; margin-bottom: 2px; }
.smart-search-results .search-item:hover { background: #f8f9fa; }
.smart-search-results .search-item i { width: 1.25rem; text-align: center; margin-right: 0.5rem; color: #6c757d; }
.smart-search-results .search-item .search-item-title { font-weight: 500; color: #0f172a; }
.smart-search-results .search-item .search-item-subtitle { font-size: 0.8rem; color: #64748b; }
</style>

@push('scripts')
<script>
(function() {
    const modalEl = document.getElementById('smartSearchModal');
    const inputEl = document.getElementById('smartSearchInput');
    const triggerEl = document.getElementById('smartSearchTrigger');
    const resultsEl = document.getElementById('smartSearchResults');
    const emptyEl = document.getElementById('smartSearchEmpty');
    const loadingEl = document.getElementById('smartSearchLoading');
    const errorEl = document.getElementById('smartSearchError');

    if (!modalEl || !inputEl) return;

    const searchPath = @json(route('mis.search', [], false));
    /**
     * Build a path-only URL (e.g. /mis/search?q=…) so fetch always targets the same host as the page.
     * If searchPath were ever absolute with a different host than the tab (APP_URL localhost vs 127.0.0.1),
     * returning u.href would omit session cookies and Laravel returns 401 Unauthenticated.
     */
    function misSmartSearchRequestUrl(q) {
        var path = searchPath;
        if (typeof path !== 'string') path = '/mis/search';
        if (path.indexOf('http://') === 0 || path.indexOf('https://') === 0) {
            try {
                path = new URL(path).pathname || '/mis/search';
            } catch (e) {
                path = '/mis/search';
            }
        }
        if (path.indexOf('/') !== 0) path = '/' + path;
        path = path.split('?')[0];
        var sp = new URLSearchParams();
        sp.set('q', q);
        return path + '?' + sp.toString();
    }
    let debounceTimer = null;
    const debounceMs = 200;

    function showModal() {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    function openModalFocusField() {
        inputEl.value = triggerEl ? triggerEl.value : inputEl.value;
        showModal();
        setTimeout(function() {
            inputEl.focus();
            try { inputEl.setSelectionRange(inputEl.value.length, inputEl.value.length); } catch (_) {}
        }, 100);
    }

    function closeModal() {
        bootstrap.Modal.getInstance(modalEl)?.hide();
    }

    if (triggerEl) {
        /* Click: open palette and type in the modal (Bootstrap focus trap). */
        triggerEl.addEventListener('click', function () {
            openModalFocusField();
        });
        /* Tab into bar or paste: type here; mirror into modal and open when there is input. */
        triggerEl.addEventListener('input', function () {
            inputEl.value = triggerEl.value;
            if (!modalEl.classList.contains('show')) {
                showModal();
                setTimeout(function () {
                    inputEl.focus();
                    try {
                        inputEl.setSelectionRange(inputEl.value.length, inputEl.value.length);
                    } catch (_) {}
                }, 100);
            }
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(doSearch, debounceMs);
        });
    }

    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault();
            if (modalEl.classList.contains('show')) closeModal();
            else openModalFocusField();
        }
        if (e.key === 'Escape' && modalEl.classList.contains('show')) closeModal();
    });

    function escapeHtml(s) {
        if (!s) return '';
        const div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    function queryValid(q) {
        const t = (q || '').trim();
        return t.length >= 2 || /^\d+$/.test(t);
    }

    function renderCategories(categories, qTrimmed) {
        errorEl.classList.add('d-none');
        const list = Array.isArray(categories) ? categories : [];
        const hasItems = list.some(function(c) { return c.items && c.items.length; });
        if (!hasItems) {
            emptyEl.classList.remove('d-none');
            if (qTrimmed && queryValid(qTrimmed)) {
                emptyEl.textContent = emptyEl.getAttribute('data-noresults-msg') || 'No matches.';
            } else {
                emptyEl.textContent = emptyEl.getAttribute('data-default-msg') || emptyEl.textContent;
            }
            resultsEl.innerHTML = '';
            return;
        }
        emptyEl.textContent = emptyEl.getAttribute('data-default-msg') || emptyEl.textContent;
        emptyEl.classList.add('d-none');
        let html = '';
        list.forEach(function(cat) {
            const items = cat.items || [];
            if (!items.length) return;
            html += '<div class="search-category">' + escapeHtml(cat.title || '') + '</div>';
            items.forEach(function(item) {
                const sub = item.meta ? '<div class="search-item-subtitle">' + escapeHtml(item.meta) + '</div>' : '';
                html += '<a href="' + escapeHtml(item.url) + '" class="search-item d-block">' +
                    '<i class="fas fa-circle"></i>' +
                    '<div><div class="search-item-title">' + escapeHtml(item.label) + '</div>' + sub + '</div></a>';
            });
        });
        resultsEl.innerHTML = html;
    }

    function doSearch() {
        const q = (inputEl.value || '').trim();
        if (!queryValid(q)) {
            resultsEl.innerHTML = '';
            emptyEl.textContent = emptyEl.getAttribute('data-default-msg') || emptyEl.textContent;
            emptyEl.classList.remove('d-none');
            loadingEl.classList.add('d-none');
            errorEl.classList.add('d-none');
            return;
        }

        loadingEl.classList.remove('d-none');
        emptyEl.classList.add('d-none');
        errorEl.classList.add('d-none');

        fetch(misSmartSearchRequestUrl(q), {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || ''
            }
        })
        .then(function(r) {
            if (!r.ok) {
                return r.text().then(function(text) {
                    var msg = 'HTTP ' + r.status;
                    try {
                        var j = JSON.parse(text);
                        if (j && j.message) msg = j.message;
                    } catch (_) {}
                    throw new Error(msg);
                });
            }
            var ct = (r.headers.get('content-type') || '');
            if (!ct.includes('application/json')) {
                return r.text().then(function(text) {
                    throw new Error('Server returned non-JSON (try reloading the page or signing in again).');
                });
            }
            return r.json();
        })
        .then(function(data) {
            loadingEl.classList.add('d-none');
            if (!data || typeof data !== 'object') throw new Error('Invalid response');
            renderCategories(data.categories || [], q);
        })
        .catch(function(err) {
            loadingEl.classList.add('d-none');
            var msg = err.message || 'Unknown error';
            if (/unauthenticated/i.test(msg)) {
                msg = 'Session missing for this page address - reload, or log in using the same host you use in the URL (localhost vs 127.0.0.1).';
            }
            var tail = msg.slice(-1) === '.' ? '' : '.';
            errorEl.textContent = 'Search failed: ' + msg + tail + ' Please try again.';
            errorEl.classList.remove('d-none');
            emptyEl.classList.add('d-none');
            resultsEl.innerHTML = '';
        });
    }

    inputEl.addEventListener('input', function() {
        if (triggerEl) triggerEl.value = inputEl.value;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(doSearch, debounceMs);
    });

    inputEl.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            doSearch();
        }
    });

    modalEl.addEventListener('shown.bs.modal', function() {
        const q = (inputEl.value || '').trim();
        if (queryValid(q)) doSearch();
    });

    modalEl.addEventListener('hidden.bs.modal', function() {
        inputEl.value = '';
        if (triggerEl) triggerEl.value = '';
        resultsEl.innerHTML = '';
        emptyEl.textContent = emptyEl.getAttribute('data-default-msg') || emptyEl.textContent;
        emptyEl.classList.remove('d-none');
        loadingEl.classList.add('d-none');
        errorEl.classList.add('d-none');
    });
})();
</script>
@endpush
