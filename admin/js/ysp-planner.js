/**
 * Script para a página do YoApy Planner com AJAX.
 *
 * @package YoApySocialPoster
 * @since 1.6.0
 */
jQuery(document).ready(function ($) {
    const ajax_url = ysp_ajax_object.ajax_url;
    const nonce = ysp_ajax_object.nonce;
    const i18n = ysp_ajax_object.i18n;

    // --- Notificações (Toast) ---
    function toast(msg, isSuccess = true) {
        const toastId = 'ysp-toast-' + Date.now();
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
        const tableBody = $('#ysp_tasks_tbody');
        const cardsContainer = $('#ysp_tasks_cards');
        const placeholder = $('#ysp-no-tasks-placeholder');

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
                return `<span class="ysp-net-chip ysp-net--${n}">${networkName}</span>`;
            }).join('');

            const resultsHtml = task.results ? Object.entries(task.results).map(([net, res]) => {
                const networkName = net.charAt(0).toUpperCase() + net.slice(1);
                if (res && res.permalink) return `<a href="${res.permalink}" target="_blank" class="ysp-result-link">${networkName}</a>`;
                if (res && res.success === false) return `<div class="ysp-result-error" title="${res.message || ''}">${networkName}: Error</div>`;
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
                    <td class="px-4 py-3 align-top"><span class="ysp-status-badge ysp-status--${task.status}">${task.status}</span><div class="mt-1.5 space-y-1 text-xs">${resultsHtml}</div></td>
                    <td class="px-4 py-3 align-top">
                        <div class="flex items-center gap-2">
                            <button class="button ysp-act" data-act="send"><span class="dashicons dashicons-migrate"></span> ${i18n.send || 'Post'}</button>
                            <button class="button button-link-delete ysp-act" data-act="delete"><span class="dashicons dashicons-trash"></span> ${i18n.delete || 'Delete'}</button>
                        </div>
                    </td>
                </tr>`;
            tableBody.append(tableRow);
        });
    }

    // --- Polling for task status updates ---
    function pollTaskStatus() {
        // Check if there are any tasks that are processing or scheduled
        // If not, we don't need to poll
        const tasks = $('#ysp_tasks_tbody tr');
        let needsPolling = false;
        
        tasks.each(function() {
            const status = $(this).find('.ysp-status-badge').text().trim().toLowerCase();
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
                action: 'ysp_get_tasks_ajax',
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
    const form = $('#ysp_form_task');
    const saveButton = $('#ysp_btn_save');
    const originalButtonHtml = saveButton.html();

    form.on('submit', function (e) {
        e.preventDefault();
        saveButton.prop('disabled', true).html(`<span class="dashicons dashicons-update animate-spin mr-2"></span> ${i18n.saving}`);

        const formData = new FormData(this);
        formData.append('action', 'ysp_save_task_ajax');
        formData.append('nonce', nonce);

        $.ajax({
            url: ajax_url, type: 'POST', data: formData, processData: false, contentType: false,
            success: function (res) {
                if (res.success) {
                    toast(i18n.taskSaved);
                    form[0].reset();
                    $('#ysp_text').trigger('input');
                    renderTaskList(res.data.tasks); // Atualiza a lista dinamicamente
                } else { throw new Error(res.data.message || i18n.error); }
            },
            error: (jqXHR, textStatus, errorThrown) => toast(errorThrown, false),
            complete: () => saveButton.prop('disabled', false).html(originalButtonHtml)
        });
    });

    // --- Ações na Tabela de Tarefas (Publicar, Excluir) via AJAX ---
    $('body').on('click', '.ysp-act', function(e) {
        e.preventDefault();
        const button = $(this);
        const action = button.data('act');
        const taskId = button.closest('tr, div.rounded-xl').data('id');

        if (action === 'delete' && !confirm(i18n.deleteConfirm)) return;

        button.prop('disabled', true);

        $.ajax({
            url: ajax_url, type: 'POST', data: { action: 'ysp_task_action', nonce: nonce, act: action, id: taskId },
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
});