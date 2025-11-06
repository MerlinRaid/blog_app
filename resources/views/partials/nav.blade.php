<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name','Blog') }}</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMain" aria-controls="navbarMain"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="navbarMain" class="collapse navbar-collapse">
            <!-- Vasak -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('blog.index') }}">Blogi</a></li>

                {{-- Authori lühitee (enda postitused) --}}
                {{-- @role('Author')
                    <li class="nav-item dropdown">
                    <a id="authorDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Autor
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="authorDropdown">
                        <li><a class="dropdown-item" href="{{ route('author.dashboard') }}">Avaleht</a></li>
                        <li><a class="dropdown-item" href="{{ route('author.posts.index') }}">Minu postitused</a></li>
                        <li><a class="dropdown-item" href="{{ route('author.posts.create') }}">+ Uus postitus</a></li>
                    </ul>
                    </li>
                @endrole --}}


                {{-- Moderatorile (ja Adminile) sisuhaldus – kui soovid moderaatorile ka oma ala teha, lisa siia viide --}}
                {{-- Admin + Moderator näevad sama admin-menüüd --}}
                @role('Admin|Moderator')
                <li class="nav-item dropdown">
                    <a id="adminDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                        @if (Auth::user()->hasRole('Admin'))
                            Admin
                        @elseif (Auth::user()->hasRole('Moderator'))
                            Moderator
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Avaleht</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.posts.index') }}">Postitused</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.posts.create') }}">+ Uus postitus</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.comments.index') }}">Kommentaarid</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}">Kategooriad</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.tags.index') }}">Sildid</a></li>

                        {{-- Kasutajate haldus ainult Adminile --}}
                        @role('Admin')
                            <li><hr class="dropdown-divider"></li>
                            {{-- <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Kasutajad</a></li> --}}
                        @endrole
                    </ul>
                </li>
                @endrole

            


                {{-- Adminile administraatori paneel --}}
                {{-- @role('Admin')
                    <li class="nav-item dropdown">
                        <a id="adminDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Avaleht</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.posts.index') }}">Postitused</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.posts.create') }}">+ Uus postitus</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.comments.index') }}">Kommentaarid</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Kasutajad</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}">Kategooriad</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.tags.index') }}">Sildid</a></li>
                        </ul>
                    </li>
                @endrole --}}
            </ul>

            <!-- Parem -->
            <ul class="navbar-nav ms-auto">
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @endif
                    @if (Route::has('register'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @role('Admin')
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Administraatori paneel</a>
                            @endrole

                            @role('Moderator')
                                <a class="dropdown-item" href="{{ url('/admin') }}">Sisu haldus</a>
                            @endrole

                            @role('Author')
                                <a class="dropdown-item" href="{{ url('/dashboard') }}">Minu postitused</a>
                            @endrole

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
