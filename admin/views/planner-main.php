<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Inclui funções de ajuda (helpers)
require_once 'helpers.php';

// Obtém tarefas, KPIs e configurações
$tasks = isset($tasks) && is_array($tasks) ? $tasks : array();
$kpis = array('pending'=>0,'scheduled'=>0,'processing'=>0,'complete'=>0,'error'=>0);
foreach ($tasks as $t) { $k = strtolower($t['status'] ?? 'pending'); if(isset($kpis[$k])) $kpis[$k]++; }

$opt = get_option('ysp_settings', array());
$hasKeys = YSP_Client::has_keys();

// Prepara os nomes de usuário (@handles) para o preview
$ysp_handle_default  = !empty($opt['account']) ? ltrim($opt['account'], '@') : 'yourusername';
$ysp_handles = array(
    'facebook'  => !empty($opt['account_facebook'])  ? ltrim($opt['account_facebook'], '@')  : $ysp_handle_default,
    'instagram' => !empty($opt['account_instagram']) ? ltrim($opt['account_instagram'], '@') : $ysp_handle_default,
    'youtube'   => !empty($opt['account_youtube'])   ? ltrim($opt['account_youtube'], '@')   : $ysp_handle_default,
    'tiktok'    => !empty($opt['account_tiktok'])    ? ltrim($opt['account_tiktok'], '@')    : $ysp_handle_default,
);
?>

