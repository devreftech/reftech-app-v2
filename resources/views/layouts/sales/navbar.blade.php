<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="mdi mdi-menu mdi-24px"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler fw-normal px-0" href="javascript:void(0);">
                    <i class="mdi mdi-magnify mdi-24px scaleX-n1-rtl"></i>
                    <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                </a>
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <!-- Style Switcher -->
            <li class="nav-item me-1 me-xl-0">
                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon style-switcher-toggle hide-arrow"
                    href="javascript:void(0);">
                    <i class="mdi mdi-24px"></i>
                </a>
            </li>
            <!--/ Style Switcher -->
            <!-- Notification -->
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-1">
                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                    href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false">
                    <i class="mdi mdi-bell-outline mdi-24px"></i>
                    @php
                        $hasBadge = false;
                        if (Auth::user()?->role == 'Admin' && @$unreadCommentAdmin && $unreadCommentAdmin->count() >= 1) $hasBadge = true;
                        if (Auth::user()?->role != 'Admin' && @$unreadComment && $unreadComment->count() >= 1) $hasBadge = true;
                        if (@$prMentions && $prMentions->count() >= 1) $hasBadge = true;
                        if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$pendingCancelQuotes && $pendingCancelQuotes->count() >= 1) $hasBadge = true;
                    @endphp
                    @if ($hasBadge)
                        <span class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h6 class="mb-0 me-auto">Notification</h6>
                            @if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$pendingCancelQuotes && $pendingCancelQuotes->count() > 0)
                                <span class="badge rounded-pill bg-danger me-1">{{ $pendingCancelQuotes->count() }} Cancel PO</span>
                            @endif
                            @if (Auth::user()?->role == 'Admin')
                                @if (@$unreadCommentAdmin)
                                    @if ($unreadCommentAdmin->count() > 1)
                                        <span
                                            class="badge rounded-pill bg-label-primary">{{ $unreadCommentAdmin->count() }}
                                            New</span>
                                    @endif
                                @endif
                            @else
                                @if (@$unreadComment)
                                    @if ($unreadComment->count() > 1)
                                        <span class="badge rounded-pill bg-label-primary">{{ $unreadComment->count() }}
                                            New</span>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                        <ul class="list-group list-group-flush">
                            {{-- Notifikasi Cancel PO untuk Accounting / Admin / Finance --}}
                            @if (in_array(Auth::user()?->role, ['Admin', 'Accounting', 'Finance']) && @$pendingCancelQuotes && $pendingCancelQuotes->count() > 0)
                                @foreach ($pendingCancelQuotes as $cancelQ)
                                    <a href="{{ route('unit-quotation.show', $cancelQ->id) }}"
                                        class="list-group-item list-group-item-action dropdown-notifications-item" style="background:#fff0f0;">
                                        <div class="d-flex gap-2">
                                            <div class="flex-shrink-0">
                                                <div class="avatar me-1">
                                                    <span class="avatar-initial rounded-circle bg-danger text-white">
                                                        <i class="mdi mdi-alert-circle-outline"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                                                <h6 class="mb-1 text-truncate text-danger fw-bold">{{ $cancelQ->no_quote }}</h6>
                                                <small class="text-truncate text-body">
                                                    <strong class="text-dark">{{ $cancelQ->sales?->name }}</strong> mengajukan pembatalan PO ({{ $cancelQ->client?->company ?? '-' }})
                                                </small>
                                            </div>
                                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($cancelQ->updated_at)->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                            @if (Auth::user()?->role == 'Admin')
                                @if (@$commentAdmin)
                                    @foreach ($commentAdmin as $item)
                                        <a href="{{ route('quotation.show', $item->idQ) }}#viewComment"
                                            class="view-quote {{ $item->level == 1 ? 'bg-label-secondary' : '' }}"
                                            data-id="{{ $item->idC }}" data-quotation="{{ $item->idQ }}">
                                            <li
                                                class="list-group-item list-group-item-action dropdown-notifications-item view-quote">
                                                <div class="d-flex gap-2">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar me-1">
                                                            <img src="{{ url('') . '/' . $item->image }}" alt
                                                                class="w-px-40 h-auto rounded-circle" />
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                                                        <h6 class="mb-1 text-truncate">{{ $item->no_quote }}</h6>
                                                        <small class="text-truncate text-body">New Comment on your
                                                            Quotations! </small>
                                                    </div>
                                                    @php
                                                        $date = \Carbon\Carbon::parse($item->date);
                                                    @endphp
                                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                                        <small
                                                            class="text-muted">{{ $date->diffInHours(Carbon\Carbon::now()) > 24 ? $date->format('d M y h:i:s') : $date->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </li>
                                        </a>
                                    @endforeach
                                @endif
                            @else
                                @if (@$comment)
                                    @foreach ($comment as $item)
                                        @php
                                            if ($item->type == 'prospect') {
                                                $route = 'prospect.show';
                                            } else {
                                                $route = 'quotation.show';
                                            }
                                        @endphp
                                        <a href="{{ route($route, $item->idQ) }}#viewComment"
                                            class="view-{{ $item->type == 'prospect' ? 'prospect' : 'quote' }} {{ $item->level == 1 ? 'bg-label-secondary' : '' }}"
                                            data-id="{{ $item->idC }}" data-quotation="{{ $item->idQ }}">
                                            <li
                                                class="list-group-item list-group-item-action dropdown-notifications-item view-quote">
                                                <div class="d-flex gap-2">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar me-1">
                                                            <img src="{{ url('') . '/' . $item->image }}" alt
                                                                class="w-px-40 h-auto rounded-circle" />
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                                                        <h6 class="mb-1 text-truncate">
                                                            {{ $item->type == 'prospect' ? 'Prospect ' : '' }}{{ $item->no_quote }}
                                                        </h6>
                                                        <small class="text-truncate text-body">New Comment on your
                                                            {{ $item->type == 'prospect' ? 'Prospect!' : 'Quotations' }}!
                                                        </small>
                                                    </div>
                                                    @php
                                                        $date = \Carbon\Carbon::parse($item->date);
                                                    @endphp
                                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                                        <small
                                                            class="text-muted">{{ $date->diffInHours(Carbon\Carbon::now()) > 24 ? $date->format('d M y h:i:s') : $date->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </li>
                                        </a>
                                    @endforeach
                                @endif
                            @endif
                        </ul>
                    </li>
                            {{-- Notifikasi PR Discussion Mention --}}
                            @if (@$prMentions && $prMentions->count() > 0)
                                @foreach ($prMentions as $pm)
                                    <a href="{{ route('purchase-request.show', $pm->discussion->id_pending) }}#diskusi"
                                        class="view-pr-mention {{ $pm->level == '0' ? 'bg-label-secondary' : '' }}"
                                        data-mention-id="{{ $pm->id }}">
                                        <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                            <div class="d-flex gap-2">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar me-1">
                                                        <span class="avatar-initial rounded-circle bg-label-warning">
                                                            <i class="mdi mdi-at"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                                                    <h6 class="mb-1 text-truncate">PR #{{ $pm->discussion->pending->no_pending ?? $pm->discussion->id_pending }}</h6>
                                                    <small class="text-truncate text-body">Kamu di-mention dalam diskusi</small>
                                                </div>
                                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($pm->created_at)->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </li>
                                    </a>
                                @endforeach
                            @endif

                    <li class="dropdown-menu-footer border-top p-2">
                        <a href="{{ route('index.notif') }}" class="btn btn-primary d-flex justify-content-center">
                            View all notifications
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ Notification -->

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ url('') . '/' . Auth::user()?->image }}" alt
                            class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                @if (Auth::user())

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show', Auth::user()?->id) }}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ url('') . '/' . Auth::user()?->image }}" alt
                                                class="w-px-40 h-auto rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">{{ Auth::user()?->name }}</span>
                                        <small class="text-muted">{{ Auth::user()?->role }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show', Auth::user()?->id) }}">
                                <i class="mdi mdi-account-outline me-2"></i>
                                <span class="align-middle">My Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit', Auth::user()?->id) }}">
                                <i class="mdi mdi-cog-outline me-2"></i>
                                <span class="align-middle">Settings</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"
                                target="_blank">
                                <i class="mdi mdi-logout me-2"></i>
                                <span class="align-middle">Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                @endif
            </li>
            <!--/ User -->
        </ul>
    </div>

    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper d-none">
        <input type="text" class="form-control search-input container-fluid border-0" placeholder="Search..."
            aria-label="Search..." />
        <i class="mdi mdi-close search-toggler cursor-pointer"></i>
    </div>
</nav>
