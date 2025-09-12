<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Drag and Drop & Media Upload Functionality Script
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const metaEl = document.getElementById('yoapsopo_media_meta');
        if (!metaEl) return;

        const UPLOAD_URL = metaEl.dataset.uploadUrl;
        const MEDIA_NONCE = metaEl.dataset.mediaNonce;

        function toast(msg, isSuccess = true) {
            const t = document.createElement('div');
            t.className = 'fixed z-50 bottom-5 right-5 rounded-lg px-4 py-2 shadow-lg text-sm text-white transition-all';
            t.style.background = isSuccess ? '#22c55e' : '#ef4444'; // verde ou vermelho
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => {
                t.style.opacity = '0';
                t.style.transform = 'translateY(10px)';
            }, 1800);
            setTimeout(() => t.remove(), 2200);
        }

        async function uploadFile(file, dz) {
            const progWrap = dz.querySelector('.yoapsopo-dz-progress');
            const progBar = progWrap ? progWrap.querySelector('span') : null;
            const targetInput = document.querySelector(dz.dataset.target);

            if (!targetInput) return;

            const setProgress = (p) => {
                if (progWrap && progBar) {
                    progWrap.hidden = false;
                    progBar.style.width = `${p}%`;
                    if (p >= 100) setTimeout(() => { progWrap.hidden = true; progBar.style.width = '0%'; }, 800);
                }
            };

            try {
                dz.classList.add('dragover');
                setProgress(10);

                const fd = new FormData();
                fd.append('async-upload', file, file.name);
                fd.append('name', file.name);
                fd.append('action', 'upload-attachment');
                fd.append('_wpnonce', MEDIA_NONCE);

                const res = await fetch(UPLOAD_URL, { method: 'POST', body: fd });
                setProgress(80);

                if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);

                const json = await res.json();

                if (!json.success || !json.data || !json.data.url) {
                    throw new Error(json.data.message || 'Upload failed.');
                }

                targetInput.value = json.data.url;
                targetInput.dispatchEvent(new Event('input', { bubbles: true })); // ATIVA O PREVIEW
                setProgress(100);
                toast('<?php esc_html_e( "Media uploaded successfully!", 'yoapy-social-poster' ); ?>');

            } catch (error) {
                console.error('Upload Error:', error);
                toast(`<?php esc_html_e( "Error:", 'yoapy-social-poster' ); ?> ${error.message}`, false);
                setProgress(100); // Esconde a barra
            } finally {
                dz.classList.remove('dragover');
            }
        }

        document.querySelectorAll('.yoapsopo-dz').forEach(dz => {
            const fileInput = dz.querySelector('.yoapsopo-dz-file');
            const accept = dz.dataset.accept || '*';

            const handleFile = (file) => {
                if (file && file.type.match(accept.replace('*', '.*'))) {
                    uploadFile(file, dz);
                } else {
                    toast('<?php esc_html_e( "Invalid file type.", 'yoapy-social-poster' ); ?>', false);
                }
            };

            // Clique
            dz.addEventListener('click', (e) => {
                if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A') {
                    fileInput.click();
                }
            });
            dz.querySelector('.yoapsopo-dz-browse')?.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

            // Arrastar
            dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('dragover'); });
            dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
            dz.addEventListener('drop', (e) => {
                e.preventDefault();
                dz.classList.remove('dragover');
                handleFile(e.dataTransfer.files[0]);
            });

            // Colar (Paste)
            dz.addEventListener('paste', (e) => {
                const file = Array.from(e.clipboardData.items).find(item => item.kind === 'file')?.getAsFile();
                if (file) {
                    e.preventDefault();
                    handleFile(file);
                }
            });
        });
    });
</script>
