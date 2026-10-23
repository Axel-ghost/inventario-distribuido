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

    @if ($errors->any())
        <div style="color: red; background: #fee; padding: 16px; margin-bottom: 16px; border-radius: 8px;">
            <strong>Errores:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div style="color: green; background: #efe; padding: 16px; margin-bottom: 16px; border-radius: 8px;">
            {{ session('status') }}
        </div>
    @endif

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

        // --- BUSCADOR ---
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
                <div class="venta-sugerencia" data-id="${p.id_producto}" data-nombre="${p.nombre}">
                    <div><strong>${p.nombre}</strong><span>${p.codigo}</span></div>
                </div>
            `).join('');

            sugerenciasBox.classList.add('open');

            sugerenciasBox.querySelectorAll('.venta-sugerencia').forEach((el) => {
                el.addEventListener('click', () => {
                    const id = parseInt(el.getAttribute('data-id'));
                    const nombre = el.getAttribute('data-nombre');
                    agregarAlCarrito(id, nombre);
                    sugerenciasBox.classList.remove('open');
                    inputBuscar.value = '';
                });
            });
        }

        // --- CARRITO ---
        function agregarAlCarrito(id, nombre) {
            const existente = carrito.find(item => item.id === id);
            if (existente) {
                existente.cantidad += 1; // Si ya existe, suma 1
            } else {
                carrito.push({ id, nombre, cantidad: 1 }); // Si es nuevo, entra con 1
            }
            renderCarrito();
        }

        function quitarDelCarrito(index) {
            carrito.splice(index, 1); 
            renderCarrito();          
        }

        // Se ejecuta cada vez que el usuario cambia el número en el input
        function actualizarCantidad(index, valor) {
            let cantidad = parseInt(valor);
            if (isNaN(cantidad) || cantidad < 1) {
                cantidad = 1;
            }
            carrito[index].cantidad = cantidad;
            // NOTA: No llamamos a renderCarrito() aquí para que el input no pierda el foco
        }

        function renderCarrito() {
            if (carrito.length === 0) {
                carritoBody.innerHTML = `<tr id="carritoVacio"><td colspan="3" style="text-align: center; color: var(--muted);">Aún no has agregado productos</td></tr>`;
                totalItemsEl.textContent = '0';
                return;
            }

            // Usamos el 'index' (posición) para todo
            carritoBody.innerHTML = carrito.map((item, index) => `
                <tr>
                    <td>${item.nombre}</td>
                    <td>
                        <input type="number" min="1" 
                               value="${item.cantidad}" 
                               class="venta-cantidad-input" 
                               onchange="actualizarCantidad(${index}, this.value)">
                    </td>
                    <td>
                        <button type="button" class="venta-quitar" onclick="quitarDelCarrito(${index})">✕</button>
                    </td>
                </tr>
            `).join('');

            totalItemsEl.textContent = carrito.length;
        }

        // --- OTROS EVENTOS ---
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
            agregarAlCarrito(sugeridoInicial.id, sugeridoInicial.nombre);
            // Si el producto sugerido trae una cantidad específica, la forzamos:
            carrito[0].cantidad = sugeridoInicial.cantidad;
            renderCarrito();
        }
    </script>

</body>

</html>