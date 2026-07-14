<!DOCTYPE html>
<html lang="en">
    <head>
        @php
            $themeSetting = $setting ?? \App\Models\Setting::firstOrEmpty();
            $primary = $themeSetting->primary_color ?? '#3386B5';
            $secondary = $themeSetting->secondary_color ?? '#00205B';
            $neutral = $themeSetting->neutral_color ?? '#58A9C9';
            $fontFamily = $themeSetting->font_family ?? 'Poppins';
            $googleFontParam = str_replace(' ', '+', $fontFamily);
        @endphp
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dashboard') — Mercy Tides</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family={{ $googleFontParam }}:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="{{ asset('assets/admin/css/styles.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/admin/css/admin-refine.css') }}" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --brand-primary: {{ $primary }};
                --brand-secondary: {{ $secondary }};
                --brand-neutral: {{ $neutral }};
                --brand-yellow: {{ $primary }};
                --brand-green: {{ $secondary }};
                --brand-blue: {{ $neutral }};
                --brand-dark: #00205B;
            }

            body {
                font-family: "{{ $fontFamily }}", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }
        </style>
        @stack('head')
    </head>
    <body class="sb-nav-fixed" data-turbo="true">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="{{ route('redirects') }}">Mercy Tides</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" type="button" aria-label="Toggle sidebar"><i class="fas fa-bars"></i></button>

            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" action="#" onsubmit="return false;">
                <div class="input-group">
                    <input class="form-control" type="search" placeholder="Search…" aria-label="Search" autocomplete="off" />
                    <button class="btn btn-primary" type="button" disabled title="Search is not wired yet"><i class="fas fa-search"></i></button>
                </div>
            </form>

            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4 align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" id="adminUserDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user fa-fw me-1"></i>
                        <span class="d-none d-lg-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminUserDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('home') }}" target="_blank" rel="noopener">View site</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" data-no-sweet-submit="true">
                                @csrf
                                <button type="submit" class="dropdown-item">Log out</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        @yield('content')

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('assets/admin/js/scripts.js') }}"></script>
        @php
            $imageUploadConfig = [
                'maxBytes' => (int) config('image.max_bytes'),
                'maxWidth' => (int) config('image.max_width'),
                'maxHeight' => (int) config('image.max_height'),
                'initialQuality' => ((int) config('image.initial_quality')) / 100,
                'minQuality' => ((int) config('image.min_quality')) / 100,
                'presets' => config('image.presets'),
            ];
        @endphp
        <script>
            window.MercyTidesImageUpload = @json($imageUploadConfig);
        </script>
        <script src="{{ asset('assets/admin/js/image-upload-preview.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/admin/js/datatables-simple-demo.js') }}"></script>

        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
        <script>
            function initAdminAlerts() {
                if (document.documentElement.hasAttribute('data-turbo-preview')) {
                    return;
                }

                const successMessage = @json(session('success'));
                const errorMessage = @json(session('error'));
                const warningMessage = @json(session('warning'));
                const hasValidationErrors = {{ $errors->any() ? 'true' : 'false' }};
                const pendingFeedback = sessionStorage.getItem('admin:pending-feedback') === '1';
                const pageKey = window.location.pathname + window.location.search;
                const successKey = successMessage ? ('success:' + pageKey + ':' + successMessage) : null;
                const errorKey = errorMessage ? ('error:' + pageKey + ':' + errorMessage) : null;
                const warningKey = warningMessage ? ('warning:' + pageKey + ':' + warningMessage) : null;
                const validationKey = 'validation:' + pageKey;

                if (successMessage && pendingFeedback && sessionStorage.getItem(successKey) !== 'shown') {
                    sessionStorage.setItem(successKey, 'shown');
                    sessionStorage.removeItem('admin:pending-feedback');
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: successMessage,
                        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--brand-primary') || '#3386B5',
                    });
                } else if (errorMessage && pendingFeedback && sessionStorage.getItem(errorKey) !== 'shown') {
                    sessionStorage.setItem(errorKey, 'shown');
                    sessionStorage.removeItem('admin:pending-feedback');
                    Swal.fire({
                        icon: 'error',
                        title: 'Something went wrong',
                        text: errorMessage,
                    });
                } else if (warningMessage && pendingFeedback && sessionStorage.getItem(warningKey) !== 'shown') {
                    sessionStorage.setItem(warningKey, 'shown');
                    sessionStorage.removeItem('admin:pending-feedback');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Notice',
                        text: warningMessage,
                    });
                } else if (hasValidationErrors && sessionStorage.getItem(validationKey) !== 'shown') {
                    sessionStorage.setItem(validationKey, 'shown');
                    sessionStorage.removeItem('admin:pending-feedback');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation error',
                        text: 'Please correct the highlighted fields and try again.',
                    });
                } else if (!successMessage && !errorMessage && !warningMessage) {
                    sessionStorage.removeItem('admin:pending-feedback');
                }
            }

            function initAdminSubmitFeedback() {
                document.querySelectorAll('form:not([data-no-sweet-submit])').forEach((form) => {
                    if (form.dataset.sweetBound === 'true') {
                        return;
                    }
                    form.dataset.sweetBound = 'true';
                    form.addEventListener('submit', () => {
                        sessionStorage.setItem('admin:pending-feedback', '1');
                        Swal.fire({
                            title: 'Submitting...',
                            text: 'Please wait while we save your changes.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading(),
                        });
                    });
                });
            }

            function initAdminSummernote() {
                if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.summernote === 'undefined') {
                    return;
                }
                window.jQuery('form textarea[data-editor="rich"]').each(function () {
                    const $el = window.jQuery(this);
                    // Skip summernote-generated/internal textareas
                    if ($el.hasClass('note-codable') || $el.hasClass('note-input') || $el.closest('.note-editor').length) {
                        return;
                    }
                    if ($el.hasClass('swal2-textarea') || $el.closest('.swal2-container').length) {
                        return;
                    }
                    if ($el.data('summernote-initialized') === true) {
                        return;
                    }
                    $el.summernote({
                        height: 220,
                        placeholder: 'Write content here...',
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'italic', 'underline', 'clear']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['insert', ['link', 'picture']],
                            ['view', ['codeview']]
                        ]
                    });
                    $el.data('summernote-initialized', true);
                });
            }

            function ensureModalDismissButton(container, options) {
                if (!container) {
                    return;
                }

                let dismissBtn = container.querySelector('[data-bs-dismiss="modal"], [data-dismiss="modal"]');
                if (dismissBtn) {
                    dismissBtn.setAttribute('data-bs-dismiss', 'modal');
                    dismissBtn.setAttribute('type', 'button');
                    dismissBtn.setAttribute('aria-label', dismissBtn.getAttribute('aria-label') || 'Close');
                    return;
                }

                dismissBtn = document.createElement('button');
                dismissBtn.type = 'button';
                dismissBtn.setAttribute('data-bs-dismiss', 'modal');
                dismissBtn.setAttribute('aria-label', 'Close');

                if (options && options.variant === 'header') {
                    dismissBtn.className = 'btn-close';
                } else {
                    dismissBtn.className = 'btn btn-outline-secondary';
                    dismissBtn.textContent = options && options.label ? options.label : 'Close';
                }

                container.appendChild(dismissBtn);
            }

            function cleanupOrphanModalBackdrops() {
                const openModals = document.querySelectorAll('.modal.show').length;
                if (openModals > 0) {
                    return;
                }

                document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');
            }

            function initAdminModalCloseControls() {
                document.querySelectorAll('.modal').forEach((modal) => {
                    modal.setAttribute('data-bs-backdrop', modal.getAttribute('data-bs-backdrop') || 'true');
                    modal.setAttribute('data-bs-keyboard', modal.getAttribute('data-bs-keyboard') || 'true');

                    let header = modal.querySelector('.modal-header');
                    if (!header) {
                        const content = modal.querySelector('.modal-content') || modal;
                        header = document.createElement('div');
                        header.className = 'modal-header';
                        const title = document.createElement('h5');
                        title.className = 'modal-title';
                        title.textContent = modal.getAttribute('aria-label') || 'Dialog';
                        header.appendChild(title);
                        content.prepend(header);
                    }

                    ensureModalDismissButton(header, { variant: 'header' });

                    // Normalize legacy Bootstrap 4 dismiss attributes.
                    modal.querySelectorAll('[data-dismiss="modal"]').forEach((btn) => {
                        btn.setAttribute('data-bs-dismiss', 'modal');
                        btn.setAttribute('type', 'button');
                    });

                    const footer = modal.querySelector('.modal-footer');
                    if (footer) {
                        ensureModalDismissButton(footer, { label: 'Close' });
                    } else {
                        const body = modal.querySelector('.modal-body');
                        const hasBodyDismiss = body && body.querySelector('[data-bs-dismiss="modal"], .admin-modal__fallback-actions');
                        if (body && !hasBodyDismiss) {
                            const actions = document.createElement('div');
                            actions.className = 'd-flex justify-content-end gap-2 mt-3 admin-modal__fallback-actions';
                            const cancelBtn = document.createElement('button');
                            cancelBtn.type = 'button';
                            cancelBtn.className = 'btn btn-outline-secondary';
                            cancelBtn.setAttribute('data-bs-dismiss', 'modal');
                            cancelBtn.textContent = 'Cancel';
                            actions.appendChild(cancelBtn);
                            body.appendChild(actions);
                        }
                    }
                });

                cleanupOrphanModalBackdrops();
            }

            document.addEventListener('DOMContentLoaded', initAdminAlerts);
            document.addEventListener('DOMContentLoaded', initAdminSubmitFeedback);
            document.addEventListener('DOMContentLoaded', initAdminSummernote);
            document.addEventListener('DOMContentLoaded', initAdminModalCloseControls);
            document.addEventListener('turbo:load', initAdminAlerts);
            document.addEventListener('turbo:load', initAdminSubmitFeedback);
            document.addEventListener('turbo:load', initAdminSummernote);
            document.addEventListener('turbo:load', initAdminModalCloseControls);
            document.addEventListener('shown.bs.tab', initAdminSummernote);
            document.addEventListener('hidden.bs.modal', cleanupOrphanModalBackdrops);
            document.addEventListener('turbo:before-cache', cleanupOrphanModalBackdrops);
            document.addEventListener('turbo:before-render', cleanupOrphanModalBackdrops);

            document.addEventListener('turbo:load', () => {
                const nav = document.getElementById('layoutSidenav_nav');
                if (nav) {
                    nav.setAttribute('data-turbo-permanent', '');
                }
            });
        </script>

        @yield('scripts')
        @stack('scripts')
    </body>
</html>
