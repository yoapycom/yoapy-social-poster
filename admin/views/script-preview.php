<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos do DOM
        const device = document.getElementById('yoapsopo_device');
        const frame = document.getElementById('yoapsopo_prev_frame');
        const textInput = document.getElementById('yoapsopo_text');
        const imageInput = document.getElementById('yoapsopo_image_url');
        const videoInput = document.getElementById('yoapsopo_video_url');
        const typeSelect = document.querySelector('select[name="type"]');
        const modePills = document.querySelectorAll('#yoapsopo_prev_modes .yoapsopo-pill');
        const meta = document.getElementById('yoapsopo_prev_meta');

        const modes = {
            feed:    { aspect: '125%',      template: 'instagram', orientation: 'vertical' },
            story:   { aspect: '177.77%',   template: 'stories',   orientation: 'vertical' },
            reels:   { aspect: '177.77%',   template: 'reels',     orientation: 'vertical' },
            tiktok:  { aspect: '177.77%',   template: 'tiktok',    orientation: 'vertical' },
            youtube: { aspect: '56.25%',    template: 'youtube',   orientation: 'horizontal' },
            shorts:  { aspect: '177.77%',   template: 'shorts',    orientation: 'vertical' }
        };

        let currentMode = 'feed';

        function updatePreview() {
            updateText();
            updateHandles();
            updateMedia();
        }

        function updateText() {
            const text = textInput ? textInput.value : '';
            document.querySelectorAll('[id$="_text_content"]').forEach(el => {
                el.textContent = text;
            });
            const charCounter = document.getElementById('yoapsopo_char');
            if (charCounter) charCounter.textContent = text.length;
        }

        function updateHandles() {
            if (!meta) return;
            const handles = meta.dataset;
            const defaultHandle = handles.default || 'yourusername';
            const safeUpdate = (id, text) => {
                const el = document.getElementById(id);
                if (el) el.textContent = text;
            };
            safeUpdate('fb_display_name', handles.facebook || defaultHandle);
            safeUpdate('ig_username', handles.instagram || defaultHandle);
            safeUpdate('ig_caption_username', handles.instagram || defaultHandle);
            safeUpdate('story_username', handles.instagram || defaultHandle);
            safeUpdate('reels_username', handles.instagram || defaultHandle);
            safeUpdate('tiktok_username', '@' + (handles.tiktok || defaultHandle));
            safeUpdate('youtube_username', handles.youtube || defaultHandle);
            safeUpdate('shorts_username', handles.youtube || defaultHandle);
        }

        // ===== FUNÇÃO DE MÍDIA FINAL E À PROVA DE FALHAS =====
        function updateMedia() {
            const imageUrl = imageInput ? imageInput.value.trim() : '';
            const videoUrl = videoInput ? videoInput.value.trim() : '';

            // Seleciona todas as mídias de uma vez para eficiência
            const allImages = document.querySelectorAll('img[id$="_prev_img"]');
            const allVideos = document.querySelectorAll('video[id$="_prev_vid"]');

            if (videoUrl) {
                // MODO VÍDEO: Prioridade máxima.
                // 1. Garante que todas as imagens estejam escondidas e limpas.
                allImages.forEach(img => {
                    img.style.display = 'none';
                    img.src = '';
                });
                // 2. Garante que todos os vídeos estejam visíveis e com a URL correta.
                allVideos.forEach(vid => {
                    vid.style.display = 'block';
                    if (vid.src !== videoUrl) {
                        vid.src = videoUrl;
                    }
                    vid.play().catch(() => {});
                });
            } else if (imageUrl) {
                // MODO IMAGEM: Executado apenas se não houver vídeo.
                // 1. Garante que todos os vídeos estejam escondidos e limpos.
                allVideos.forEach(vid => {
                    vid.style.display = 'none';
                    vid.pause();
                    vid.src = '';
                });
                // 2. Garante que todas as imagens estejam visíveis e com a URL correta.
                allImages.forEach(img => {
                    img.style.display = 'block';
                    if (img.src !== imageUrl) {
                        img.src = imageUrl;
                    }
                });
            } else {
                // MODO SEM MÍDIA: Esconde e limpa tudo.
                allImages.forEach(img => {
                    img.style.display = 'none';
                    img.src = '';
                });
                allVideos.forEach(vid => {
                    vid.style.display = 'none';
                    vid.pause();
                    vid.src = '';
                });
            }
        }

        function setMode(mode) {
            if (!modes[mode]) return;
            currentMode = mode;

            device.setAttribute('data-mode', modes[mode].orientation);
            frame.style.setProperty('--yoapsopo-ar', modes[mode].aspect);

            document.querySelectorAll('.preview-container').forEach(c => c.classList.add('hidden'));
            const activeTemplate = document.getElementById(modes[mode].template + '_preview');
            if (activeTemplate) activeTemplate.classList.remove('hidden');

            modePills.forEach(p => p.classList.toggle('yoapsopo-pill--active', p.dataset.mode === mode));

            updatePreview();
        }

        function autoSwitchMode() {
            const hasVideo = videoInput && videoInput.value.trim() !== '';
            const type = typeSelect ? typeSelect.value : 'image';

            if (type === 'story') setMode('story');
            else if (type === 'live_schedule') setMode('youtube');
            else if (hasVideo) setMode('reels');
            else setMode('feed');
        }

        // Adiciona os Event Listeners
        textInput?.addEventListener('input', updateText);
        imageInput?.addEventListener('input', () => { updateMedia(); autoSwitchMode(); });
        videoInput?.addEventListener('input', () => { updateMedia(); autoSwitchMode(); });
        typeSelect?.addEventListener('change', autoSwitchMode);

        modePills.forEach(pill => {
            pill.addEventListener('click', () => setMode(pill.dataset.mode));
        });

        // Inicialização
        setMode('feed');
    });
</script>
