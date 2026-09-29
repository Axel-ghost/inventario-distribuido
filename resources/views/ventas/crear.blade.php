<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

                <!-- TOTAL Y BOTÓN -->
                <div class="venta-resumen">
                    <div class="venta-total">
                        <span>Total</span>
                        <strong id="ventaTotal">$0.00</strong>
                    </div>

                    <x-order-button id="btnConfirmarVenta" label="Confirmar venta" />
                </div>

            </div>

        </main>

    </div>

    <script>
        const carrito = [];

        const inputBuscar = document.getElementById('buscarProducto');
        const sugerenciasBox = document.getElementById('sugerencias');
        const carritoBody = document.getElementById('carritoBody');
        const ventaTotalEl = document.getElementById('ventaTotal');

        let timeoutBusqueda = null;

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
                <div class="venta-sugerencia" data-id="${p.id}">
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
            const existente = carrito.find(item => item.id === producto.id);

            if (existente) {
                existente.cantidad += 1;
            } else {
                carrito.push({
                    id: producto.id,
                    codigo: producto.codigo,
                    nombre: producto.nombre,
                    precio: Number(producto.precio_venta),
                    cantidad: 1,
                });
            }

            renderCarrito();
        }

        function quitarDelCarrito(id) {
            const index = carrito.findIndex(item => item.id === id);
            if (index !== -1) {
                carrito.splice(index, 1);
            }
            renderCarrito();
        }

        function cambiarCantidad(id, nuevaCantidad) {
            const item = carrito.find(item => item.id === id);
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
                return;
            }

            let total = 0;

            carritoBody.innerHTML = carrito.map(item => {
                const subtotal = item.precio * item.cantidad;
                total += subtotal;

                return `
                    <tr>
                        <td>${item.nombre}</td>
                        <td>$${item.precio.toFixed(2)}</td>
                        <td>
                            <input
                                type="number"
                                min="1"
                                value="${item.cantidad}"
                                class="venta-cantidad-input"
                                onchange="cambiarCantidad(${item.id}, this.value)"
                            >
                        </td>
                        <td>$${subtotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="venta-quitar" onclick="quitarDelCarrito(${item.id})">✕</button>
                        </td>
                    </tr>
                `;
            }).join('');

            ventaTotalEl.textContent = '$' + total.toFixed(2);
        }

        // Cerrar sugerencias si haces clic fuera
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.venta-buscador')) {
                sugerenciasBox.classList.remove('open');
            }
        });

        // Botón de confirmar venta
        document.getElementById('btnConfirmarVenta').addEventListener('click', function () {
            if (carrito.length === 0) {
                alert('Agrega al menos un producto antes de confirmar.');
                return;
            }

            const boton = this;

            playOrderAnimation(boton, {
                duration: 2200,
                onComplete: function () {
                    fetch('/ventas', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ items: carrito }),
                    })
                    .then(res => res.text())
                    .then(html => {
                        document.open();
                        document.write(html);
                        document.close();
                    });
                }
            });
        });
    </script>

</body>

</html>