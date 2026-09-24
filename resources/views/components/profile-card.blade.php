<div class="profile-card" id="profileCard">

    <button type="button" class="user-profile" onclick="toggleProfileMenu()">

        <div class="user-avatar">
            @if (Auth::user()->foto)
                <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Foto de perfil">
            @else
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            @endif
        </div>

        <div class="user-info">
            <strong>{{ Auth::user()->name }} {{ Auth::user()->apellidos }}</strong>
            <span>{{ ucfirst(Auth::user()->rol) }}</span>
        </div>

    </button>

    <div class="profile-dropdown" id="profileDropdown">

        <div class="profile-dropdown-header">
            <div class="user-avatar" style="width: 56px; height: 56px; font-size: 20px;">
                @if (Auth::user()->foto)
                    <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Foto de perfil">
                @else
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                @endif
            </div>

            <div>
                <strong>{{ Auth::user()->name }} {{ Auth::user()->apellidos }}</strong>
                <p class="profile-email">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <div class="profile-dropdown-body">

            <div class="profile-field">
                <span>Usuario</span>
                <strong>{{ Auth::user()->usuario }}</strong>
            </div>

            <div class="profile-field">
                <span>Rol</span>
                <strong>{{ ucfirst(Auth::user()->rol) }}</strong>
            </div>

            @if (session('status'))
                <p style="color: #22c55e; font-size: 13px; margin-top: 8px;">
                    {{ session('status') }}
                </p>
            @endif

            <form method="POST" action="/perfil/foto" enctype="multipart/form-data" style="margin-top: 14px;">
                @csrf
                <label class="profile-upload-label">
                    Cambiar foto de perfil
                    <input type="file" name="foto" accept="image/*" onchange="this.form.submit()">
                </label>
            </form>

        </div>

        <form method="POST" action="/logout" class="profile-dropdown-footer">
            @csrf
            <button type="submit" class="nav-item" style="width: 100%; background: none; border: none; cursor: pointer; text-align: left;">
                <span>⏻</span>
                Cerrar sesión
            </button>
        </form>

    </div>

</div>

<script>
    function toggleProfileMenu() {
        document.getElementById('profileDropdown').classList.toggle('open');
    }

    document.addEventListener('click', function (event) {
        const card = document.getElementById('profileCard');
        if (card && !card.contains(event.target)) {
            document.getElementById('profileDropdown').classList.remove('open');
        }
    });
</script>