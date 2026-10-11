<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoreBuild | Nueva venta</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="ventas" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Nueva venta</h2>
                    <p>Busca productos y arma la venta</p>
                </div>

                <x-profile-card />
            </header>

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom:16px;">
                    <ul style="margin:0; padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/ventas" id="formVenta">
                @csrf

                <div class="venta-layout">

                    <!-- BUSCADOR -->
                    <div class="venta-buscador">
                        <input
                            type="text"
                            id="buscarProducto"
                            placeholder="Busca por nombre o código..."
                            autocomplete="off"
                        >
                        <div id="sugerencias" class="venta-sugerencias"></div>
                    </div>

                    <!-- MÉTODO DE PAGO -->
                    <div style="margin-bottom: 20px;">
                        <div class="input-group">
                            <label for="metodo_pago">Método de pago</label>
                            <select name="metodo_pago" id="metodo_pago" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                    </div>

                    <!-- CARRITO -->
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="carritoBody">
                            <tr id="carritoVacio">
                                <td colspan="5" style="text-align: center; color: var(--muted);">
                                    Aún no has agregado productos
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- MENSAJE GLOBAL DE STOCK INSUFICIENTE -->
                    <div id="alertaStock" style="display:none; margin-top:16px; padding:12px 16px; border-radius:8px; background:rgba(239,68,68,0.15); color:#ef4444; font-weight:600;">
                        ⚠ Stock insuficiente: uno o más productos superan la existencia disponible. Ajusta las cantidades para continuar.
                    </div>

                    <!-- TOTAL Y BOTÓN -->
                    <div class="venta-resumen">
                        <div class="venta-total">
                            <span>Total</span>
                            <strong id="ventaTotal">$0.00</strong>
                        </div>

                        <button type="submit" class="btn-process" id="btnConfirmarVenta">
                            <span class="btn-label">Confirmar venta</span>
                        </button>
                    </div>

                </div>

            </form>

        </main>

    </div>

    <script>
        const carrito = [];

        const inputBuscar = document.getElementById('buscarProducto');
        const sugerenciasBox = document.getElementById('sugerencias');
        const carritoBody = document.getElementById('carritoBody');
        const ventaTotalEl = document.getElementById('ventaTotal');
        const formVenta = document.getElementById('formVenta');
        const btnConfirmar = document.getElementById('btnConfirmarVenta');
        const alertaStock = document.getElementById('alertaStock');

        let timeoutBusqueda = null;
        let stockInvalido = false;

        inputBuscar.addEventListener('input', function () {
            const termino = this.value.trim();

            clearTimeout(timeoutBusqueda);

            if (termino.length < 2) {
                sugerenciasBox.innerHTML = '';
                sugerenciasBox.classList.remove('open');
                return;
            }

            timeoutBusqueda = setTimeout(() => {
                fetch(`/api/productos/buscar?q=${encodeURIComponent(termino)}`)
                    .then(res => res.json())
                    .then(productos => mostrarSugerencias(productos));
            }, 250);
        });

        function mostrarSugerencias(productos) {
            if (productos.length === 0) {
                sugerenciasBox.innerHTML = '<div class="venta-sugerencia-vacia">Sin resultados</div>';
                sugerenciasBox.classList.add('open');
                return;
            }

            sugerenciasBox.innerHTML = productos.map(p => `
                <div class="venta-sugerencia" data-id="${p.id_producto}">
                    <div>
                        <strong>${p.nombre}</strong>
                        <span>${p.codigo} · Existencia: ${p.existencia}</span>
                    </div>
                    <span>$${Number(p.precio_venta).toFixed(2)}</span>
                </div>
            `).join('');

            sugerenciasBox.classList.add('open');

            sugerenciasBox.querySelectorAll('.venta-sugerencia').forEach((el, index) => {
                el.addEventListener('click', () => {
                    agregarAlCarrito(productos[index]);
                    sugerenciasBox.classList.remove('open');
                    inputBuscar.value = '';
                });
            });
        }

        function agregarAlCarrito(producto) {
            const existente = carrito.find(item => item.id_producto === producto.id_producto);

            if (existente) {
                existente.cantidad += 1;
            } else {
                carrito.push({
                    id_producto: producto.id_producto,
                    codigo: producto.codigo,
                    nombre: producto.nombre,
                    precio: Number(producto.precio_venta),
                    existencia: Number(producto.existencia),
                    cantidad: 1,
                });
            }

            renderCarrito();
        }

        function quitarDelCarrito(id) {
            const index = carrito.findIndex(item => item.id_producto === id);
            if (index !== -1) {
                carrito.splice(index, 1);
            }
            renderCarrito();
        }

        function cambiarCantidad(id, nuevaCantidad) {
            const item = carrito.find(item => item.id_producto === id);
            if (item) {
                item.cantidad = Math.max(1, parseInt(nuevaCantidad) || 1);
            }
            renderCarrito();
        }

        function renderCarrito() {
            if (carrito.length === 0) {
                carritoBody.innerHTML = `
                    <tr id="carritoVacio">
                        <td colspan="5" style="text-align: center; color: var(--muted);">
                            Aún no has agregado productos
                        </td>
                    </tr>
                `;
                ventaTotalEl.textContent = '$0.00';
                alertaStock.style.display = 'none';
                btnConfirmar.disabled = false;
                btnConfirmar.style.opacity = '1';
                btnConfirmar.style.cursor = 'pointer';
                stockInvalido = false;
                return;
            }

            let total = 0;
            stockInvalido = false;

            carritoBody.innerHTML = carrito.map((item, i) => {
                const subtotal = item.precio * item.cantidad;
                total += subtotal;

                const excede = item.cantidad > item.existencia;
                if (excede) stockInvalido = true;

                const stockTexto = excede
                    ? `<span style="color:#ef4444; font-weight:600;">⚠ Excede el stock · Disponible: ${item.existencia}</span>`
                    : `<span style="color:#22c55e; font-weight:600;">✓ Disponible: ${item.existencia}</span>`;

                return `
                    <tr>
                        <td>
                            ${item.nombre}
                            <br>
                            <small>${stockTexto}</small>
                            <input type="hidden" name="items[${i}][id_producto]" value="${item.id_producto}">
                        </td>
                        <td>$${item.precio.toFixed(2)}</td>
                        <td>
                            <input
                                type="number"
                                min="1"
                                value="${item.cantidad}"
                                class="venta-cantidad-input"
                                name="items[${i}][cantidad]"
                                onchange="cambiarCantidad(${item.id_producto}, this.value)"
                                style="${excede ? 'border:1px solid #ef4444;' : ''}"
                            >
                        </td>
                        <td>$${subtotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="venta-quitar" onclick="quitarDelCarrito(${item.id_producto})">✕</button>
                        </td>
                    </tr>
                `;
            }).join('');

            ventaTotalEl.textContent = '$' + total.toFixed(2);

            // Si hay exceso: mostrar aviso y DESHABILITAR el botón
            if (stockInvalido) {
                alertaStock.style.display = 'block';
                btnConfirmar.disabled = true;
                btnConfirmar.style.opacity = '0.5';
                btnConfirmar.style.cursor = 'not-allowed';
            } else {
                alertaStock.style.display = 'none';
                btnConfirmar.disabled = false;
                btnConfirmar.style.opacity = '1';
                btnConfirmar.style.cursor = 'pointer';
            }
        }

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.venta-buscador')) {
                sugerenciasBox.classList.remove('open');
            }
        });

        // Confirmación antes de enviar (solo cuando el botón está activo)
        formVenta.addEventListener('submit', function (e) {
            if (carrito.length === 0) {
                e.preventDefault();
                alert('Agrega al menos un producto antes de confirmar.');
                return;
            }

            // Si llegó aquí, es porque NO hay exceso (el botón estaba habilitado)
            const continuar = confirm('¿Deseas continuar con la compra?');

            if (!continuar) {
                e.preventDefault();
                return;
            }
        });
    </script>

</body>

</html>