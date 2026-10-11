<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Editar producto</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="productos" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Editar producto</h2>
                    <p>{{ $producto->codigo }} — {{ $producto->nombre }}</p>
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

            <form method="POST" action="/productos/{{ $producto->id }}" class="form-card">
                @csrf
                @method('PUT')

                <div class="input-group">
                    <label for="codigo">Código</label>
                    <input type="text" id="codigo" name="codigo" value="{{ old('codigo', $producto->codigo) }}" maxlength="50" required>
                </div>

                <div class="input-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" maxlength="255" required>
                </div>

               <div class="input-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                </div>

                {{-- Categoría con select --}}
                <div class="input-group">
                    <label for="id_categoria">Categoría</label>
<select id="id_categoria" name="id_categoria" required>
    <option value="">— Selecciona —</option>
    @foreach ($categorias as $cat)
        <option value="{{ $cat->id }}" {{ old('id_categoria', $producto->id_categoria) == $cat->id ? 'selected' : '' }}>
            {{ $cat->nombre }}
        </option>
    @endforeach
</select>
                </div>

                {{-- Proveedor con select --}}
                <div class="input-group">
                    <label for="id_proveedor">Proveedor</label>
                    <select id="id_proveedor" name="id_proveedor">
                        <option value="">— Sin proveedor —</option>
                        @foreach ($proveedores as $prov)
                            <option value="{{ $prov->id_proveedor }}" @selected(old('id_proveedor', $producto->id_proveedor) == $prov->id_proveedor)>
                                {{ $prov->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group">
                    <label for="precio_compra">Precio de compra</label>
                    <input type="number" step="0.01" min="0" id="precio_compra" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra) }}" required>
                </div>

                <div class="input-group">
                    <label for="precio_venta">Precio de venta</label>
                    <input type="number" step="0.01" min="0" id="precio_venta" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta) }}" required>
                </div>

                <div class="input-group">
                    <label for="existencia">Existencia</label>
                    <input type="number" min="0" id="existencia" name="existencia" value="{{ old('existencia', $producto->existencia) }}" required>
                </div>

                <div class="input-group">
                    <label for="stock_minimo">Stock mínimo</label>
                    <input type="number" min="0" id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}" required>
                </div>

                <button type="submit" class="btn-process">
                    <span class="btn-label">Guardar cambios</span>
                </button>

            </form>

        </main>

    </div>

</body>

</html>