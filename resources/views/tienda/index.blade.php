<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Catálogo de Motocicletas</title>
    @vite(['resources/css/tienda.css', 'resources/js/app.js'])

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

    <nav class="tienda-navbar">
        <div class="nav-container">
            <a href="/" class="nav-logo">CoreBuild</a>
            
            <span id="nav-title" class="nav-title">EXPLORA EL CATÁLOGO</span>

            <div class="nav-links">
                <div class="nav-dropdown">
                    <button class="nav-dropdown-btn">MOTOS ▾</button>
                    <div class="nav-dropdown-content">
                        <a href="/tienda?categoria=boxer" class="filtro-trigger">Boxer</a>
                        <a href="/tienda?categoria=dominar" class="filtro-trigger">Dominar</a>
                        <a href="/tienda?categoria=platina" class="filtro-trigger">Platina</a>
                        <a href="/tienda?categoria=pulsar" class="filtro-trigger">Pulsar</a>
                        <a href="/tienda?categoria=avenger" class="filtro-trigger">Avenger</a>
                    </div>
                </div>
                <a href="/promos">PROMOS</a>
                <a href="/financiamiento">FINANCIAMIENTO</a>
                <a href="/servicio">SERVICIO</a>
            </div>
            
            <div class="nav-actions">
                <a href="/carrito" class="nav-cart">🛒</a>
                
                <!-- Botón de Cerrar Sesión -->
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

    <section class="tienda-hero">
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

    <main class="tienda-main">
        <div class="tienda-main-inner">

            @if (session('status'))
                <p class="tienda-status">{{ session('status') }}</p>
            @endif

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

            <div class="tienda-buscador-wrapper reveal">
                <form method="GET" action="/tienda" class="tienda-buscador-input">
                    @if(request('categoria'))
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                    @endif
                    <input type="text" name="q" placeholder="Busca tu modelo ideal..." value="{{ request('q') }}">
                    <button type="submit" class="btn-buscar">Buscar</button>
                </form>
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
                                    <a href="/tienda?categoria={{ $cat['slug'] }}" class="carousel-link">
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
                            <button id="btn-prev" class="carousel-btn">&#10094;</button>
                            <div class="carousel-dots" id="carousel-dots">
                                @foreach ($categorias as $index => $cat)
                                    <span class="dot" data-index="{{ $index }}"></span>
                                @endforeach
                            </div>
                            <button id="btn-next" class="carousel-btn">&#10095;</button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- GRID DE PRODUCTOS -->
            @if(request('categoria') || request('q'))
                <div class="tienda-grid reveal mt-5" id="vista-cuadricula">
                    @forelse ($productos as $producto)
                        <div class="producto-card">
                            <div class="producto-card-imagen">
                                <img src="{{ $producto->imagen_url ?: 'https://picsum.photos/seed/' . $producto->id . '/800/600' }}" alt="{{ $producto->nombre }}">
                            </div>
                            <div class="producto-card-body">
                                @if ($producto->categoria)
                                    <span class="producto-categoria">{{ $producto->categoria }}</span>
                                @endif
                                <h3 class="producto-nombre">{{ $producto->nombre }}</h3>
                                <p class="producto-desc">{{ Str::limit($producto->descripcion, 80) }}</p>
                                
                                <div class="producto-card-footer">
                                    <strong class="producto-precio">${{ number_format($producto->precio_venta, 2) }}</strong>
                                    <form method="POST" action="/tienda/carrito/agregar">
                                        @csrf
                                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                        <button type="submit" class="btn-agregar">Cotizar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="tienda-vacia">
                            <p>No encontramos motocicletas con esa búsqueda.</p>
                        </div>
                    @endforelse
                </div>
            @endif

        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Animaciones de scroll
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

            // Guardar scroll (menú y categorías)
            const elementosFiltro = document.querySelectorAll('.filtro-trigger, .carousel-link');
            elementosFiltro.forEach(elemento => {
                elemento.addEventListener('click', function() {
                    sessionStorage.setItem('scrollPosCatalogo', window.scrollY);
                });
            });

            // Navbar compacto
            const navbar = document.querySelector('.tienda-navbar');
            const heroSection = document.querySelector('.tienda-hero');
            if (navbar && heroSection) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (!entry.isIntersecting) {
                            navbar.classList.add('navbar-compact');
                        } else {
                            navbar.classList.remove('navbar-compact');
                        }
                    });
                }, { rootMargin: "0px", threshold: 0.1 });
                observer.observe(heroSection);
            }

            // Carrusel 3D
            const track = document.getElementById('carousel-track');
            if (!track) return;

            const container = track.closest('.carousel-container');
            const items = document.querySelectorAll('.carousel-item');
            const dots = document.querySelectorAll('.carousel-dots .dot');
            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');
            const total = items.length;

            if (total === 0) return;

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

            if(btnNext && btnPrev) {
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
        });
    </script>
</body>
</html>