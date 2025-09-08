/**
 * Admin JavaScript for YoApy Social Poster
 *
 * @package YoApySocialPoster
 * @since 1.6.0
 */

(function($) {
    'use strict';

    /**
     * WordPress Media Picker Integration
     */
    $(document).on('click', '.ysp-pick', function(e) {
        e.preventDefault();
        
        var target = $(this).data('target');
        var frame = wp.media({
            title: (window.YSP && YSP.i18n && YSP.i18n.chooseMedia) || 'Choose Media',
            multiple: false
        });
        
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $(target).val(attachment.url).trigger('input');
        });
        
        frame.open();
    });

    /**
     * Preview Functionality
     */
    function updatePreview() {
        var img = $('#ysp_image_url').val();
        var vid = $('#ysp_video_url').val();
        var txt = $('textarea[name="text"]').val();
        
        $('#ysp_prev_text').text(txt);
        
        if (vid) {
            $('#ysp_prev_vid').attr('src', vid).show();
            $('#ysp_prev_img').hide();
        } else if (img) {
            $('#ysp_prev_img').attr('src', img).show();
            $('#ysp_prev_vid').hide();
        } else {
            $('#ysp_prev_img,#ysp_prev_vid').hide();
        }
    }
    
    $(document).on('input', '#ysp_image_url,#ysp_video_url,textarea[name="text"]', updatePreview);
    $(updatePreview);

    /**
     * API Communication
     */
    function api(action, data) {
        data = data || {};
        data.action = action;
        data.nonce = (window.YSP && YSP.nonce) || '';
        
        return $.post((window.YSP && YSP.ajax) || ajaxurl, data);
    }

    /**
     * Task Management
     */
    function renderTasks(tasks) {
        var $tbody = $('#ysp_tasks_tbody').empty();
        
        tasks.forEach(function(task) {
            var networks = (task.networks || []).map(function(network) {
                return '<span class="badge">' + escapeHtml(network) + '</span>';
            }).join(' ');
            
            var row = [
                '<tr data-id="' + task.id + '">',
                '  <td data-label="' + ((window.YSP && YSP.i18n && YSP.i18n.title) || 'Title') + '">' + escapeHtml(task.title || '—') + '</td>',
                '  <td data-label="' + ((window.YSP && YSP.i18n && YSP.i18n.networks) || 'Networks') + '">' + networks + '</td>',
                '  <td data-label="' + ((window.YSP && YSP.i18n && YSP.i18n.type) || 'Type') + '">' + escapeHtml(task.type || '') + '</td>',
                '  <td data-label="' + ((window.YSP && YSP.i18n && YSP.i18n.when) || 'When') + '">' + escapeHtml(task.when || '—') + '</td>',
                '  <td data-label="' + ((window.YSP && YSP.i18n && YSP.i18n.status) || 'Status') + '">',
                '    <span class="status status-' + escapeHtml(task.status || '') + '">',
                '      ' + escapeHtml(task.status || ''),
                '    </span>',
                '  </td>',
                '  <td data-label="' + ((window.YSP && YSP.i18n && YSP.i18n.actions) || 'Actions') + '" class="ysp-actions-cell">',
                '    <button class="button ysp-act" data-act="send" ' + (YSP.hasKeys ? '' : 'disabled') + '>',
                '      ' + ((window.YSP && YSP.i18n && YSP.i18n.send) || 'Send'),
                '    </button>',
                '    <button class="button ysp-act" data-act="refresh">',
                '      ' + ((window.YSP && YSP.i18n && YSP.i18n.refreshStatus) || 'Refresh status'),
                '    </button>',
                '    <button class="button button-link-delete ysp-act" data-act="delete">',
                '      ' + ((window.YSP && YSP.i18n && YSP.i18n.delete) || 'Delete'),
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
        api('ysp_get_tasks', {}).done(function(response) {
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
    $(document).on('click', '#ysp_refresh_btn', function(e) {
        e.preventDefault();
        refreshTasks();
    });

    $(document).on('click', '.ysp-act', function(e) {
        e.preventDefault();
        
        var $row = $(this).closest('tr');
        var taskId = $row.data('id');
        var action = $(this).data('act');
        
        api('ysp_task_action', {
            id: taskId,
            act: action
        }).done(function(response) {
            if (response && response.success) {
                renderTasks(response.data.tasks || []);
                schedulePolling(response.data.tasks || []);
            }
        });
    });

    /**
     * Initialize
     */
    $(function() {
        refreshTasks();
    });

})(jQuery);