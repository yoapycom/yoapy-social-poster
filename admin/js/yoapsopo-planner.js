/**
 * Script para a página do YoApy Planner com AJAX.
 *
 * @package YoApySocialPoster
 * @since 1.6.0
 */
document.addEventListener('DOMContentLoaded', function() {
    // Garante que a biblioteca de mídia do WordPress foi carregada
    if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
        console.error('YOAPSOPO: WordPress media library not loaded.');
        return;
    }

    // Usaremos uma única instância do seletor de mídia para melhor performance
    let mediaFrame;

    // Usa delegação de evento para ouvir cliques em qualquer botão .yoapsopo-pick
    document.body.addEventListener('click', function(e) {
        const button = e.target.closest('.yoapsopo-pick');
        if (!button) {
            return;
        }

        e.preventDefault();

        const targetSelector = button.dataset.target;
        const targetInput = document.querySelector(targetSelector);
        if (!targetInput) {
            console.error('YOAPSOPO Media Picker: Input de destino não encontrado para o seletor:', targetSelector);
            return;
        }

        // Cria o frame de mídia apenas na primeira vez que for necessário
        if (!mediaFrame) {
            mediaFrame = wp.media({
                title: 'Choose or Upload Media',
                button: {
                    text: 'Use this media'
                },
                multiple: false
            });
        }

        // *** A CORREÇÃO CRÍTICA ESTÁ AQUI ***
        // 1. Remove qualquer "ouvinte" do evento 'select' que possa existir de cliques anteriores.
        //    Isso impede que ele atualize o campo de input antigo.
        mediaFrame.off('select');

        // 2. Adiciona um novo "ouvinte" para o evento 'select'. Este novo ouvinte
        //    sabe exatamente qual é o 'targetInput' ATUAL, pois foi definido no clique do botão.
        mediaFrame.on('select', function() {
            const attachment = mediaFrame.state().get('selection').first().toJSON();

            // Atualiza o valor do campo de input correto
            targetInput.value = attachment.url;

            // Dispara o evento 'input' para que o script de preview seja ativado
            targetInput.dispatchEvent(new Event('input', { bubbles: true }));
        });

        // 3. Abre o seletor de mídia.
        mediaFrame.open();
    });
});
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
            toast('Media uploaded successfully!');

        } catch (error) {
            console.error('Upload Error:', error);
            toast(`Error: ${error.message}`, false);
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
                toast('Invalid file type.', false);
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

    // Garante que a biblioteca de mídia do WordPress foi carregada
    if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
        console.error('YOAPSOPO: WordPress media library not loaded.');
        return;
    }

});
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.yoapsopo-toggle'); // pega o botão mesmo se clicou no <span>
    if (!btn) return;

    e.preventDefault();

    // Se o .yoapsopo-details for o próximo irmão direto:
    const details = btn.nextElementSibling && btn.nextElementSibling.classList.contains('yoapsopo-details')
        ? btn.nextElementSibling
        : btn.parentElement.querySelector('.yoapsopo-details'); // fallback, caso mude o HTML

    if (!details) return;

    // Zera qualquer estilo inline remanescente de handlers antigos
    details.style.display = '';

    const isHidden = details.classList.contains('hidden');
    details.classList.toggle('hidden', !isHidden);

    btn.innerHTML = isHidden
        ? '<span class="dashicons dashicons-hidden"></span> hide details'
        : '<span class="dashicons dashicons-visibility"></span> view details';
});
jQuery(document).ready(function ($) {
    const ajax_url = yoapsopo_ajax_object.ajax_url;
    const nonce = yoapsopo_ajax_object.nonce;
    const i18n = yoapsopo_ajax_object.i18n;

    // --- Notificações (Toast) ---
    function toast(msg, isSuccess = true) {
        const toastId = 'yoapsopo-toast-' + Date.now();
        const toastHTML = `<div id="${toastId}" class="fixed z-[99999] top-5 right-5 rounded-xl px-4 py-3 shadow-lg text-sm text-white transition-all animate-slideIn" style="background: ${isSuccess ? 'linear-gradient(135deg, #22c55e, #16a34a)' : 'linear-gradient(135deg, #ef4444, #dc2626)'};"><span class="dashicons ${isSuccess ? 'dashicons-yes-alt' : 'dashicons-warning'}" style="vertical-align: middle; margin-right: 8px;"></span>${msg}</div>`;
        $('body').append(toastHTML);
        setTimeout(() => {
            const el = $('#' + toastId);
            el.css({ opacity: 0, transform: 'translateY(-20px)' });
            setTimeout(() => el.remove(), 500);
        }, 3000);
    }

    // --- Renderização Dinâmica da Lista de Tarefas (APÓS AÇÕES AJAX) ---
    function renderTaskList(tasks) {
        const tableBody = $('#yoapsopo_tasks_tbody');
        const cardsContainer = $('#yoapsopo_tasks_cards');
        const placeholder = $('#yoapsopo-no-tasks-placeholder');

        tableBody.empty();
        cardsContainer.empty();

        if (!tasks || tasks.length === 0) {
            placeholder.show();
            return;
        }
        placeholder.hide();

        tasks.forEach(task => {
            const networksHtml = (task.networks || []).map(n => {
                const networkName = n.charAt(0).toUpperCase() + n.slice(1);
                return `<span class="yoapsopo-net-chip yoapsopo-net--${n}">${networkName}</span>`;
            }).join('');

            const resultsHtml = task.results ? Object.entries(task.results).map(([net, res]) => {
                const networkName = net.charAt(0).toUpperCase() + net.slice(1);
                if (res && res.permalink) return `<a href="${res.permalink}" target="_blank" class="yoapsopo-result-link">${networkName}</a>`;
                if (res && res.success === false) return `<div class="yoapsopo-result-error" title="${res.message || ''}">${networkName}: Error</div>`;
                return '';
            }).join('') : '';

            // Format date/time for display
            let dateTimeInfo = '';
            if (task.when) {
                const date = new Date(task.when * 1000);
                // For completed tasks, show "Posted on" instead of scheduled time
                if (task.status === 'complete') {
                    dateTimeInfo = `
                        <div class="text-xs text-slate-500 mt-1">
                            ✅ Posted on ${date.toLocaleDateString()} ${date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                        </div>
                    `;
                } else if (task.status === 'scheduled') {
                    dateTimeInfo = `
                        <div class="text-xs text-slate-500 mt-1">
                            🕒 Scheduled for ${date.toLocaleDateString()} ${date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                        </div>
                    `;
                } else {
                    dateTimeInfo = `
                        <div class="text-xs text-slate-500 mt-1">
                            📅 Created on ${date.toLocaleDateString()} ${date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                        </div>
                    `;
                }
            } else if (task.status === 'complete') {
                // For immediate tasks that don't have a scheduled time
                const now = new Date();
                dateTimeInfo = `
                    <div class="text-xs text-slate-500 mt-1">
                        ✅ Posted on ${now.toLocaleDateString()} ${now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                    </div>
                `;
            }

            // Renderiza a linha da tabela (Desktop)
            const tableRow = `
                <tr data-id="${task.id}" class="hover:bg-slate-50/50 transition-colors duration-150">
                    <td class="px-4 py-3 align-top">
                        <div class="font-medium text-slate-800">${task.title || '—'}</div>
                        <div class="text-xs text-slate-500">${task.type}</div>
                        ${dateTimeInfo}
                    </td>
                    <td class="px-4 py-3 align-top"><div class="flex flex-wrap gap-1">${networksHtml}</div></td>
                    <td class="px-4 py-3 align-top"><span class="yoapsopo-status-badge yoapsopo-status--${task.status}">${task.status}</span><div class="mt-1.5 space-y-1 text-xs">${resultsHtml}</div></td>
                    <td class="px-4 py-3 align-top">
                        <div class="flex items-center gap-2">
                            <button class="button button-primary !px-4 !py-2 !h-auto !text-base yoapsopo-act" data-act="send"><span class="dashicons dashicons-migrate"></span> ${i18n.send || 'Post'}</button>
                            <button class="button button-danger !px-4 !py-2 !h-auto !text-base button-link-delete yoapsopo-act" data-act="delete"><span class="dashicons dashicons-trash"></span> ${i18n.delete || 'Delete'}</button>
                        </div>
                    </td>
                </tr>`;
            tableBody.append(tableRow);

            // Renderiza o card para mobile
            const cardHtml = `
                <div data-id="${task.id}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <div class="font-medium text-slate-800">${task.title || '—'}</div>
                            <div class="text-xs text-slate-500">${task.type}</div>
                            ${dateTimeInfo}
                        </div>
                        <span class="yoapsopo-status-badge yoapsopo-status--${task.status}">${task.status}</span>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-1">
                        ${networksHtml}
                    </div>
                    ${resultsHtml ? `<div class="mt-2 space-y-1 text-xs">${resultsHtml}</div>` : ''}
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button class="button button-primary !px-4 !py-2 !h-auto !text-base yoapsopo-act" data-act="send"><span class="dashicons dashicons-migrate"></span> ${i18n.send || 'Post'}</button>
                        <button class="button button-danger !px-4 !py-2 !h-auto !text-base button-link-delete yoapsopo-act" data-act="delete"><span class="dashicons dashicons-trash"></span> ${i18n.delete || 'Delete'}</button>
                    </div>
                </div>`;
            cardsContainer.append(cardHtml);
        });
    }

    // --- Polling for task status updates ---
    function pollTaskStatus() {
        // Check if there are any tasks that are processing or scheduled
        // If not, we don't need to poll
        const tasks = $('#yoapsopo_tasks_tbody tr');
        let needsPolling = false;

        tasks.each(function() {
            const status = $(this).find('.yoapsopo-status-badge').text().trim().toLowerCase();
            if (status === 'processing' || status === 'scheduled') {
                needsPolling = true;
                return false; // break the loop
            }
        });

        // If no tasks need polling, skip this round
        if (!needsPolling) {
            return;
        }

        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'yoapsopo_get_tasks_ajax',
                nonce: nonce
            },
            success: function(res) {
                if (res.success && res.data && res.data.tasks) {
                    // Debug: log the tasks to see if status is being updated
                    console.log('Polling results:', res.data.tasks);
                    renderTaskList(res.data.tasks);
                }
            },
            error: function(xhr, status, error) {
                // Silent fail - we don't want to spam the user with polling errors
                console.log('Polling error:', error);
            }
        });
    }

    // Start polling every 2 seconds (instead of 5) for more responsive updates
    setInterval(pollTaskStatus, 2000);

    // Also poll immediately when the page loads to get the latest status
    $(document).ready(function() {
        setTimeout(pollTaskStatus, 1000);
    });

    // --- Submissão do Formulário via AJAX ---
    const form = $('#yoapsopo_form_task');
    const saveButton = $('#yoapsopo_btn_save');
    const originalButtonHtml = saveButton.html();

    form.on('submit', function (e) {
        e.preventDefault();
        saveButton.prop('disabled', true).html(`<span class="dashicons dashicons-update animate-spin mr-2"></span> ${i18n.saving}`);

        const formData = new FormData(this);
        formData.append('action', 'yoapsopo_save_task_ajax');
        formData.append('nonce', nonce);

        $.ajax({
            url: ajax_url, type: 'POST', data: formData, processData: false, contentType: false,
            success: function (res) {
                if (res.success) {
                    toast(i18n.taskSaved);
                    form[0].reset();
                    $('#yoapsopo_text').trigger('input');
                    renderTaskList(res.data.tasks); // Atualiza a lista dinamicamente
                } else { throw new Error(res.data.message || i18n.error); }
            },
            error: (jqXHR, textStatus, errorThrown) => toast(errorThrown, false),
            complete: () => saveButton.prop('disabled', false).html(originalButtonHtml)
        });
    });

    // --- Ações na Tabela de Tarefas (Publicar, Excluir) via AJAX ---
    $('body').on('click', '.yoapsopo-act', function(e) {
        e.preventDefault();
        const button = $(this);
        const action = button.data('act');
        const taskId = button.closest('tr, div.rounded-xl').data('id');

        if (action === 'delete' && !confirm(i18n.deleteConfirm)) return;

        button.prop('disabled', true);

        $.ajax({
            url: ajax_url, type: 'POST', data: { action: 'yoapsopo_task_action', nonce: nonce, act: action, id: taskId },
            success: function(res) {
                if (res.success) {
                    toast(i18n.actionSuccess);
                    renderTaskList(res.data.tasks); // Atualiza a lista dinamicamente
                } else { toast(res.data.message || i18n.actionFailed, false); }
            },
            error: () => toast(i18n.error, false),
            complete: () => button.prop('disabled', false)
        });
    });

    // Initialize mobile cards with existing tasks on page load
    // Fetch the initial tasks via AJAX to ensure we have complete data
    function initializeTaskList() {
        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'yoapsopo_get_tasks_ajax',
                nonce: nonce
            },
            success: function(res) {
                if (res.success && res.data && res.data.tasks) {
                    renderTaskList(res.data.tasks);
                }
            },
            error: function(xhr, status, error) {
                console.log('Initialization error:', error);
            }
        });
    }

    // Initialize task list when the page loads
    initializeTaskList();
});
(function($) {
    'use strict';

    /**
     * WordPress Media Picker Integration
     */
    $(document).on('click', '.yoapsopo-pick', function(e) {
        e.preventDefault();

        var target = $(this).data('target');
        var frame = wp.media({
            title: (window.YOAPSOPO && YOAPSOPO.i18n && YOAPSOPO.i18n.chooseMedia) || 'Choose Media',
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $(target).val(attachment.url).trigger('input');
        });

        // frame.open();
    });

    /**
     * Preview Functionality
     */
    function updatePreview() {
        var img = $('#yoapsopo_image_url').val();
        var vid = $('#yoapsopo_video_url').val();
        var txt = $('textarea[name="text"]').val();

        $('#yoapsopo_prev_text').text(txt);

        if (vid) {
            $('#yoapsopo_prev_vid').attr('src', vid).show();
            $('#yoapsopo_prev_img').hide();
        } else if (img) {
            $('#yoapsopo_prev_img').attr('src', img).show();
            $('#yoapsopo_prev_vid').hide();
        } else {
            $('#yoapsopo_prev_img,#yoapsopo_prev_vid').hide();
        }
    }

    $(document).on('input', '#yoapsopo_image_url,#yoapsopo_video_url,textarea[name="text"]', updatePreview);
    $(updatePreview);

    /**
     * API Communication
     */
    function api(action, data) {
        data = data || {};
        data.action = action;
        data.nonce = (window.YOAPSOPO && YOAPSOPO.nonce) || '';

        return $.post((window.YOAPSOPO && YOAPSOPO.ajax) || ajaxurl, data);
    }

    /**
     * Task Management
     */
    function renderTasks(tasks) {
        var $tbody = $('#yoapsopo_tasks_tbody').empty();

        tasks.forEach(function(task) {
            var networks = (task.networks || []).map(function(network) {
                return '<span class="badge">' + escapeHtml(network) + '</span>';
            }).join(' ');

            var row = [
                '<tr data-id="' + task.id + '">',
                '  <td data-label="' + ((window.YOAPSOPO && YOAPSOPO.i18n && YOAPSOPO.i18n.title) || 'Title') + '">' + escapeHtml(task.title || '—') + '</td>',
                '  <td data-label="' + ((window.YOAPSOPO && YOAPSOPO.i18n && YOAPSOPO.i18n.networks) || 'Networks') + '">' + networks + '</td>',
                '  <td data-label="' + ((window.YOAPSOPO && YOAPSOPO.i18n && YOAPSOPO.i18n.type) || 'Type') + '">' + escapeHtml(task.type || '') + '</td>',
                '  <td data-label="' + ((window.YOAPSOPO && YOAPSOPO.i18n && YOAPSOPO.i18n.when) || 'When') + '">' + escapeHtml(task.when || '—') + '</td>',
                '  <td data-label="' + ((window.YOAPSOPO && YOAPSOPO.i18n && YOAPSOPO.i18n.status) || 'Status') + '">',
                '    <span class="status status-' + escapeHtml(task.status || '') + '">',
                '      ' + escapeHtml(task.status || ''),
                '    </span>',
                '  </td>',
                '  <td data-label="' + ((window.YOAPSOPO && YOAPSOPO.i18n && YOAPSOPO.i18n.actions) || 'Actions') + '" class="yoapsopo-actions-cell">',
                '    <button class="button yoapsopo-act" data-act="send" ' + (YOAPSOPO.hasKeys ? '' : 'disabled') + '>',
                '      ' + ((window.YOAPSOPO && YOAPSOPO.i18n && YOAPSOPO.i18n.send) || 'Send'),
                '    </button>',
                '    <button class="button yoapsopo-act" data-act="refresh">',
                '      ' + ((window.YOAPSOPO && YOAPSOPO.i18n && YOAPSOPO.i18n.refreshStatus) || 'Refresh status'),
                '    </button>',
                '    <button class="button button-link-delete yoapsopo-act" data-act="delete">',
                '      ' + ((window.YOAPSOPO && YOAPSOPO.i18n && YOAPSOPO.i18n.delete) || 'Delete'),
                '    </button>',
                '  </td>',
                '</tr>'
            ].join('');

            $tbody.append(row);
        });
    }

    /**
     * Utility Functions
     */
    function escapeHtml(text) {
        return $('<div>').text(text || '').html();
    }

    /**
     * Task Polling
     */
    var pollTimer = null;

    function schedulePolling(tasks) {
        var needsPolling = (tasks || []).some(function(task) {
            return task.status === 'processing' || task.status === 'scheduled';
        });

        if (needsPolling) {
            if (pollTimer) {
                clearInterval(pollTimer);
            }
            pollTimer = setInterval(function() {
                refreshTasks(false);
            }, 8000);
        } else {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        }
    }

    function refreshTasks(startPolling) {
        api('yoapsopo_get_tasks', {}).done(function(response) {
            if (response && response.success) {
                renderTasks(response.data.tasks || []);
                if (startPolling !== false) {
                    schedulePolling(response.data.tasks || []);
                }
            }
        });
    }

    /**
     * Event Handlers
     */
    $(document).on('click', '#yoapsopo_refresh_btn', function(e) {
        e.preventDefault();
        refreshTasks();
    });

    $(document).on('click', '.yoapsopo-act', function(e) {
        e.preventDefault();

        var $row = $(this).closest('tr');
        var taskId = $row.data('id');
        var action = $(this).data('act');

        api('yoapsopo_task_action', {
            id: taskId,
            act: action
        }).done(function(response) {
            if (response && response.success) {
                renderTasks(response.data.tasks || []);
                schedulePolling(response.data.tasks || []);
            }
        });
    });

})(jQuery);
(function(){
    // DOM Elements
    const previewDevice = document.getElementById('yoapsopo_device');
    const previewPills = document.querySelectorAll('#yoapsopo_prev_modes .yoapsopo-pill');
    const textInput = document.getElementById('yoapsopo_text');
    const imageInput = document.getElementById('yoapsopo_image_url');
    const videoInput = document.getElementById('yoapsopo_video_url');
    const typeSelect = document.querySelector('select[name="type"]');
    const networkCheckboxes = document.querySelectorAll('input[name="networks[]"]');

    // Preview containers
    const facebookPreview = document.getElementById('facebook_preview');
    const instagramPreview = document.getElementById('instagram_preview');
    const storiesPreview = document.getElementById('stories_preview');
    const reelsPreview = document.getElementById('reels_preview');
    const tiktokPreview = document.getElementById('tiktok_preview');
    const youtubePreview = document.getElementById('youtube_preview');
    const shortsPreview = document.getElementById('shorts_preview');

    // Handles data
    const handles = {
        facebook: '@yourfacebook',
        instagram: '@yourinstagram',
        youtube: '@youryoutube',
        tiktok: '@yourtiktok'
    };

    // Get handles from meta element if available
    const metaElement = document.getElementById('yoapsopo_prev_meta');
    if (metaElement) {
        handles.facebook = metaElement.dataset.facebook || handles.facebook;
        handles.instagram = metaElement.dataset.instagram || handles.instagram;
        handles.youtube = metaElement.dataset.youtube || handles.youtube;
        handles.tiktok = metaElement.dataset.tiktok || handles.tiktok;
    }

    // Content filtering rules - networks that DON'T support certain content types
    const contentRestrictions = {
        image: ['tiktok', 'youtube'], // TikTok and YouTube primarily focus on video
        video: [], // All platforms support video
        story: ['tiktok', 'youtube'] // Only Facebook and Instagram have stories
    };

    // Current preview mode
    let currentMode = 'feed';

    // Preview mode mappings
    const previewModes = {
        feed: { container: instagramPreview, aspectRatio: '125%' }, // 4:5 Instagram feed
        story: { container: storiesPreview, aspectRatio: '177.78%' }, // 9:16
        reels: { container: reelsPreview, aspectRatio: '177.78%' }, // 9:16
        tiktok: { container: tiktokPreview, aspectRatio: '177.78%' }, // 9:16
        youtube: { container: youtubePreview, aspectRatio: '56.25%' }, // 16:9
        shorts: { container: shortsPreview, aspectRatio: '177.78%' } // 9:16
    };

    // Initialize preview system
    function initPreview() {
        // Set up mode switching buttons
        for (let i = 0; i < previewPills.length; i++) {
            previewPills[i].addEventListener('click', function(e) {
                e.preventDefault();
                const mode = this.dataset.mode;
                switchPreviewMode(mode);
            });
        }

        // Set up input listeners with debounce
        if (textInput) {
            textInput.addEventListener('input', debounce(() => {
                updatePreviewContent();
                if (textInput.value.trim()) {
                    autoSwitchPreviewMode();
                }
            }));
        }

        if (imageInput) {
            imageInput.addEventListener('input', debounce(() => {
                updatePreviewMedia();
                filterNetworksByContent();
                if (imageInput.value.trim()) {
                    setTimeout(() => autoSwitchPreviewMode(), 50);
                }
            }));
        }

        if (videoInput) {
            videoInput.addEventListener('input', debounce(() => {
                updatePreviewMedia();
                filterNetworksByContent();
                if (videoInput.value.trim()) {
                    setTimeout(() => autoSwitchPreviewMode(), 50);
                }
            }));
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', () => {
                handleTypeChange();
                filterNetworksByContent();
                autoSwitchPreviewMode();
            });
        }

        // Set up network checkbox listeners
        for (let j = 0; j < networkCheckboxes.length; j++) {
            networkCheckboxes[j].addEventListener('change', handleNetworkChange);
        }

        // Initial setup
        updatePreviewContent();
        updatePreviewMedia();
        filterNetworksByContent();
        autoSwitchPreviewMode();

        // Ensure action overlays are visible from start
        showActionOverlays(true);
    }

    // Switch between preview modes
    function switchPreviewMode(mode) {
        if (!previewModes[mode]) return;

        currentMode = mode;

        // Update active pill
        for (let i = 0; i < previewPills.length; i++) {
            previewPills[i].classList.toggle('yoapsopo-pill--active', previewPills[i].dataset.mode === mode);
        }

        // Animate pill selection
        const activePill = document.querySelector(`.yoapsopo-pill[data-mode="${mode}"]`);
        if (activePill) {
            activePill.style.transform = 'translateY(-2px) scale(1.05)';
            setTimeout(() => {
                activePill.style.transform = activePill.classList.contains('yoapsopo-pill--active') ? 'translateY(-1px)' : '';
            }, 200);
        }

        // Update device data-mode and aspect ratio with animation
        if (previewDevice) {
            previewDevice.dataset.mode = mode;
            const frame = document.getElementById('yoapsopo_prev_frame');
            if (frame) {
                frame.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                frame.style.setProperty('--yoapsopo-ar', previewModes[mode].aspectRatio);
            }
        }

        // Hide all previews with fade effect
        const allModes = Object.values(previewModes);
        for (let i = 0; i < allModes.length; i++) {
            const container = allModes[i].container;
            if (container) {
                container.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                container.style.opacity = '0';
                container.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    if (container) container.classList.add('hidden');
                }, 200);
            }
        }

        // Show current preview with fade in effect
        setTimeout(() => {
            const current = previewModes[mode].container;
            if (current) {
                current.classList.remove('hidden');
                setTimeout(() => {
                    if (current) {
                        current.style.opacity = '1';
                        current.style.transform = 'scale(1)';
                    }
                }, 50);
            }
        }, 250);

        // Update content for current mode
        setTimeout(() => {
            updatePreviewContent();
            updatePreviewMedia();
        }, 100);
    }

    // Handle type selection change with enhanced UX
    function handleTypeChange() {
        const type = typeSelect ? typeSelect.value : '';

        // Auto-switch preview mode based on type with animation
        switch (type) {
            case 'image':
                switchPreviewMode('feed');
                break;
            case 'video':
                switchPreviewMode('reels');
                break;
            case 'story':
                switchPreviewMode('story');
                break;
            case 'live_schedule':
                switchPreviewMode('youtube');
                break;
        }
    }

    // Enhanced content filtering with visual feedback
    function filterNetworksByContent() {
        const currentType = typeSelect ? typeSelect.value : 'image';
        const hasImage = imageInput ? imageInput.value.trim() : '';
        const hasVideo = videoInput ? videoInput.value.trim() : '';

        // Determine content type from inputs if type is mixed
        let contentType = currentType;
        if (currentType === 'video' || hasVideo) {
            contentType = 'video';
        } else if (hasImage && !hasVideo) {
            contentType = 'image';
        }

        const restrictedNetworks = contentRestrictions[contentType] || [];

        // Update network checkboxes with smooth animations
        for (let i = 0; i < networkCheckboxes.length; i++) {
            const checkbox = networkCheckboxes[i];
            const network = checkbox.value;
            const isRestricted = restrictedNetworks.includes(network);

            // Disable checkbox and provide visual feedback
            checkbox.disabled = isRestricted;

            // Find the parent label/container
            const label = checkbox.closest('label');
            if (label) {
                label.style.transition = 'all 0.3s ease';

                if (isRestricted) {
                    label.style.opacity = '0.4';
                    label.style.pointerEvents = 'none';
                    label.style.transform = 'scale(0.95)';
                    label.title = `${network.charAt(0).toUpperCase() + network.slice(1)} doesn't support ${contentType} posts`;
                    // Uncheck if currently checked
                    if (checkbox.checked) {
                        checkbox.checked = false;
                    }
                } else {
                    label.style.opacity = '1';
                    label.style.pointerEvents = 'auto';
                    label.style.transform = 'scale(1)';
                    label.title = '';
                }
            }
        }
    }

    // Enhanced auto-switch with smart logic
    function autoSwitchPreviewMode() {
        const hasImage = imageInput ? imageInput.value.trim() : '';
        const hasVideo = videoInput ? videoInput.value.trim() : '';

        // Get selected networks
        const selectedNetworks = [];
        for (let i = 0; i < networkCheckboxes.length; i++) {
            if (networkCheckboxes[i].checked) {
                selectedNetworks.push(networkCheckboxes[i].value);
            }
        }

        // If no media, don't auto-switch
        if (!hasImage && !hasVideo) return;

        // ENHANCED Priority logic for auto-switching
        if (hasVideo) {
            // Video content - prioritize video platforms
            if (selectedNetworks.includes('tiktok')) {
                switchPreviewMode('tiktok');
            } else if (selectedNetworks.includes('youtube')) {
                switchPreviewMode('shorts');
            } else if (selectedNetworks.includes('instagram')) {
                switchPreviewMode('reels');
            } else {
                // Default to most popular video format
                switchPreviewMode('reels');
            }
        } else if (hasImage) {
            // Image content - check type and networks
            const currentType = typeSelect ? typeSelect.value : '';
            if (currentType === 'story') {
                switchPreviewMode('story');
            } else if (selectedNetworks.includes('instagram')) {
                switchPreviewMode('feed');
            } else if (selectedNetworks.includes('facebook')) {
                switchPreviewMode('feed');
            } else {
                // Default to Instagram feed format
                switchPreviewMode('feed');
            }
        }

        // Update content after switching
        setTimeout(() => {
            updatePreviewContent();
            updatePreviewMedia();
        }, 50);
    }

    // Handle network change events
    function handleNetworkChange() {
        // Auto-switch preview when networks change
        autoSwitchPreviewMode();
    }

    // Enhanced content updates with real-time typing effects
    function updatePreviewContent() {
        const text = textInput ? textInput.value : '';
        const cleanText = text.trim();

        // Update all text content areas with immediate updates
        const textElements = [
            'fb_text_content',
            'ig_text_content',
            'story_text_content',
            'reels_text_content',
            'tiktok_text_content',
            'youtube_text_content',
            'shorts_text_content'
        ];

        for (let i = 0; i < textElements.length; i++) {
            const id = textElements[i];
            const el = document.getElementById(id);
            if (el) {
                // Immediate text update for real-time feedback
                el.textContent = cleanText || 'Your content here...';
            }
        }

        // Update character counter
        const charCounter = document.getElementById('yoapsopo_char');
        if (charCounter) {
            charCounter.textContent = text.length;
        }

        // Update usernames
        updateUsernames();
    }

    // Update usernames with smooth transitions
    function updateUsernames() {
        // Remove @ symbol from handles for consistent display
        const cleanHandles = {
            facebook: handles.facebook.replace(/^@/, ''),
            instagram: handles.instagram.replace(/^@/, ''),
            youtube: handles.youtube.replace(/^@/, ''),
            tiktok: handles.tiktok.replace(/^@/, '')
        };

        // Facebook
        const fbName = document.getElementById('fb_display_name');
        if (fbName) {
            updateElementWithFade(fbName, cleanHandles.facebook || 'Your Page Name');
        }

        // Instagram
        const igUsername = document.getElementById('ig_username');
        const igCaptionUsername = document.getElementById('ig_caption_username');
        if (igUsername) updateElementWithFade(igUsername, cleanHandles.instagram || 'yourusername');
        if (igCaptionUsername) updateElementWithFade(igCaptionUsername, cleanHandles.instagram || 'yourusername');

        // Stories
        const storyUsername = document.getElementById('story_username');
        if (storyUsername) updateElementWithFade(storyUsername, cleanHandles.instagram || 'yourusername');

        // Reels
        const reelsUsername = document.getElementById('reels_username');
        if (reelsUsername) updateElementWithFade(reelsUsername, cleanHandles.instagram || 'yourusername');

        // TikTok (always keep @ symbol for TikTok)
        const tiktokUsername = document.getElementById('tiktok_username');
        if (tiktokUsername) updateElementWithFade(tiktokUsername, '@' + cleanHandles.tiktok || '@yourusername');

        // YouTube
        const youtubeUsername = document.getElementById('youtube_username');
        if (youtubeUsername) updateElementWithFade(youtubeUsername, cleanHandles.youtube || 'Your Channel');

        // Shorts
        const shortsUsername = document.getElementById('shorts_username');
        if (shortsUsername) updateElementWithFade(shortsUsername, cleanHandles.youtube || 'Your Channel');
    }

    // Helper function for smooth element updates
    function updateElementWithFade(element, newText) {
        if (!element) return;

        element.style.transition = 'opacity 0.15s ease';
        element.style.opacity = '0.6';

        setTimeout(() => {
            element.textContent = newText;
            element.style.opacity = '1';
        }, 75);
    }

    // Debounce utility
    function debounce(fn, delay = 300) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn(...args), delay);
        };
    }

