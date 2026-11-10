@extends('layouts.app')

@section('title', 'Nueva venta')

@section('header_title', 'Nueva venta')
@section('header_subtitle', 'Busca productos y arma la venta')

@section('content')

    @if ($errors->any())
        <p style="color: #ef4444; margin-bottom: 16px;">{{ $errors->first() }}</p>
    @endif

    <div class="venta-layout">

        <div class="venta-buscador">
            <input type="text" id="buscarProducto" placeholder="Busca por nombre o código..." autocomplete="off">
            <div id="sugerencias" class="venta-sugerencias"></div>
        </div>

        <div class="table-container">
            <table class="data-table-clean">
                <thead>
                    <tr>
                        <th>PRODUCTO</th>
                        <th>PRECIO</th>
                        <th>CANTIDAD</th>
                        <th>SUBTOTAL</th>
                        <th style="width: 100px;"></th>
                    </tr>
                </thead>
                <tbody id="carritoBody">
                    <tr id="carritoVacio">
                        <td colspan="5" style="text-align: center; padding: 30px; color: var(--muted);">Aún no has agregado productos</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form id="formVenta" method="POST" action="/ventas" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf

            <div class="venta-resumen">
                <div class="venta-total">
                    <span>Total</span>
                    <strong id="ventaTotal">$0.00</strong>
                </div>

                <x-order-button id="btnConfirmarVenta" label="Confirmar venta" />
            </div>

            <!-- Botón de Cancelar debajo del total -->
            <div style="display: flex;">
                <a href="/ventas" class="btn-cancel" style="width: auto; min-width: 200px; height: 54px;">
                    Cancelar y volver
                </a>
            </div>
        </form>

    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'buscarProducto') {
            const inputBuscar = e.target;
            const termino = inputBuscar.value.trim();
            const sugerenciasBox = document.getElementById('sugerencias');

            if (!sugerenciasBox) return;

            if (termino.length < 2) {
                sugerenciasBox.classList.remove('open');
                sugerenciasBox.innerHTML = '';
                return;
            }

            clearTimeout(window.timeoutBusqueda);
            window.timeoutBusqueda = setTimeout(() => {
                fetch(`/api/productos/buscar?q=${encodeURIComponent(termino)}`)
                    .then(res => res.json())
                    .then(productos => {
                        sugerenciasBox.classList.add('open'); // <-- AGREGA LA CLASE .open AQUÍ

                        if (productos.length === 0) {
                            sugerenciasBox.innerHTML = '<div class="venta-sugerencia-vacia">Sin resultados</div>';
                            return;
                        }

                        sugerenciasBox.innerHTML = productos.map(p => `
                            <div class="venta-sugerencia" data-id="${p.id}" data-nombre="${p.nombre}" data-codigo="${p.codigo}" data-precio="${p.precio_venta}">
                                <div>
                                    <strong>${p.nombre}</strong>
                                    <span>${p.codigo} · Existencia: ${p.existencia}</span>
                                </div>
                                <span>$${Number(p.precio_venta).toFixed(2)}</span>
                            </div>
                        `).join('');
                    })
                    .catch(err => console.error('Error en fetch:', err));
            }, 250);
        }
    });

    // Ocultar al hacer clic fuera
    document.addEventListener('click', function (e) {
        const sugerenciasBox = document.getElementById('sugerencias');
        if (sugerenciasBox && !e.target.closest('.venta-buscador')) {
            sugerenciasBox.classList.remove('open');
        }

        // Clic en una sugerencia
        if (e.target.closest('.venta-sugerencia')) {
            const el = e.target.closest('.venta-sugerencia');
            window.agregarAlCarrito({
                id: el.dataset.id,
                nombre: el.dataset.nombre,
                codigo: el.dataset.codigo,
                precio_venta: el.dataset.precio
            });
            if (sugerenciasBox) sugerenciasBox.classList.remove('open');
            const inputBuscar = document.getElementById('buscarProducto');
            if (inputBuscar) inputBuscar.value = '';
        }

        // Clic en quitar producto
        if (e.target.closest('.venta-quitar')) {
            e.preventDefault();
            const btn = e.target.closest('.venta-quitar');
            const match = btn.getAttribute('onclick').match(/\d+/);
            if (match) window.quitarDelCarrito(match[0]);
        }

        // Clic en confirmar venta
        if (e.target.closest('#btnConfirmarVenta')) {
            e.preventDefault();
            if (!window.carritoVenta || window.carritoVenta.length === 0) {
                alert('Agrega al menos un producto antes de confirmar.');
                return;
            }
            const boton = e.target.closest('#btnConfirmarVenta');
            if (typeof playOrderAnimation === 'function') {
                playOrderAnimation(boton, {
                    duration: 2200,
                    onComplete: function () { window.enviarFormularioVenta(); }
                });
            } else {
                window.enviarFormularioVenta();
            }
        }
    });

    window.carritoVenta = window.carritoVenta || [];

    window.agregarAlCarrito = function(producto) {
        const existente = window.carritoVenta.find(item => item.id == producto.id);
        if (existente) {
            existente.cantidad += 1;
        } else {
            window.carritoVenta.push({
                id: producto.id,
                codigo: producto.codigo,
                nombre: producto.nombre,
                precio: Number(producto.precio_venta),
                cantidad: 1,
            });
        }
        window.renderCarritoVenta();
    };

    window.quitarDelCarrito = function(id) {
        const index = window.carritoVenta.findIndex(item => item.id == id);
        if (index !== -1) window.carritoVenta.splice(index, 1);
        window.renderCarritoVenta();
    };

    window.cambiarCantidad = function(id, nuevaCantidad) {
        const item = window.carritoVenta.find(item => item.id == id);
        if (item) item.cantidad = Math.max(1, parseInt(nuevaCantidad) || 1);
        window.renderCarritoVenta();
    };

    window.renderCarritoVenta = function() {
        const carritoBody = document.getElementById('carritoBody');
        const ventaTotalEl = document.getElementById('ventaTotal');
        if (!carritoBody || !ventaTotalEl) return;

        if (window.carritoVenta.length === 0) {
            carritoBody.innerHTML = `
                <tr id="carritoVacio">
                    <td colspan="5" style="text-align: center; color: var(--muted);">Aún no has agregado productos</td>
                </tr>
            `;
            ventaTotalEl.textContent = '$0.00';
            return;
        }

        let total = 0;
        carritoBody.innerHTML = window.carritoVenta.map(item => {
            const subtotal = item.precio * item.cantidad;
            total += subtotal;
            return `
                <tr>
                    <td>${item.nombre}</td>
                    <td>$${item.precio.toFixed(2)}</td>
                    <td>
                        <input type="number" min="1" value="${item.cantidad}" class="venta-cantidad-input"
                               onchange="cambiarCantidad(${item.id}, this.value)">
                    </td>
                    <td>$${subtotal.toFixed(2)}</td>
                    <td><button type="button" class="venta-quitar" onclick="quitarDelCarrito(${item.id})">✕</button></td>
                </tr>
            `;
        }).join('');
        ventaTotalEl.textContent = '$' + total.toFixed(2);
    };

    window.enviarFormularioVenta = function() {
        const form = document.getElementById('formVenta');
        window.carritoVenta.forEach((item, index) => {
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
    };
</script>
@endpush