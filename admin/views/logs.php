<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<?php
// ---------- helpers locais ----------
function yoapsopo_try_json($raw){ if(is_array($raw)) return $raw; $j=json_decode(is_string($raw)?$raw:'',true); return is_array($j)?$j:null; }
function yoapsopo_fmt_br($utcStr){
    // Convert "Y-m-d H:i:s" UTC to site timezone using WordPress date format
    try{
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $utcStr, new DateTimeZone('UTC'));
        if(!$dt) return esc_html($utcStr);
        $dt->setTimezone( wp_timezone() );
        // Use WordPress date and time format settings
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        return esc_html( $dt->format($date_format . ' ' . $time_format) );
    }catch(Exception $e){ return esc_html($utcStr); }
}
function yoapsopo_status_chip($status){
    $map = array('complete'=>'success','processing'=>'warn','scheduled'=>'info','pending'=>'muted','error'=>'error');
    $kind = $map[$status] ?? 'muted';
    $icon = array(
        'success'=>'<span class="dashicons dashicons-yes-alt"></span>',
        'warn'=>'<span class="dashicons dashicons-update"></span>',
        'info'=>'<span class="dashicons dashicons-info"></span>',
        'muted'=>'<span class="dashicons dashicons-minus"></span>',
        'error'=>'<span class="dashicons dashicons-dismiss"></span>',
    )[$kind];
    return '<span class="yoapsopo-chip yoapsopo-chip--'.$kind.'">'.$icon.' '.esc_html($status).'</span>';
}
function yoapsopo_net_svg($net){
    // SVGs simples inline (cores controladas por CSS da rede)
    $svg = array(
        'facebook'=>'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7h-2.3V12h2.3V9.8c0-2.3 1.4-3.6 3.5-3.6 1 0 2 .2 2 .2v2.2h-1.1c-1.1 0-1.5.7-1.5 1.4V12h2.6l-.4 2.9h-2.2v7A10 10 0 0 0 22 12"/></svg>',
        'instagram'=>'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm5 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm6.5-.9a1.1 1.1 0 1 0 0 2.2 1.1 1.1 0 0 0 0-2.2z"/></svg>',
        'youtube'=>'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M23 7.5s-.2-1.6-.8-2.3c-.8-.8-1.7-.8-2.2-.9C16.9 4 12 4 12 4h0s-4.9 0-8 .3c-.5.1-1.4.1-2.2.9C1.2 5.9 1 7.5 1 7.5S.8 9.3.8 11.1v1.8c0 1.8.2 3.6.2 3.6s.2 1.6.8 2.3c.8.8 1.9.8 2.4.9C6.1 20 12 20 12 20s4.9 0 8-.3c.5-.1 1.4-.1 2.2-.9.6-.7.8-2.3.8-2.3s.2-1.8.2-3.6v-1.8c0-1.8-.2-3.6-.2-3.6zM9.8 15.3V8.7l6.1 3.3-6.1 3.3z"/></svg>',
        'tiktok'=>'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M20 8.9a7.3 7.3 0 0 1-4.3-1.4v7.3a5.8 5.8 0 1 1-5.8-5.8c.5 0 .9 0 1.3.1v2.5a2.9 2.9 0 1 0 2.9 2.9V2h2.7a4.6 4.6 0 0 0 4.3 4.1v2.8z"/></svg>',
    );
    return $svg[$net] ?? '';
}
function yoapsopo_net_chip($net){
    $label = ucfirst($net);
    return '<span class="yoapsopo-net yoapsopo-net--'.$net.'">'.yoapsopo_net_svg($net).' <span>'.$label.'</span></span>';
}
function yoapsopo_links_from_result($body){
    $links = array();
    if (isset($body['data']) && is_array($body['data'])) {
        foreach ($body['data'] as $net=>$info) {
            if (is_array($info)) {
                if (!empty($info['permalink'])) $links[$net] = $info['permalink'];
                elseif (!empty($info['url']))    $links[$net] = $info['url'];
            }
        }
    }
    foreach (array('permalink','url') as $k) {
        if (!empty($body[$k]) && empty($links['post'])) $links['post'] = $body[$k];
    }
    return $links;
}
?>