// Update preview media with 100% width and vertical centering
    function updatePreviewMedia() {
        const imageUrl = imageInput ? imageInput.value.trim() : '';
        const videoUrl = videoInput ? videoInput.value.trim() : '';

        // Get current media type based on type selection or available media
        const type = typeSelect ? typeSelect.value : 'image';
        const isVideo = type === 'video' || (videoUrl && !imageUrl);
        const mediaUrl = isVideo ? videoUrl : imageUrl;

        // Update all preview images with 100% width and vertical centering
        const imageElements = [
            'fb_prev_img', 'ig_prev_img', 'story_prev_img',
            'reels_prev_img', 'tiktok_prev_img', 'youtube_prev_img', 'shorts_prev_img'
        ];

        const videoElements = [
            'fb_prev_vid', 'ig_prev_vid', 'story_prev_vid',
            'reels_prev_vid', 'tiktok_prev_vid', 'youtube_prev_vid', 'shorts_prev_vid'
        ];

        // Show/hide and style image elements
        for (let i = 0; i < imageElements.length; i++) {
            const id = imageElements[i];
            const el = document.getElementById(id);
            if (el) {
                if (!isVideo && mediaUrl) {
                    if (el.src !== mediaUrl) {
                        el.src = mediaUrl;
                    }
                    el.style.width = '100%';
                    el.style.height = '100%';
                    el.style.objectFit = 'contain';
                    el.style.display = 'block';
                    el.classList.remove('hidden');
                    showActionOverlays(true);
                } else {
                    el.classList.add('hidden');
                    el.src = '';
                }
            }
        }

        // Show/hide and style video elements
        for (let i = 0; i < videoElements.length; i++) {
            const id = videoElements[i];
            const el = document.getElementById(id);
            if (el) {
                if (isVideo && mediaUrl) {
                    el.style.width = '100%';
                    el.style.height = '100%';
                    el.style.objectFit = 'contain';
                    el.style.display = 'block';
                    el.muted = true;
                    el.loop = true;
                    el.classList.remove('hidden');

                    // Only update src and play if necessary
                    if (el.src !== mediaUrl) {
                        el.pause(); // Pause to avoid interrupting previous play
                        el.src = mediaUrl;
                        el.load(); // Ensure fresh load
                        // Only play if not already playing
                        if (el.paused) {
                            el.play().catch(() => {
                                // Silent catch to avoid logging AbortError
                            });
                        }
                    } else if (el.paused) {
                        // Resume playback if paused but src is unchanged
                        el.play().catch(() => {});
                    }
                    showActionOverlays(true);
                } else {
                    el.classList.add('hidden');
                    el.pause();
                    el.src = '';
                }
            }
        }

        // Hide action overlays if no media
        if (!mediaUrl) {
            showActionOverlays(false);
        }
    }

    // Show/hide action overlays for horizontal formats
    function showActionOverlays(show) {
        const overlays = [
            document.querySelector('.fb-action-overlay'),
            document.querySelector('.ig-action-overlay'),
            document.querySelector('.youtube-action-overlay')
        ];

        for (let i = 0; i < overlays.length; i++) {
            const overlay = overlays[i];
            if (overlay) {
                overlay.style.display = show ? 'flex' : 'none';
                overlay.style.opacity = show ? '1' : '0';
            }
        }
    }

    // Initialize when DOM is ready with New Task auto-preview
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initPreview();

            // Auto-start with "New Task" preview mode
            switchPreviewMode('feed');

            // Show empty state with sample content
            updatePreviewContent();
            updatePreviewMedia();
        });
    } else {
        // DOM is already ready
        initPreview();

        // Auto-start with "New Task" preview mode
        switchPreviewMode('feed');

        // Show empty state with sample content
        updatePreviewContent();
        updatePreviewMedia();
    }

    // Also initialize on load for safety
    window.addEventListener('load', function() {
        initPreview();

        // Ensure auto-preview is working
        if (!currentMode) {
            switchPreviewMode('feed');
        }
    });

    // Expose autoSwitchPreviewMode globally for upload integration
    window.autoSwitchPreviewMode = autoSwitchPreviewMode;

})();
(function(){
    // Toggle secret
    var btn = document.getElementById('yoapsopo_secret_toggle');
    var inp = document.getElementById('yoapsopo_secret');
    if(btn && inp){
        btn.addEventListener('click', function(){
            var showing = inp.type === 'text';
            inp.type = showing ? 'password' : 'text';
            btn.innerHTML = (showing ? '<span class="dashicons dashicons-visibility"></span> Show' : '<span class="dashicons dashicons-hidden"></span> Hide')
        });
    }

    // Copy helpers
    function toast(msg){
        var t = document.createElement('div');
        t.className = 'fixed z-50 bottom-5 right-5 bg-slate-900 text-white text-sm rounded-lg px-3 py-2 shadow-lg transition-all';
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(function(){ t.style.opacity='0'; t.style.transform='translateY(6px)'; }, 1400);
        setTimeout(function(){ t.remove(); }, 1800);
    }
    document.querySelectorAll('[data-clip]').forEach(function(btn){
        btn.addEventListener('click', function(){
            var sel = btn.getAttribute('data-clip');
            var el = document.querySelector(sel);
            if(!el) return;
            if (el.select) el.select();
            try {
                navigator.clipboard.writeText(el.value || '');
                toast('Copied!');
            } catch(e){
                toast('Copy manually.');
            }
        });
    });
})();
