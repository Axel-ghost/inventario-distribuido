@props(['label' => 'Confirmar', 'id' => 'btn-' . uniqid()])

<button type="button" class="btn-process" id="{{ $id }}">
    <span class="btn-label">{{ $label }}</span>

    <div class="btn-process-track">
        <div class="btn-process-fill"></div>
        <span class="btn-process-truck">🚚</span>
    </div>

    <div class="btn-process-success">
        <span>✓</span>
        <span>¡Listo!</span>
    </div>
</button>

@once
    <script>
        function playOrderAnimation(button, options = {}) {
            const duration = options.duration || 2000;
            const onComplete = options.onComplete || function () {};

            if (button.classList.contains('is-processing') || button.classList.contains('is-success')) {
                return;
            }

            const fill = button.querySelector('.btn-process-fill');
            const truck = button.querySelector('.btn-process-truck');
            const trackWidth = button.offsetWidth;

            button.disabled = true;
            button.classList.add('is-processing');

            fill.style.transition = `width ${duration}ms linear`;
            truck.style.transition = `left ${duration}ms linear`;

            requestAnimationFrame(() => {
                fill.style.width = '100%';
                truck.style.left = (trackWidth - 34) + 'px';
            });

            setTimeout(() => {
                button.classList.remove('is-processing');
                button.classList.add('is-success');

                setTimeout(() => {
                    onComplete();
                }, 400);
            }, duration);
        }
    </script>
@endonce