<div class="wrap ysp-wrap">
    <!-- Cabeçalho Principal com KPIs -->
    <div class="mb-6 rounded-2xl p-6 text-white" style="background:linear-gradient(135deg,#0ea5e9,#7c3aed)">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="!m-0 text-2xl font-semibold"><?php esc_html_e( 'YoApy Planner', 'yoapy-social-poster' ); ?></h1>
                <p class="opacity-90 mt-1"><?php esc_html_e( 'Schedule independent publications and track their status.', 'yoapy-social-poster' ); ?></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo esc_url( admin_url('admin.php?page=ysp_settings') ); ?>" class="inline-flex items-center gap-2 rounded-lg bg-white/90 px-3 py-2 text-slate-800 hover:bg-white">
                    <span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'Settings', 'yoapy-social-poster' ); ?>
                </a>
            </div>
        </div>
        <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-5">
            <div class="rounded-xl bg-white/10 p-3"><div class="text-xs opacity-90"><?php esc_html_e( 'Pending', 'yoapy-social-poster' ); ?></div><div class="text-xl font-semibold"><?php echo intval($kpis['pending']); ?></div></div>
            <div class="rounded-xl bg-white/10 p-3"><div class="text-xs opacity-90"><?php esc_html_e( 'Scheduled', 'yoapy-social-poster' ); ?></div><div class="text-xl font-semibold"><?php echo intval($kpis['scheduled']); ?></div></div>
            <div class="rounded-xl bg-white/10 p-3"><div class="text-xs opacity-90"><?php esc_html_e( 'Processing', 'yoapy-social-poster' ); ?></div><div class="text-xl font-semibold"><?php echo intval($kpis['processing']); ?></div></div>
            <div class="rounded-xl bg-white/10 p-3"><div class="text-xs opacity-90"><?php esc_html_e( 'Completed', 'yoapy-social-poster' ); ?></div><div class="text-xl font-semibold"><?php echo intval($kpis['complete']); ?></div></div>
            <div class="rounded-xl bg-white/10 p-3"><div class="text-xs opacity-90"><?php esc_html_e( 'Errors', 'yoapy-social-poster' ); ?></div><div class="text-xl font-semibold"><?php echo intval($kpis['error']); ?></div></div>
        </div>
    </div>

    <?php if ( ! $hasKeys ): ?>
        <div class="notice notice-warning">
            <p><?php
                /* translators: 1: opening <a> tag linking to the Settings page; 2: closing </a> tag. */
                printf( esc_html__( 'Configure your YoApy API keys in %1$sSettings%2$s to send tasks.', 'yoapy-social-poster' ), '<a href="' . esc_url( admin_url('admin.php?page=ysp_settings') ) . '">', '</a>' ); ?></p>
        </div>
    <?php endif; ?>

    <!-- ====== Linha 1: Composer (esquerda) + Preview (direita) ====== -->
    <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <!-- COMPOSER -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-slate-800">
                <span class="dashicons dashicons-edit"></span> <?php esc_html_e( 'New Task', 'yoapy-social-poster' ); ?>
            </h2>
            <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" id="ysp_form_task" class="space-y-5">
                <?php wp_nonce_field('ysp_save_task'); ?>
                <input type="hidden" name="action" value="ysp_save_task"/>
                <!-- Redes (com Toggles) -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700"><?php esc_html_e( 'Networks', 'yoapy-social-poster' ); ?></label>
                    <div class="flex flex-wrap gap-4">
                        <?php foreach (['facebook'=>'Facebook','instagram'=>'Instagram','youtube'=>'YouTube','tiktok'=>'TikTok'] as $k=>$label):
                            $colors = [
                                'facebook'  => 'peer-checked:bg-[#1877F2]',
                                'instagram' => 'peer-checked:bg-gradient-to-r peer-checked:from-[#f58529] peer-checked:via-[#dd2a7b] peer-checked:to-[#515bd4]',
                                'youtube'   => 'peer-checked:bg-[#FF0000]',
                                'tiktok'    => 'peer-checked:bg-gradient-to-r peer-checked:from-[#69C9D0] peer-checked:to-[#EE1D52]'
                            ];
                            ?>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="networks[]" value="<?php echo esc_attr($k); ?>" class="sr-only peer">
                                <div class="ysp-track <?php echo esc_attr( $colors[$k] ); ?>"><div class="ysp-thumb"></div></div>
                                <span class="text-sm font-medium text-slate-600"><?php echo esc_html($label); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Tipo -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700"><?php esc_html_e( 'Type', 'yoapy-social-poster' ); ?></label>
                    <select name="type" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                        <option value="image">🖼️ <?php esc_html_e( 'Image Post (FB/IG)', 'yoapy-social-poster' ); ?></option>
                        <option value="video">🎬 <?php esc_html_e( 'Video/Reels (IG/FB/YouTube/TikTok)', 'yoapy-social-poster' ); ?></option>
                        <option value="story">📒 <?php esc_html_e( 'Story (FB/IG)', 'yoapy-social-poster' ); ?></option>
                        <option value="live_schedule">🔴 <?php esc_html_e( 'YouTube Live Schedule', 'yoapy-social-poster' ); ?></option>
                    </select>
                </div>
                <!-- Texto -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-slate-700"><?php esc_html_e( 'Caption / Title', 'yoapy-social-poster' ); ?></label>
                        <span id="ysp_char">0</span>
                    </div>
                    <textarea name="text" rows="5" id="ysp_text" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="<?php esc_attr_e( 'Write your text... (#hashtags, @mentions)', 'yoapy-social-poster' ); ?>"></textarea>
                </div>
                <!-- Mídias com Drag & Drop -->
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700"><?php esc_html_e('Image URL', 'yoapy-social-poster'); ?></label>
                        <div class="flex gap-2">
                            <input type="url" id="ysp_image_url" name="image_url" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="<?php esc_attr_e( 'https://...', 'yoapy-social-poster' ); ?>">
                            <button type="button" class="button ysp-pick" data-target="#ysp_image_url"><span class="dashicons dashicons-format-image"></span></button>
                        </div>
                        <div id="ysp_dz_img" class="ysp-dz mt-2" data-target="#ysp_image_url" data-accept="image/*">
                            <input class="ysp-dz-file" type="file" accept="image/*" hidden>
                            <div class="ysp-dz-inner"><div class="ysp-dz-ic">🖼️</div><div class="ysp-dz-title"><?php esc_html_e('Drag image here', 'yoapy-social-poster'); ?></div><div class="ysp-dz-sub"><?php esc_html_e('or', 'yoapy-social-poster'); ?> <button type="button" class="ysp-dz-browse"><?php esc_html_e('click to choose', 'yoapy-social-poster'); ?></button></div></div>
                            <div class="ysp-dz-progress" hidden><span></span></div>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700"><?php esc_html_e('Video URL', 'yoapy-social-poster'); ?></label>
                        <div class="flex gap-2">
                            <input type="url" id="ysp_video_url" name="video_url" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="<?php esc_attr_e( 'https://...', 'yoapy-social-poster' ); ?>">
                            <button type="button" class="button ysp-pick" data-target="#ysp_video_url"><span class="dashicons dashicons-video-alt3"></span></button>
                        </div>
                        <div id="ysp_dz_vid" class="ysp-dz mt-2" data-target="#ysp_video_url" data-accept="video/*">
                            <input class="ysp-dz-file" type="file" accept="video/*" hidden>
                            <div class="ysp-dz-inner"><div class="ysp-dz-ic">🎬</div><div class="ysp-dz-title"><?php esc_html_e('Drag video here', 'yoapy-social-poster'); ?></div><div class="ysp-dz-sub"><?php esc_html_e('or', 'yoapy-social-poster'); ?> <button type="button" class="ysp-dz-browse"><?php esc_html_e('click to choose', 'yoapy-social-poster'); ?></button></div></div>
                            <div class="ysp-dz-progress" hidden><span></span></div>
                        </div>
                    </div>
                </div>
                <!-- Article URL -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700"><?php esc_html_e( 'Article URL (FB optional)', 'yoapy-social-poster' ); ?></label>
                    <input type="url" name="article_url" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="<?php esc_attr_e( 'https://...', 'yoapy-social-poster' ); ?>">
                </div>
                <!-- Agendamento -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700"><?php esc_html_e( 'Schedule', 'yoapy-social-poster' ); ?></label>
                    <input type="datetime-local" name="when" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <small class="text-slate-500"><?php esc_html_e( 'Leave empty to send now.', 'yoapy-social-poster' ); ?></small>
                </div>
                <!-- Ações do Formulário -->
                <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center pt-2">
                    <input type="text" class="w-full max-w-xs rounded-lg border border-slate-300 px-3 py-2" name="title" placeholder="<?php esc_attr_e( 'Title (internal)', 'yoapy-social-poster' ); ?>">
                    <button type="submit" class="button button-primary !px-4 !py-2 !h-auto !text-base" id="ysp_btn_save" <?php if(!$hasKeys) echo 'disabled'; ?>>
                        <span class="dashicons dashicons-yes" style="vertical-align:middle"></span> <?php esc_html_e( 'Save Task', 'yoapy-social-poster' ); ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- PREVIEW -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:sticky lg:top-10">
            <div id="ysp_prev_meta" data-default="<?php echo esc_attr($ysp_handle_default); ?>" data-facebook="<?php echo esc_attr($ysp_handles['facebook']); ?>" data-instagram="<?php echo esc_attr($ysp_handles['instagram']); ?>" data-youtube="<?php echo esc_attr($ysp_handles['youtube']); ?>" data-tiktok="<?php echo esc_attr($ysp_handles['tiktok']); ?>" class="hidden"></div>
            <h3 class="mb-3 flex items-center gap-2 text-base font-semibold text-slate-800">
                <span class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'Preview', 'yoapy-social-poster' ); ?>
            </h3>
            <div id="ysp_prev_modes" class="mb-4 flex flex-wrap gap-2">
                <button type="button" data-mode="feed" class="ysp-pill ysp-pill--active">📰 <?php esc_html_e( 'Feed', 'yoapy-social-poster' ); ?></button>
                <button type="button" data-mode="story" class="ysp-pill">📒 <?php esc_html_e( 'Stories', 'yoapy-social-poster' ); ?></button>
                <button type="button" data-mode="reels" class="ysp-pill">🎞️ <?php esc_html_e( 'Reels', 'yoapy-social-poster' ); ?></button>
                <button type="button" data-mode="tiktok" class="ysp-pill">🎵 <?php esc_html_e( 'TikTok', 'yoapy-social-poster' ); ?></button>
                <button type="button" data-mode="youtube" class="ysp-pill">▶️ <?php esc_html_e( 'YouTube', 'yoapy-social-poster' ); ?></button>
                <button type="button" data-mode="shorts" class="ysp-pill">🎬 <?php esc_html_e( 'Shorts', 'yoapy-social-poster' ); ?></button>
            </div>
            <div class="h-[560px] md:h-[640px] flex items-center justify-center w-full">
                <div id="ysp_device" class="ysp-device" data-mode="feed">
                    <div id="ysp_prev_frame" class="ysp-ar" style="--ysp-ar:125%;">
                        <div class="ysp-ar-obj">
                            <?php include 'preview-facebook.php'; ?>
                            <?php include 'preview-instagram.php'; ?>
                            <?php include 'preview-stories.php'; ?>
                            <?php include 'preview-reels.php'; ?>
                            <?php include 'preview-tiktok.php'; ?>
                            <?php include 'preview-youtube.php'; ?>
                            <?php include 'preview-shorts.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====== Linha 2: Lista de Tarefas com Novo Design ====== -->
    <div class="mt-8 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-800">
                <span class="dashicons dashicons-list-view"></span> <?php esc_html_e( 'Tasks', 'yoapy-social-poster' ); ?>
            </h2>
        </div>

        <div class="p-2 md:p-4">
            <!-- Tabela para Desktop -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-2 font-semibold"><?php esc_html_e( 'Title', 'yoapy-social-poster' ); ?></th>
                        <th class="px-4 py-2 font-semibold"><?php esc_html_e( 'Networks', 'yoapy-social-poster' ); ?></th>
                        <th class="px-4 py-2 font-semibold"><?php esc_html_e( 'Status & Results', 'yoapy-social-poster' ); ?></th>
                        <th class="px-4 py-2 font-semibold"><?php esc_html_e( 'Actions', 'yoapy-social-poster' ); ?></th>
                    </tr>
                    </thead>
                    <tbody id="ysp_tasks_tbody" class="divide-y divide-slate-100">
                    <!-- RENDERIZAÇÃO INICIAL DAS TAREFAS VIA PHP -->
                    <?php foreach ( $tasks as $t ): ?>
                        <tr data-id="<?php echo intval($t['id']);?>" class="hover:bg-slate-50/50 transition-colors duration-150">
                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-slate-800"><?php echo esc_html($t['title'] ?: '—'); ?></div>
                                <div class="text-xs text-slate-500"><?php echo esc_html(ucfirst($t['type'])); ?></div>
                                <?php if (!empty($t['when'])): ?>
                                    <?php if ($t['status'] === 'complete'): ?>
                                        <div class="text-xs text-slate-500 mt-1">
                                            ✅ Posted on <?php echo esc_html(date_i18n('M j, Y g:i A', $t['when'])); ?>
                                        </div>
                                    <?php elseif ($t['status'] === 'scheduled'): ?>
                                        <div class="text-xs text-slate-500 mt-1">
                                            🕒 Scheduled for <?php echo esc_html(date_i18n('M j, Y g:i A', $t['when'])); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-xs text-slate-500 mt-1">
                                            📅 Created on <?php echo esc_html(date_i18n('M j, Y g:i A', $t['when'])); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php elseif ($t['status'] === 'complete'): ?>
                                    <!-- For immediate tasks that don't have a scheduled time -->
                                    <div class="text-xs text-slate-500 mt-1">
                                        ✅ Posted on <?php echo esc_html(date_i18n('M j, Y g:i A', time())); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ( (array)($t['networks'] ?? []) as $n ) echo wp_kses_post( ysp_net_chip( $n ) ); ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <?php echo wp_kses_post( ysp_status_badge( $t['status'] ?? '—' ) ); ?>
                                <?php if ( !empty($t['results']) && is_array($t['results']) ): ?>
                                    <div class="mt-1.5 space-y-1 text-xs">
                                        <?php foreach($t['results'] as $net => $result):
                                            $net_name = ucfirst($net);
                                            if(!empty($result['permalink'])): ?>
                                                <a href="<?php echo esc_url($result['permalink']); ?>" target="_blank" class="ysp-result-link"><?php echo esc_html($net_name); ?></a>
                                            <?php elseif (isset($result['success']) && $result['success'] === false): ?>
                                                <div class="ysp-result-error" title="<?php echo esc_attr($result['message'] ?? __( 'Unknown error', 'yoapy-social-poster' )); ?>"><?php echo esc_html($net_name . ': ' . __( 'Error', 'yoapy-social-poster' )); ?></div>
                                            <?php endif;
                                        endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex items-center gap-2">
                                    <button class="button ysp-act" data-act="send"><span class="dashicons dashicons-migrate"></span> <?php esc_html_e('Post', 'yoapy-social-poster'); ?></button>
                                    <button class="button button-link-delete ysp-act" data-act="delete"><span class="dashicons dashicons-trash"></span> <?php esc_html_e('Delete', 'yoapy-social-poster'); ?></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Cards para Mobile (Ainda renderizado via JS para simplicidade) -->
            <div class="md:hidden space-y-3" id="ysp_tasks_cards"></div>

            <!-- Placeholder para quando não houver tarefas -->
            <div id="ysp-no-tasks-placeholder" class="py-12 text-center text-slate-500" <?php if (!empty($tasks)) echo 'style="display: none;"'; ?>>
                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                <h3 class="mt-2 text-sm font-semibold text-slate-800"><?php esc_html_e( 'No tasks yet', 'yoapy-social-poster' ); ?></h3>
                <p class="mt-1 text-sm text-slate-500"><?php esc_html_e( 'Create your first publication using the form above.', 'yoapy-social-poster' ); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Meta para Upload e Scripts -->
