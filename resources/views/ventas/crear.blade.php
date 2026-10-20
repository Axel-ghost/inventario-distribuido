<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Nueva requisición</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="pedidos" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Nueva requisición</h2>
                    <p>Solicita reabasto de productos al administrador</p>
                </div>

                <x-profile-card />
            </header>

            <div class="venta-layout">

                <div class="venta-buscador">
                    <input type="text" id="buscarProducto" placeholder="Busca por nombre o código..." autocomplete="off">
                    <div id="sugerencias" class="venta-sugerencias"></div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad solicitada</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="carritoBody">
                        <tr id="carritoVacio">
                            <td colspan="3" style="text-align: center; color: var(--muted);">Aún no has agregado productos</td>
                        </tr>
                    </tbody>
                </table>

                <form id="formPedido" method="POST" action="/pedidos">
                    @csrf

                    <div class="venta-resumen">
                        <div class="venta-total">
                            <span>Total de productos</span>
                            <strong id="totalItems">0</strong>
                        </div>

                        <x-order-button id="btnEnviarPedido" label="Enviar requisición" />
                    </div>
                </form>

            </div>

        </main>

    </div>

    <script>
        const carrito = [];
        const sugeridoInicial = @json($sugerido);

        const inputBuscar = document.getElementById('buscarProducto');
        const sugerenciasBox = document.getElementById('sugerencias');
        const carritoBody = document.getElementById('carritoBody');
        const totalItemsEl = document.getElementById('totalItems');

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
                    <div><strong>${p.nombre}</strong><span>${p.codigo}</span></div>
                </div>
            `).join('');

            sugerenciasBox.classList.add('open');

            sugerenciasBox.querySelectorAll('.venta-sugerencia').forEach((el, index) => {
                el.addEventListener('click', () => {
                    agregarAlCarrito(productos[index].id, productos[index].nombre, 1);
                    sugerenciasBox.classList.remove('open');
                    inputBuscar.value = '';
                });
            });
        }

        function agregarAlCarrito(id, nombre, cantidad) {
            const existente = carrito.find(item => item.id === id);
            if (existente) {
                existente.cantidad += cantidad;
            } else {
                carrito.push({ id, nombre, cantidad });
            }
            renderCarrito();
        }

        function quitarDelCarrito(id) {
            const index = carrito.findIndex(item => item.id === id);
            if (index !== -1) carrito.splice(index, 1);
            renderCarrito();
        }

        function cambiarCantidad(id, nuevaCantidad) {
            const item = carrito.find(item => item.id === id);
            if (item) item.cantidad = Math.max(1, parseInt(nuevaCantidad) || 1);
            renderCarrito();
        }

        function renderCarrito() {
            if (carrito.length === 0) {
                carritoBody.innerHTML = `<tr id="carritoVacio"><td colspan="3" style="text-align: center; color: var(--muted);">Aún no has agregado productos</td></tr>`;
                totalItemsEl.textContent = '0';
                return;
            }

            carritoBody.innerHTML = carrito.map(item => `
                <tr>
                    <td>${item.nombre}</td>
                    <td>
                        <input type="number" min="1" value="${item.cantidad}" class="venta-cantidad-input"
                               onchange="cambiarCantidad(${item.id}, this.value)">
                    </td>
                    <td><button type="button" class="venta-quitar" onclick="quitarDelCarrito(${item.id})">✕</button></td>
                </tr>
            `).join('');

            totalItemsEl.textContent = carrito.length;
        }

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.venta-buscador')) {
                sugerenciasBox.classList.remove('open');
            }
        });

        document.getElementById('btnEnviarPedido').addEventListener('click', function () {
            if (carrito.length === 0) {
                alert('Agrega al menos un producto.');
                return;
            }

            const boton = this;
            playOrderAnimation(boton, {
                duration: 1800,
                onComplete: function () {
                    const form = document.getElementById('formPedido');

                    carrito.forEach((item, index) => {
                        const inputId = document.createElement('input');
                        inputId.type = 'hidden';
                        inputId.name = `items[${index}][producto_id]`;
                        inputId.value = item.id;
                        form.appendChild(inputId);

                        const inputCant = document.createElement('input');
                        inputCant.type = 'hidden';
                        inputCant.name = `items[${index}][cantidad]`;
                        inputCant.value = item.cantidad;
                        form.appendChild(inputCant);
                    });

                    form.submit();
                }
            });
        });

        if (sugeridoInicial) {
            agregarAlCarrito(sugeridoInicial.id, sugeridoInicial.nombre, sugeridoInicial.cantidad);
        }
    </script>

</body>

</html>