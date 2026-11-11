<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAJAJ | Catálogo de Motocicletas</title>
    @vite(['resources/css/tienda.css', 'resources/js/app.js'])

    <!-- Scroll suave para las secciones -->
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>

    <script>
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
        document.addEventListener("DOMContentLoaded", function() {
            const scrollPos = sessionStorage.getItem('scrollPosCatalogo');
            if (scrollPos) {
                window.scrollTo(0, parseInt(scrollPos));
                sessionStorage.removeItem('scrollPosCatalogo');
            }
        });
    </script>
</head>

<body class="tienda-body">

    <!-- MENÚ DE NAVEGACIÓN -->
    <nav class="tienda-navbar">
        <div class="nav-container">
            <a href="/" class="nav-logo">
                <img src="{{ asset('images/logo.png') }}" alt="BAJAJ" style="max-height: 45px; width: auto; object-fit: contain;">
            </a>
            
            <span id="nav-title" class="nav-title">EXPLORA EL CATÁLOGO</span>

            <div class="nav-links">
                <div class="nav-dropdown">
                    <!-- Se usa onclick directo para garantizar que abra sin fallos de JS -->
                    <button class="nav-dropdown-btn" type="button">MOTOS ▾</button>
                    <div class="nav-dropdown-content">
                        <!-- Las categorías mueven el carrusel sin recargar -->
                        <a href="#vista-pasarela" class="filtro-carrusel" data-target="0">Boxer</a>
                        <a href="#vista-pasarela" class="filtro-carrusel" data-target="1">Dominar</a>
                        <a href="#vista-pasarela" class="filtro-carrusel" data-target="2">Platina</a>
                        <a href="#vista-pasarela" class="filtro-carrusel" data-target="3">Pulsar</a>
                        <a href="#vista-pasarela" class="filtro-carrusel" data-target="4">Avenger</a>
                    </div>
                </div>
                <a href="#promos">PROMOS</a>
                <a href="#financiamiento">FINANCIAMIENTO</a>
                <a href="#servicio">SERVICIO</a>
            </div>
            
            <div class="nav-actions">
                <!-- El 🛒 abre la vista real del carrito -->
                <a href="/tienda/carrito" class="nav-cart" title="Ver mi carrito">🛒</a>
                
                <form method="POST" action="{{ route('logout') }}" style="display: inline-block; margin: 0;">
                    @csrf
                    <button type="submit" class="nav-logout" title="Cerrar Sesión">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- SECCIÓN 1: HERO (PORTADA) -->
    <section class="tienda-hero" id="inicio">
        <div class="tienda-hero-overlay"></div>
        <div class="tienda-hero-content">
            <span class="hero-badge">CATÁLOGO 2026</span>
            <h1 class="hero-title">
                NUESTRAS<br>
                <span class="text-highlight">MOTOCICLETAS</span>
            </h1>
            <p class="hero-subtitle">Diseñadas para combinar rendimiento, estilo y confiabilidad. Encuentra el poder que se adapta a cada camino.</p>
        </div>
        <div class="scroll-indicator">
            <span>Desliza para explorar</span>
            <div class="arrow-down"></div>
        </div>
    </section>

    <!-- SECCIÓN 2: CATÁLOGO DE MOTOS -->
    <main class="tienda-main" id="motos">
        <div class="tienda-main-inner">
            
            <div class="tienda-header-catalogo reveal" id="explora-catalogo">
                <h2 class="seccion-titulo">
                    @if(request('categoria'))
                        {{ strtoupper(request('categoria')) }}
                    @else
                        EXPLORA EL CATÁLOGO
                    @endif
                </h2>
                @if(request('categoria'))
                    <a href="/tienda" class="btn-volver-categorias filtro-trigger">← Volver a categorías</a>
                @endif
            </div>

            <!-- CARRUSEL DE CATEGORÍAS -->
            @if(!request('categoria') && !request('q'))
                @php
                    $categorias = [
                        ['nombre' => 'Boxer', 'slug' => 'boxer', 'img' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?q=80&w=600&auto=format&fit=crop'],
                        ['nombre' => 'Dominar', 'slug' => 'dominar', 'img' => 'https://www.motoo.com.br/fotos/2024/9/960_720/bajaj_dominar-250_2025_1_06092024_60675_960_720.jpg'],
                        ['nombre' => 'Platina', 'slug' => 'platina', 'img' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?q=80&w=600&auto=format&fit=crop'],
                        ['nombre' => 'Pulsar', 'slug' => 'pulsar', 'img' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?q=80&w=600&auto=format&fit=crop'],
                        ['nombre' => 'Avenger', 'slug' => 'avenger', 'img' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?q=80&w=600&auto=format&fit=crop'],
                    ];
                @endphp

                <div id="vista-pasarela" class="reveal">
                    <div class="carousel-container mode-5" data-active-index="0">
                        <div class="carousel-track" id="carousel-track">
                            @foreach ($categorias as $index => $cat)
                                <div class="carousel-item" data-index="{{ $index }}">
                                    <!-- Las tarjetas mueven el carrusel -->
                                    <a href="#vista-pasarela"
                                       class="carousel-link filtro-carrusel"
                                       data-target="{{ $index }}">
                                        <div class="producto-card-pasarela categoria-pasarela">
                                            <div class="producto-card-imagen">
                                                <img src="{{ $cat['img'] }}" alt="{{ $cat['nombre'] }}">
                                            </div>
                                            <div class="producto-card-body">
                                                <h3 class="producto-nombre">{{ $cat['nombre'] }}</h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="carousel-controls">
                            <button id="btn-prev" class="carousel-btn" type="button">&#10094;</button>
                            <div class="carousel-dots" id="carousel-dots">
                                @foreach ($categorias as $index => $cat)
                                    <span class="dot" data-index="{{ $index }}"></span>
                                @endforeach
                            </div>
                            <button id="btn-next" class="carousel-btn" type="button">&#10095;</button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- GRID DE PRODUCTOS (Se muestra al filtrar o buscar) -->
            @if(request('categoria') || request('q'))
                <div class="tienda-grid reveal mt-5" id="vista-cuadricula">
                    @forelse ($productos as $producto)
                        <div class="producto-card">
                            <div class="producto-card-imagen">
                                <img src="{{ $producto->imagen_url ?: 'https://picsum.photos/seed/' . $producto->id . '/800/600' }}" alt="{{ $producto->nombre }}">
                            </div>
                            <div class="producto-card-body">
                                <h3 class="producto-nombre">{{ $producto->nombre }}</h3>
                                <p class="producto-desc">{{ Str::limit($producto->descripcion, 80) }}</p>
                                <div class="producto-card-footer">
                                    <strong class="producto-precio">${{ number_format($producto->precio_venta, 2) }}</strong>
                                    <form method="POST" action="/tienda/carrito/agregar">
                                        @csrf
                                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                        <button type="submit" class="btn-agregar">Agregar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="tienda-vacia"><p>No encontramos motocicletas.</p></div>
                    @endforelse
                </div>
            @endif
        </div>
    </main>

    <!-- SECCIÓN 3: PROMOS -->
    <section id="promos" class="tienda-main" style="background: #0f172a; padding-top: 80px;">
        <div class="tienda-main-inner" style="text-align: center;">
            <h2 class="seccion-titulo">PROMOCIONES</h2>
            <div class="form-card" style="max-width: 800px; margin: 30px auto;">
                <h3 style="color: #60a5fa; font-size: 26px;">¡Mes sin intereses!</h3>
                <p style="color: #94a3b8; margin-top: 10px;">Aprovecha nuestras ofertas en toda la línea Bajaj.</p>
            </div>
        </div>
    </section>

    <!-- SECCIÓN 4: FINANCIAMIENTO -->
    <section id="financiamiento" class="tienda-main" style="padding-top: 80px;">
        <div class="tienda-main-inner" style="text-align: center;">
            <h2 class="seccion-titulo">FINANCIAMIENTO</h2>
            <div class="form-card" style="max-width: 800px; margin: 30px auto;">
                <h3 style="color: #60a5fa; font-size: 24px;">Cotiza tu crédito</h3>
                <p style="color: #94a3b8; margin-top: 10px;">Déjanos tus datos y un asesor se contactará contigo.</p>
            </div>
        </div>
    </section>

    <!-- SECCIÓN 5: SERVICIO -->
    <section id="servicio" class="tienda-main" style="background: #0f172a; padding-top: 80px; padding-bottom: 100px;">
        <div class="tienda-main-inner" style="text-align: center;">
            <h2 class="seccion-titulo">SERVICIO TÉCNICO</h2>
            <div class="form-card" style="max-width: 800px; margin: 30px auto;">
                <h3 style="color: #60a5fa; font-size: 24px;">Agenda tu mantenimiento</h3>
                <p style="color: #94a3b8; margin-top: 10px;">Contáctanos para programar la revisión de tu moto.</p>
            </div>
        </div>
    </section>

    <!-- SCRIPTS DE LA TIENDA -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {

        /* 1. Cerrar el dropdown si se hace clic afuera */
        document.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.nav-dropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });

        /* 2. Animaciones de scroll (reveal) */
        const reveals = document.querySelectorAll(".reveal");
        const revealOnScroll = () => {
            const windowHeight = window.innerHeight;
            reveals.forEach((reveal) => {
                if (reveal.getBoundingClientRect().top < windowHeight - 100) {
                    reveal.classList.add("active");
                }
            });
        };
        window.addEventListener("scroll", revealOnScroll);
        revealOnScroll();

        /* 3. Guardar scroll en el catálogo */
        const elementosFiltro = document.querySelectorAll('.filtro-trigger, .carousel-link');
        elementosFiltro.forEach(elemento => {
            elemento.addEventListener('click', function() {
                sessionStorage.setItem('scrollPosCatalogo', window.scrollY);
            });
        });

        /* 4. NAVBAR INTELIGENTE (ScrollSpy por posiciones exactas) */
        const navbar = document.querySelector('.tienda-navbar');
        const heroSection = document.querySelector('.tienda-hero');
        const promosSection = document.getElementById('promos');

        const controlarNavbar = () => {
            if (!navbar || !heroSection || !promosSection) return;

            const scrollActual = window.scrollY;
            const finHero = heroSection.offsetTop + heroSection.offsetHeight - 100; 
            const inicioPromos = promosSection.offsetTop - 100; 

            // Si estamos entre el final del hero y el inicio de promos -> Compacto
            if (scrollActual > finHero && scrollActual < inicioPromos) {
                navbar.classList.add('navbar-compact');
            } else {
                // En cualquier otro lugar (Hero o Promos en adelante) -> Menú completo
                navbar.classList.remove('navbar-compact');
            }
        };

        window.addEventListener('scroll', controlarNavbar);
        controlarNavbar(); 

        /* 5. Scroll suave para anclas del menú (PROMOS, FINANCIAMIENTO, SERVICIO) */
        document.querySelectorAll(
            '.nav-links > a[href^="#"], .nav-dropdown-content a[href^="#"]'
        ).forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (this.classList.contains('filtro-carrusel')) return;

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    const dropdown = document.querySelector('.nav-dropdown');
                    if (dropdown) dropdown.classList.remove('open'); // Cierra menú
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        /* 6. Carrusel 3D */
        const track = document.getElementById('carousel-track');
        if (track) {
            const container = track.closest('.carousel-container');
            const items = document.querySelectorAll('.carousel-item');
            const dots = document.querySelectorAll('.carousel-dots .dot');
            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');
            const total = items.length;

            if (total > 0) {
                let currentIndex = parseInt(container.getAttribute('data-active-index')) || 0;
                const useFar = total >= 5;

                function updateCarousel() {
                    items.forEach((item, index) => {
                        item.className = 'carousel-item';
                        let offset = index - currentIndex;
                        const half = useFar ? 2 : 1;

                        if (offset < -half) offset += total;
                        if (offset > half) offset -= total;

                        if (offset === 0) item.classList.add('active');
                        else if (offset === -1) item.classList.add('prev');
                        else if (offset === 1) item.classList.add('next');
                        else if (useFar && offset === -2) item.classList.add('far-prev');
                        else if (useFar && offset === 2) item.classList.add('far-next');
                        else item.classList.add('hidden');
                    });

                    dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
                }

                function nextSlide() {
                    currentIndex = (currentIndex + 1) % total;
                    updateCarousel();
                }

                function prevSlide() {
                    currentIndex = (currentIndex - 1 + total) % total;
                    updateCarousel();
                }

                if (btnNext && btnPrev) {
                    btnNext.addEventListener('click', nextSlide);
                    btnPrev.addEventListener('click', prevSlide);
                }

                dots.forEach((dot, i) => {
                    dot.addEventListener('click', () => {
                        currentIndex = i;
                        updateCarousel();
                    });
                });

                updateCarousel();

                /* 7. Filtros del menú "Motos" + tarjetas (Redirección al carrusel) */
                const linksCarrusel = document.querySelectorAll('.filtro-carrusel');
                linksCarrusel.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();

                        const targetIndex = parseInt(this.getAttribute('data-target'));
                        if (!isNaN(targetIndex)) {
                            currentIndex = targetIndex;
                            updateCarousel();
                        }

                        // Cerramos el dropdown
                        const dropdown = document.querySelector('.nav-dropdown');
                        if (dropdown) dropdown.classList.remove('open');

                        // Hacemos scroll hasta el carrusel
                        const carruselSection = document.getElementById('vista-pasarela');
                        if (carruselSection) {
                            carruselSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    });
                });
            }
        }
    });
    </script>
</body>
</html>