<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{!! route('dashboard') !!}" class="app-brand-link">
            <span class="app-brand-logo demo" style="height: unset">
                <img style="height: 60px; object-fit: fill" src="{!! asset('assets/img/logo.png') !!}" alt="walix">
            </span>
            <div style="display: flex">
                <span class="app-brand-text demo menu-text fw-bold">Ria Busana</span>
                {{-- <span style="font: 100" class=" demo menu-text fw-bold">Marketing Tools</span> --}}
            </div>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item  {{ Route::is('dashboard') ? 'active' : '' }}">
            <a href="{!! route('dashboard') !!}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-apps" style="margin-bottom: 2px;"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <div class="menu-link px-0">
                <select class="form-control main-device" style="cursor: pointer;">
                </select>
            </div>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Main</span>
        </li>
        <li class="menu-item {{ Route::is('responder*') ? 'active' : '' }}">
            <a href="{!! route('responder') !!}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-message-2-code" style="margin-bottom: 2px;"></i>
                <div data-i18n="Auto Responders">Auto Responders</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('phonebook*') ? 'active' : '' }}">
            <a href="{!! route('phonebook') !!}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-address-book" style="margin-bottom: 2px;"></i>
                <div data-i18n="Phone Book">Phone Book</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('campaigns.*') ? 'active' : '' }}">
            <a href="{!! route('campaigns.index') !!}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-mail" style="margin-bottom: 2px;"></i>
                <div data-i18n="Campaigns">Campaigns</div>
            </a>
        </li>
        {{-- <li class="menu-item {{ Route::is('broadcast*') ? 'active' : '' }}">
            <a href="{!! route('broadcast') !!}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-broadcast" style="margin-bottom: 2px;"></i>
                <div data-i18n="Broadcast">Broadcast</div>
            </a>
        </li> --}}
        <li class="menu-item {{ Route::is('broadcast*') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-broadcast" style="margin-bottom: 2px;"></i>
                <div data-i18n="Broadcast">Broadcast</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{!! route('broadcast.location') !!}" class="menu-link">
                        <div data-i18n="Location">Location</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{!! route('broadcast.birthday') !!}" class="menu-link">
                        <div data-i18n="Birthday">Birthday</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="{!! route('admin.settings') !!}" class="menu-link">
                        <div data-i18n="Settings">Settings</div>
                    </a>
                </li> --}}
            </ul>
        </li>
        <li class="menu-item {{ Route::is('single*') ? 'active' : '' }}">
            <a href="{!! route('single') !!}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-brand-telegram" style="margin-bottom: 2px;"></i>
                <div data-i18n="Single Sender">Single Sender</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('apidocs*') ? 'active' : '' }}">
            <a href="{!! route('apidocs') !!}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-flame" style="margin-bottom: 2px;"></i>
                <div data-i18n="Rest Api">Rest Api</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('plugins*') ? 'active' : '' }}">
            <a href="{!! route('plugins') !!}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-3d-cube-sphere" style="margin-bottom: 2px;"></i>
                <div data-i18n="Plugins & Integration">Plugins & Integration</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Other</span>
        </li>
        <li class="menu-item {{ Route::is('files') ? 'active' : '' }}">
            <a href="{!! route('files') !!}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-folder" style="margin-bottom: 2px;"></i>
                <div data-i18n="File Manager">File Manager</div>
            </a>
        </li>
        @if ($auth->role == 'admin')
            <li class="menu-item {{ Route::is('admin*') ? 'active' : '' }}">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-brand-tabler" style="margin-bottom: 2px;"></i>
                    <div data-i18n="Admin Menu">Admin Menu</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{!! route('admin.users') !!}" class="menu-link">
                            <div data-i18n="Manage Users">Manage Users</div>
                        </a>
                    </li>
                    {{-- <li class="menu-item">
                        <a href="{!! route('admin.settings') !!}" class="menu-link">
                            <div data-i18n="Settings">Settings</div>
                        </a>
                    </li> --}}
                </ul>
            </li>
        @endif
        <li class="menu-item">
            <a href="https://velixs.com" class="menu-link">
                <i class="menu-icon tf-icons ti ti-code" style="margin-bottom: 2px;"></i>
                <div data-i18n="Version 3.0.0">Version 4.x</div>
                <div class="badge bg-label-success rounded-pill ms-auto">Current</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{!! route('logout') !!}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-logout" style="margin-bottom: 2px;"></i>
                <div data-i18n="Log out">Log out</div>
            </a>
        </li>
    </ul>
</aside>