<div id="ysp_media_meta" data-upload-url="<?php echo esc_url( admin_url('async-upload.php') ); ?>" data-media-nonce="<?php echo esc_attr( wp_create_nonce('media-form') ); ?>" class="hidden"></div>
<?php
include 'styles-preview.php';
include 'styles-social-preview.php';
include 'styles-animations.php';
include 'styles-utilities.php';
include 'script-preview.php';
include 'script-media-upload.php';
include 'script-dragdrop.php';
?>
<!-- Estilos para a nova lista de tarefas -->
<style>
    .ysp-net-chip { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 500; border: 1px solid; text-transform: capitalize; }
    .ysp-net--facebook { background-color: #eef2ff; color: #3b82f6; border-color: #c7d2fe; }
    .ysp-net--instagram { background-color: #fdf2f8; color: #e11d48; border-color: #fbcfe8; }
    .ysp-net--youtube { background-color: #fee2e2; color: #ef4444; border-color: #fecaca; }
    .ysp-net--tiktok { background-color: #f1f5f9; color: #1e293b; border-color: #e2e8f0; }

    .ysp-status-badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 500; text-transform: capitalize; }
    .ysp-status--pending { background-color: #f1f5f9; color: #475569; }
    .ysp-status--processing { background-color: #fffbeb; color: #b45309; }
    .ysp-status--complete { background-color: #f0fdf4; color: #16a34a; }
    .ysp-status--error { background-color: #fef2f2; color: #dc2626; }

    .ysp-result-link { display: inline-flex; align-items: center; gap: 4px; color: #0ea5e9; text-decoration: none; padding: 2px 6px; border-radius: 4px; background-color: #f0f9ff; transition: background-color .2s; }
    .ysp-result-link:before { content: '🔗'; }
    .ysp-result-link:hover { background-color: #e0f2fe; text-decoration: none; }

    .ysp-result-error { display: inline-flex; align-items: center; gap: 4px; color: #ef4444; cursor: help; padding: 2px 6px; border-radius: 4px; background-color: #fef2f2; }
    .ysp-result-error:before { content: '⚠️'; }
</style>
