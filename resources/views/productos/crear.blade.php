<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Nuevo producto</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="productos" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Nuevo producto</h2>
                    <p>Registra un producto en el catálogo</p>
                </div>

                <x-profile-card />
            </header>

            @if ($errors->any())
                <div style="color: #ef4444; margin-bottom: 16px;">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" enctype="multipart/form-data" action="/productos" class="form-card">
                @csrf

                <div class="input-group">
                    <label for="codigo">Código</label>
                    <input type="text" id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                </div>

                <div class="input-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                </div>

                <div class="input-group">
                    <label for="categoria">Categoría</label>
                    <input type="text" id="categoria" name="categoria" value="{{ old('categoria') }}">
                </div>

                <div class="input-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion') }}">
                </div>

                <div class="input-group">
                    <label for="precio_compra">Precio de compra</label>
                    <input type="number" step="0.01" min="0" id="precio_compra" name="precio_compra" value="{{ old('precio_compra') }}" required>
                </div>

                <div class="input-group">
                    <label for="precio_venta">Precio de venta</label>
                    <input type="number" step="0.01" min="0" id="precio_venta" name="precio_venta" value="{{ old('precio_venta') }}" required>
                </div>

                <div class="input-group">
                    <label for="existencia">Existencia inicial</label>
                    <input type="number" min="0" id="existencia" name="existencia" value="{{ old('existencia', 0) }}" required>
                </div>

                <div class="input-group">
                    <label for="stock_minimo">Stock mínimo</label>
                    <input type="number" min="0" id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', 10) }}" required>
                </div>

                <div class="input-group" style="margin-top:20px">
    <label for="imagen_archivo">Fotografía desde mi equipo</label>
    <input type="file"
           id="imagen_archivo"
           name="imagen_archivo"
           accept="image/jpeg,image/png,image/webp">
    <small>JPG, PNG o WEBP. Máximo 2 MB.</small>
</div>

<div class="input-group">
    <label for="imagen_url">O utilizar una imagen de Internet</label>
    <input type="url"
           id="imagen_url"
           name="imagen_url"
           placeholder="https://ejemplo.com/imagen.jpg"
           value="{{ old('imagen_url') }}">
</div>

<div class="input-group">
    <label>Vista previa</label>
    <img id="vista_previa"
         alt="Vista previa del producto"
         style="display:none;max-width:220px;max-height:180px;object-fit:contain;border-radius:12px;margin-top:10px">
</div>


            
    <button type="submit"
            class="btn-process"
            style="display:block;
                   width:100%;
                   padding:18px;
                   margin-top:24px;
                   background:#60d5f8;
                   color:#10151c;
                   border:none;
                   border-radius:12px;
                   font-size:16px;
                   font-weight:bold;
                   cursor:pointer;">
        Registrar producto
    </button>

</form>

        </main>

    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const archivo = document.getElementById('imagen_archivo');
    const url = document.getElementById('imagen_url');
    const previa = document.getElementById('vista_previa');
    const eliminar = document.getElementById('eliminar_imagen');

    function mostrar(src) {
        if (!src) {
            previa.removeAttribute('src');
            previa.style.display = 'none';
            return;
        }
        previa.src = src;
        previa.style.display = 'block';
    }

    archivo.addEventListener('change', () => {
        const foto = archivo.files[0];
        if (!foto) return;

        if (!['image/jpeg','image/png','image/webp'].includes(foto.type) ||
            foto.size > 2 * 1024 * 1024) {
            alert('Selecciona una imagen JPG, PNG o WEBP de máximo 2 MB.');
            archivo.value = '';
            return;
        }

        url.value = '';
        if (eliminar) eliminar.checked = false;

        const lector = new FileReader();
        lector.onload = e => mostrar(e.target.result);
        lector.readAsDataURL(foto);
    });

    url.addEventListener('input', () => {
        if (url.value.trim()) {
            archivo.value = '';
            if (eliminar) eliminar.checked = false;
            mostrar(url.value.trim());
        } else {
            mostrar('');
        }
    });

    if (eliminar) {
        eliminar.addEventListener('change', () => {
            if (eliminar.checked) {
                archivo.value = '';
                url.value = '';
                mostrar('');
            } else {
                mostrar(previa.dataset.original || '');
            }
        });
    }

    previa.addEventListener('error', () => {
        previa.style.display = 'none';
    });
});
</script>
</body>

</html>