<div class="wrap">
    <!-- header com degradê -->
    <div class="rounded-xl mb-4 p-5 text-white" style="background:linear-gradient(135deg,#0ea5e9,#7c3aed);">
        <h1 class="!text-white !m-0 text-2xl font-semibold"><?php esc_html_e( 'YoApy Logs', 'yoapy-social-poster' ); ?></h1>
        <p class="opacity-90 mt-1"><?php esc_html_e( 'Send and API return events. Dates in your timezone using WordPress format.', 'yoapy-social-poster' ); ?></p>
    </div>

    <form method="post" class="mb-4 flex items-center gap-2">
        <?php wp_nonce_field('yoapsopo_clear_logs'); ?>
        <input type="hidden" name="yoapsopo_clear_logs" value="1"/>
        <button class="button"><?php esc_html_e( 'Clear all', 'yoapy-social-poster' ); ?></button>
        <span class="text-slate-600"><?php
            /* translators: %s: the text "view details" wrapped in <em> tags. */
            printf( esc_html__( 'Tip: click %s to open the complete JSON.', 'yoapy-social-poster' ), '<em>' . esc_html__( 'view details', 'yoapy-social-poster' ) . '</em>' ); ?></span>
    </form>

    <div class="overflow-x-auto">
        <?php
        // --- Pré-processa os logs em $items para renderizar uma vez em tabela (md+) e cards (mobile) ---
        $items = array();
        $shown = array('req_create_post_json','res_create_post_json','res_get_task_result','do_job_end');

        foreach ( YOAPSOPO_Logger::get_lines(1000) as $r ):
            $evt = $r['event'] ?? '';
            if ( ! in_array($evt, $shown, true) ) continue;

            $data    = $r['data'] ?? array();
            $title   = '';
            $summary = '';
            $details = '';

            if ($evt==='req_create_post_json'){
                $title = __( 'Send', 'yoapy-social-poster' ) . ' → POST '.esc_html($data['endpoint'] ?? '/v1/posts');
                $json  = $data['json'] ?? yoapsopo_try_json($data['body_raw'] ?? '');
                $nets  = (isset($json['account_ids']) && is_array($json['account_ids'])) ? $json['account_ids'] : array();
                $type  = $json['post_type'] ?? '—';
                $text  = isset($json['text']) ? wp_strip_all_tags($json['text']) : '';
                if (mb_strlen($text)>140) $text = mb_substr($text,0,140).'…';
                $scheduled = !empty($json['scheduled_time']) ? '<span class="ml-2 text-slate-600">• ' . esc_html__( 'scheduled:', 'yoapy-social-poster' ) . ' '.esc_html($json['scheduled_time']).'</span>' : '';

                $netsHtml = '';
                foreach ($nets as $n) $netsHtml .= yoapsopo_net_chip($n).' ';

                $summary = '<div class="flex flex-wrap items-center gap-2">'.
                    '<span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">'.$type.'</span>'.
                    $netsHtml.$scheduled.
                    '</div>'.
                    ($text ? '<div class="mt-2 text-sm text-slate-700">“'.esc_html($text).'”</div>' : '');

                $details = '<pre class="text-xs leading-5 whitespace-pre-wrap break-words">'.esc_html(json_encode($json ?: $data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)).'</pre>';
            }

            if ($evt==='res_create_post_json'){
                $title  = __( 'Send return', 'yoapy-social-poster' );
                $body   = yoapsopo_try_json($data['response_body'] ?? '');
                $code   = intval($data['http_code'] ?? 0);
                $taskId = is_array($body) && !empty($body['task_id']) ? $body['task_id'] : '—';

                $summary = '<div class="flex flex-wrap items-center gap-2">'.
                    '<span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">HTTP '.$code.'</span>'.
                    '<span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-0.5 text-xs text-slate-500">task_id: '.$taskId.'</span>'.
                    '</div>';

                $details = '<pre class="text-xs leading-5 whitespace-pre-wrap break-words">'.esc_html(json_encode($body ?: $data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)).'</pre>';
            }

            if ($evt==='res_get_task_result'){
                $title  = __( 'Task status', 'yoapy-social-poster' );
                $body   = yoapsopo_try_json($data['response_body'] ?? '');
                $status = is_array($body) && !empty($body['status']) ? $body['status'] : '—';

                $summary = yoapsopo_status_chip($status);
                $links = yoapsopo_links_from_result($body);
                if ($links) {
                    $summary .= '<div class="mt-2 flex flex-wrap gap-2">';
                    foreach ($links as $net=>$url) {
                        $summary .= '<a class="inline-flex items-center gap-2 rounded-md px-2.5 py-1 text-xs text-white no-underline yoapsopo-net yoapsopo-net--'.esc_attr($net).'" target="_blank" rel="noopener" href="'.esc_url($url).'">'.yoapsopo_net_svg($net).' <span>' . sprintf(
                            /* translators: %s: social network name (e.g., Facebook, Instagram, YouTube, TikTok). */
                                esc_html__( 'View on %s', 'yoapy-social-poster' ), esc_html($net) ) . '</span></a> ';
                    }
                    $summary .= '</div>';
                }

                $details = '<pre class="text-xs leading-5 whitespace-pre-wrap break-words">'.esc_html(json_encode($body ?: $data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)).'</pre>';
            }

            if ($evt==='do_job_end'){
                $title   = __( 'Job completion', 'yoapy-social-poster' );
                $status  = $data['status'] ?? '—';
                $summary = yoapsopo_status_chip($status);
                $details = '<pre class="text-xs leading-5 whitespace-pre-wrap break-words">'.esc_html(json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)).'</pre>';
            }

            $items[] = array(
                'idx'     => intval($r['_i']),
                'when_br' => yoapsopo_fmt_br($r['t']),
                'title'   => $title ?: $evt,
                'summary' => $summary,
                'details' => $details,
                'evt'     => $evt,
            );
        endforeach;

        // allowed HTML para imprimir summary/details com ícones SVG
        $yoapsopo_allowed_html = array(
            'div'  => array( 'class' => true ),
            'span' => array( 'class' => true ),
            'a'    => array( 'class' => true, 'href' => true, 'target' => true, 'rel' => true ),
            'pre'  => array( 'class' => true ),
            'code' => array( 'class' => true ),
            'svg'  => array(
                'viewBox' => true, 'width' => true, 'height' => true, 'fill' => true,
                'xmlns' => true, 'role' => true, 'aria-hidden' => true, 'focusable' => true
            ),
            'path' => array(
                'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true,
                'stroke-linecap' => true, 'stroke-linejoin' => true
            ),
        );
        ?>

        <!-- ===== MOBILE (cards) ===== -->
        <ul class="md:hidden space-y-3">
            <?php foreach ($items as $it): ?>
                <li class="rounded-xl border border-slate-200 bg-white shadow-sm p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs text-slate-500"><?php echo esc_html($it['when_br']); ?></div>
                            <div class="mt-1 font-mono text-[13px] bg-slate-100 text-slate-700 inline-block rounded px-2 py-0.5"><?php echo esc_html($it['title']); ?></div>
                        </div>
                        <form method="post" class="shrink-0">
                            <?php wp_nonce_field('yoapsopo_delete_log'); ?>
                            <input type="hidden" name="line" value="<?php echo esc_attr( $it['idx'] ); ?>"/>
                            <button class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-rose-600 hover:bg-rose-50" name="yoapsopo_delete_log" value="1" title="<?php esc_attr_e( 'Delete', 'yoapy-social-poster' ); ?>">
                                <span class="dashicons dashicons-trash"></span> <?php esc_html_e( 'Delete', 'yoapy-social-poster' ); ?>
                            </button>
                        </form>
                    </div>

                    <div class="mt-3 text-sm text-slate-700 space-y-2">
                        <div class="yoapsopo-summary"><?php echo wp_kses( $it['summary'], $yoapsopo_allowed_html ); ?></div>
                        <button type="button" class="yoapsopo-toggle inline-flex items-center gap-1 text-sky-600 hover:text-sky-700">
                            <span class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'view details', 'yoapy-social-poster' ); ?>
                        </button>
                        <div class="yoapsopo-details hidden mt-2 rounded-md bg-slate-50 p-3 overflow-x-auto"><?php echo wp_kses( $it['details'], $yoapsopo_allowed_html ); ?></div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- ===== DESKTOP/TABLET (tabela) ===== -->
        <div class="hidden md:block overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm" id="yoapsopoLogTable">
                    <thead class="bg-slate-50 sticky top-0 z-10">
                    <tr>
                        <th class="w-56 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600"><?php esc_html_e( 'When (local)', 'yoapy-social-poster' ); ?></th>
                        <th class="w-72 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600"><?php esc_html_e( 'Action', 'yoapy-social-poster' ); ?></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600"><?php esc_html_e( 'Summary', 'yoapy-social-poster' ); ?></th>
                        <th class="w-28 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600"><?php esc_html_e( 'Delete', 'yoapy-social-poster' ); ?></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php foreach ($items as $it): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="align-top px-4 py-3 text-slate-600"><?php echo esc_html($it['when_br']); ?></td>
                            <td class="align-top px-4 py-3">
                                <code class="inline-flex items-center rounded-lg bg-slate-100 px-2 py-1 text-[12px] text-slate-700"><?php echo esc_html($it['title']); ?></code>
                            </td>
                            <td class="align-top px-4 py-3">
                                <div class="yoapsopo-summary prose-sm max-w-none">
                                    <?php echo wp_kses( $it['summary'], $yoapsopo_allowed_html ); ?>
                                </div>
                                <button type="button" class="yoapsopo-toggle mt-2 inline-flex items-center gap-1 rounded-md px-2 py-1 text-sky-600 hover:bg-sky-50">
                                    <span class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'view details', 'yoapy-social-poster' ); ?>
                                </button>
                                <div class="yoapsopo-details hidden mt-2 rounded-md bg-slate-50 p-3 overflow-x-auto">
              <?php echo wp_kses( $it['details'], $yoapsopo_allowed_html ); ?>
                                </div>
                            </td>
                            <td class="align-top px-4 py-3">
                                <form method="post">
                                    <?php wp_nonce_field('yoapsopo_delete_log'); ?>
                                    <input type="hidden" name="line" value="<?php echo esc_attr( $it['idx'] ); ?>"/>
                                    <button class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-rose-600 hover:bg-rose-50" name="yoapsopo_delete_log" value="1">
                                        <span class="dashicons dashicons-trash"></span> <?php esc_html_e( 'Delete', 'yoapy-social-poster' ); ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
