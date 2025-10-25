<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <h1 class="h5 mb-0 text-gray-800"><?= htmlspecialchars($title ?? 'Kasir UMKM') ?></h1>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown">
                <i class="fas fa-search fa-fw"></i>
            </a>
        </li>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Usaha Kamu</span>
                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name=Kasir+UMKM&background=4e73df&color=fff" alt="Avatar">
            </a>
        </li>
    </ul>
</nav>
