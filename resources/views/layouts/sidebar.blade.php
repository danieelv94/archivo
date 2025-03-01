<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ccleh.dashboard') ? '' : 'collapsed' }}"
               href="{{ route('ccleh.dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <!-- End Dashboard Nav -->

        <li class="nav-heading">Archivo de trámite</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ccleh.inventario.captura.index') ? '' : 'collapsed' }}"
               href="{{ route('ccleh.inventario.captura.index') }}">
                <i class="bi bi-folder2-open"></i>
                <span>Inventario Documental</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ccleh.inventario.reporte.index') ? '' : 'collapsed' }}" href="{{ route('ccleh.inventario.reporte.index') }}">
                <i class="bi bi-file-earmark-ruled"></i>
                <span>Reporte de Inventarios</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('transferencia.primaria.index') ? '' : 'collapsed' }}" href="{{ route('transferencia.primaria.index') }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Transferencia primaria</span>
            </a>
        </li>

        @role('capturista')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ccleh.cadido.ver.listado') ? '' : 'collapsed' }}" href="{{ route('ccleh.cadido.ver.listado') }}">
                <i class="bi bi-card-list"></i>
                <span>CADIDO</span>
            </a>
        </li>
        @endrole


        {{-- MENU ADMINISTRADOR DE ARCHIVO --}}
        @role('administrador de archivo|soporte|sysAdmin')
            <li><hr class="dropdown-divider"></li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is(['ccleh/admin/calendario*','ccleh/admin/supervision*','ccleh/admin/series-autorizadas*','ccleh/cadido']) ? '' : 'collapsed' }}" data-bs-target="#archivo-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-archive"></i>
                    <span>Archivo  </span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="archivo-nav" class="nav-content collapse {{ request()->is(['ccleh/admin/calendario*','ccleh/admin/supervision*','ccleh/admin/series-autorizadas*','ccleh/cadido']) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                    <li>
                        <a class="{{ request()->routeIs('ccleh.admin.calendario.index') ? 'active' : '' }}" href="{{ route('ccleh.admin.calendario.index') }}">
                            {{-- <i class="bi  bi-calendar"></i> --}}
                            <i class="bi bi-circle"></i>
                            <span>Calendario</span>
                        </a>
                    </li>

                    <li>
                        <a class="{{ request()->routeIs('ccleh.admin.supervision.index') ? 'active' : '' }}" href="{{ route('ccleh.admin.supervision.index') }}">
                            <i class="bi bi-circle"></i>
                            <span>Panel de Supervisión</span>
                        </a>
                    </li>

                    <li>
                        <a class="{{ request()->routeIs('ccleh.admin.series-autorizadas.index') ? 'active' : '' }}" href="{{ route('ccleh.admin.series-autorizadas.index') }}">
                            {{-- <i class="bi  bi-check2-square"></i> --}}
                            <i class="bi bi-circle"></i>
                            <span>Series autorizadas</span>
                        </a>
                    </li>

                    <li>
                        <a class="{{ request()->routeIs('ccleh.cadido.index') ? 'active' : '' }}" href="{{ route('ccleh.cadido.index') }}">
                            <i class="bi bi-circle"></i>
                            <span>CADIDO</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endrole


        {{-- MENU SOPORTE --}}
        @role('soporte|sysAdmin')
            <li><hr class="dropdown-divider"></li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is(['ccleh/usuarios*','ccleh/areas*','ccleh/departamentos*']) ? '' : 'collapsed' }}" data-bs-target="#soporte-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-journal-text"></i>
                    <span>Catálogos  </span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="soporte-nav" class="nav-content collapse {{ request()->is(['ccleh/usuarios*','ccleh/areas*','ccleh/departamentos*']) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('ccleh.usuarios.index') }}" class="{{ request()->routeIs('ccleh.usuarios.index') ? 'active' : '' }}">
                            <i class="bi bi-circle"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('ccleh.areas.index') }}" class="{{ request()->routeIs('ccleh.areas.index') ? 'active' : '' }}">
                            <i class="bi bi-circle"></i>
                            <span>Unidades adminstrativas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('ccleh.departamentos.index') }}" class="{{ request()->routeIs('ccleh.departamentos.index') ? 'active' : '' }}">
                            <i class="bi bi-circle"></i>
                            <span>Departamentos</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endrole
        <!-- End Catalogs -->

        <li><hr class="dropdown-divider"></li>
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#plantillas-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-file-earmark-arrow-down"></i>
                <span>Plantillas  </span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="plantillas-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ asset('excel_plantillas/plantilla_sabana.xlsx') }}">
                        <i class="bi bi-circle"></i>
                        <span>Sabana</span>
                    </a>
                </li>
                <li>
                    <a href="{{ asset('excel_plantillas/plantilla_inv-doc-v2.xlsx') }}">
                        <i class="bi bi-circle"></i>
                        <span>Inventario Documental</span>
                    </a>
                </li>
                <li>
                    <a href="{{ asset('excel_plantillas/plantilla_inv-transferencia.xlsx') }}">
                        <i class="bi bi-circle"></i>
                        <span>Transferencia primaria</span>
                    </a>
                </li>
                <li>
                    <a href="{{ asset('excel_plantillas/plantilla_cadido.xlsx') }}">
                        <i class="bi bi-circle"></i>
                        <span>CADIDO</span>
                    </a>
                </li>
                <li>
                    <a href="{{ asset('excel_plantillas/etiquetas.xlsx') }}">
                        <i class="bi bi-circle"></i>
                        <span>Etiquetas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ asset('excel_plantillas/plantilla_caratula-cajas.xlsx') }}">
                        <i class="bi bi-circle"></i>
                        <span>Carátula Cajas</span>
                    </a>
                </li>
            </ul>
        </li>

    </ul>

</aside>
