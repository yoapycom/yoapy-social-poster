<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
$opt = get_option('ysp_settings', array());
$hasKeys = !empty($opt['key_id']) && !empty($opt['secret']);

$acc_default = trim($opt['account'] ?? '');
$netHandles = array(
    'facebook'  => trim($opt['account_facebook']  ?? ''),
    'instagram' => trim($opt['account_instagram'] ?? ''),
    'youtube'   => trim($opt['account_youtube']   ?? ''),
    'tiktok'    => trim($opt['account_tiktok']    ?? ''),
);
$resolved = array(
    'facebook'  => $netHandles['facebook']  !== '' ? $netHandles['facebook']  : $acc_default,
    'instagram' => $netHandles['instagram'] !== '' ? $netHandles['instagram'] : $acc_default,
    'youtube'   => $netHandles['youtube']   !== '' ? $netHandles['youtube']   : $acc_default,
    'tiktok'    => $netHandles['tiktok']    !== '' ? $netHandles['tiktok']    : $acc_default,
);
?>


<div class="wrap ysp-wrap !max-w-[1120px]">
    <!-- Header gradient -->
    <div class="rounded-2xl mb-6 p-5 md:p-6 text-white flex items-center justify-between gap-4" style="background:linear-gradient(135deg,#0ea5e9,#7c3aed)">
        <div class="min-w-0">
            <h1 class="!m-0 text-2xl md:text-3xl font-semibold"><?php esc_html_e( 'YoApy Settings', 'yoapy-social-poster' ); ?></h1>
            <p class="opacity-90 mt-1 text-sm md:text-base"><?php esc_html_e( 'Connect your keys, set accounts by network and test the integration.', 'yoapy-social-poster' ); ?></p>
        </div>
        <span class="shrink-0 inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-medium border
      <?php echo $hasKeys
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-amber-50 text-amber-700 border-amber-200'; ?>">
      <span class="dashicons <?php echo $hasKeys ? 'dashicons-yes' : 'dashicons-warning'; ?>"></span>
      <?php echo $hasKeys ? esc_html__( 'Keys configured', 'yoapy-social-poster' ) : esc_html__( 'Keys pending', 'yoapy-social-poster' ); ?>
    </span>
    </div>

    <!-- CTA keys -->
    <div class="flex items-start md:items-center justify-between gap-4 flex-wrap mb-6">
        <div class="flex items-center gap-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3">
            <div class="text-lg">🔑</div>
            <p class="m-0 text-sm md:text-base">
                <?php
                /* translators: %s: link to the YoApy website (HTML <a> tag). */
                printf( esc_html__( 'Create your keys for free at %s.', 'yoapy-social-poster' ), '<a class="font-semibold text-sky-700 hover:underline" href="https://yoapy.com" target="_blank" rel="noopener">yoapy.com</a>' );
                ?>
            </p>
        </div>
        <a class="button" href="https://yoapy.com" target="_blank" rel="noopener">
            <span class="dashicons dashicons-external" style="vertical-align:middle"></span> <?php esc_html_e( 'Create Keys', 'yoapy-social-poster' ); ?>
        </a>
    </div>

    <!-- GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna principal (2 colunas) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card: Credenciais -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="dashicons dashicons-lock"></span>
                    <h2 class="m-0 text-lg font-semibold"><?php esc_html_e( 'YoApy Credentials', 'yoapy-social-poster' ); ?></h2>
                </div>
                <div class="px-5 py-5">
                    <form method="post" class="space-y-5">
                        <?php wp_nonce_field('ysp_save_settings','ysp_nonce_save'); ?>
                        <!-- Form identifier for credentials -->
                        <input type="hidden" name="ysp_save_settings" value="1"/>
                        <input type="hidden" name="form_type" value="credentials"/>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1"><?php esc_html_e( 'Base URL', 'yoapy-social-poster' ); ?></label>
                            <input type="url" name="base_url"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2"
                                   value="<?php echo esc_attr($opt['base_url'] ?? 'https://api.yoapy.com'); ?>">
                            <p class="text-xs text-slate-500 mt-1">
                                <?php
                                /* translators: %s: example base URL wrapped in <code> tags. */
                                printf( esc_html__( 'Normally: %s', 'yoapy-social-poster' ), '<code>https://api.yoapy.com</code>' );
                                ?>
                            </p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1"><?php esc_html_e( 'Key ID', 'yoapy-social-poster' ); ?></label>
                                <div class="flex items-stretch gap-2">
                                    <input type="text" id="ysp_key_id" name="key_id"
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2"
                                           value="<?php echo esc_attr($opt['key_id'] ?? ''); ?>">
                                    <button type="button" class="button" data-clip="#ysp_key_id">
                                        <span class="dashicons dashicons-admin-page" style="vertical-align:middle"></span> <?php esc_html_e( 'Copy', 'yoapy-social-poster' ); ?>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1"><?php esc_html_e( 'Secret (hex)', 'yoapy-social-poster' ); ?></label>
                                <div class="flex items-stretch gap-2">
                                    <input type="password" id="ysp_secret" name="secret"
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2"
                                           value="<?php echo esc_attr($opt['secret'] ?? ''); ?>">
                                    <button type="button" class="button" id="ysp_secret_toggle">
                                        <span class="dashicons dashicons-visibility" style="vertical-align:middle"></span> <?php esc_html_e( 'Show', 'yoapy-social-poster' ); ?>
                                    </button>
                                    <button type="button" class="button" data-clip="#ysp_secret">
                                        <span class="dashicons dashicons-admin-page" style="vertical-align:middle"></span> <?php esc_html_e( 'Copy', 'yoapy-social-poster' ); ?>
                                    </button>
                                </div>
                                <p class="text-xs text-slate-500 mt-1"><?php esc_html_e( 'Hexadecimal secret — do not share.', 'yoapy-social-poster' ); ?></p>
                            </div>
                        </div>

                        <div class="pt-1">
                            <button type="submit" class="button button-primary">
                                <span class="dashicons dashicons-yes-alt" style="vertical-align:middle"></span> <?php esc_html_e( 'Save', 'yoapy-social-poster' ); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card: Contas (@ por rede) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="dashicons dashicons-admin-users"></span>
                    <h2 class="m-0 text-lg font-semibold"><?php esc_html_e( 'Accounts (@) by network', 'yoapy-social-poster' ); ?></h2>
                </div>
                <div class="px-5 py-5">
                    <form method="post" class="space-y-5">
                        <?php wp_nonce_field('ysp_save_settings','ysp_nonce_save'); ?>
                        <!-- Form identifier for accounts -->
                        <input type="hidden" name="ysp_save_settings" value="1"/>
                        <input type="hidden" name="form_type" value="accounts"/>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1"><?php esc_html_e( 'Default account (@)', 'yoapy-social-poster' ); ?></label>
                            <input type="text" name="account" placeholder="<?php esc_attr_e( '@yourusername', 'yoapy-social-poster' ); ?>"
                                   class="w-full rounded-xl border border-slate-300 px-3 py-2"
                                   value="<?php echo esc_attr($opt['account'] ?? ''); ?>">
                            <p class="text-xs text-slate-500 mt-1"><?php esc_html_e( 'Used when the network-specific @ is empty. (The @ symbol will be automatically removed)', 'yoapy-social-poster' ); ?></p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                  <span class="inline-flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 inline-block"></span> Facebook @
                  </span>
                                </label>
                                <input type="text" name="account_facebook" placeholder="<?php esc_attr_e( '@yourpage', 'yoapy-social-poster' ); ?>"
                                       class="w-full rounded-xl border border-slate-300 px-3 py-2"
                                       value="<?php echo esc_attr($opt['account_facebook'] ?? ''); ?>">
                                <p class="text-xs text-slate-500 mt-1"><?php esc_html_e( 'The @ symbol will be automatically removed', 'yoapy-social-poster' ); ?></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                  <span class="inline-flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:linear-gradient(45deg,#f59e0b,#ec4899,#8b5cf6)"></span> Instagram @
                  </span>
                                </label>
                                <input type="text" name="account_instagram" placeholder="<?php esc_attr_e( '@yourinsta', 'yoapy-social-poster' ); ?>"
                                       class="w-full rounded-xl border border-slate-300 px-3 py-2"
                                       value="<?php echo esc_attr($opt['account_instagram'] ?? ''); ?>">
                                <p class="text-xs text-slate-500 mt-1"><?php esc_html_e( 'The @ symbol will be automatically removed', 'yoapy-social-poster' ); ?></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                  <span class="inline-flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600 inline-block"></span> YouTube @
                  </span>
                                </label>
                                <input type="text" name="account_youtube" placeholder="<?php esc_attr_e( '@yourchannel', 'yoapy-social-poster' ); ?>"
                                       class="w-full rounded-xl border border-slate-300 px-3 py-2"
                                       value="<?php echo esc_attr($opt['account_youtube'] ?? ''); ?>">
                                <p class="text-xs text-slate-500 mt-1"><?php esc_html_e( 'The @ symbol will be automatically removed', 'yoapy-social-poster' ); ?></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                  <span class="inline-flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:linear-gradient(45deg,#06b6d4,#000,#f43f5e)"></span> TikTok @
                  </span>
                                </label>
                                <input type="text" name="account_tiktok" placeholder="<?php esc_attr_e( '@yourtiktok', 'yoapy-social-poster' ); ?>"
                                       class="w-full rounded-xl border border-slate-300 px-3 py-2"
                                       value="<?php echo esc_attr($opt['account_tiktok'] ?? ''); ?>">
                                <p class="text-xs text-slate-500 mt-1"><?php esc_html_e( 'The @ symbol will be automatically removed', 'yoapy-social-poster' ); ?></p>
                            </div>
                        </div>

                        <!-- Prévia de resolução por rede -->
                        <div class="mt-2">
                            <div class="text-sm font-medium text-slate-700 mb-2"><?php esc_html_e( 'Usage preview by network', 'yoapy-social-poster' ); ?></div>
                            <div class="flex flex-wrap gap-2">
                                <?php
                                foreach (['facebook'=>'Facebook','instagram'=>'Instagram','youtube'=>'YouTube','tiktok'=>'TikTok'] as $k=>$label):
                                    $h = $resolved[$k];
                                    $isInherited = ($netHandles[$k]==='' && $acc_default!=='');
                                    $badgeClass = $k==='facebook' ? 'bg-blue-50 text-blue-800 border-blue-200'
                                        : ($k==='instagram' ? 'bg-pink-50 text-pink-800 border-pink-200'
                                            : ($k==='youtube' ? 'bg-red-50 text-red-800 border-red-200'
                                                : 'bg-slate-50 text-slate-800 border-slate-200'));
                                    ?>
                                    <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-sm <?php echo esc_attr( $badgeClass ); ?>">
                    <strong><?php echo esc_html($label); ?></strong>
                    <span class="opacity-70"><?php echo $h ? esc_html($h) : '—'; ?></span>
                    <?php if($isInherited): ?>
                        <span class="text-xs opacity-60"><?php esc_html_e( '(default)', 'yoapy-social-poster' ); ?></span>
                    <?php endif; ?>
                  </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="pt-1">
                            <button type="submit" class="button button-primary">
                                <span class="dashicons dashicons-yes-alt" style="vertical-align:middle"></span> <?php esc_html_e( 'Save', 'yoapy-social-poster' ); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Card: Teste -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="dashicons dashicons-rss"></span>
                    <h3 class="m-0 text-base font-semibold"><?php esc_html_e( 'Connection test', 'yoapy-social-poster' ); ?></h3>
                </div>
                <div class="px-5 py-5">
                    <form method="post" class="flex items-center gap-3">
                        <?php wp_nonce_field('ysp_ping','ysp_nonce_ping'); ?>
                        <button type="submit" class="button" name="ysp_ping" value="1">
                            <span class="dashicons dashicons-controls-repeat" style="vertical-align:middle"></span> <?php esc_html_e( 'Test connection', 'yoapy-social-poster' ); ?>
                        </button>
                        <?php if ( !$hasKeys ): ?>
                            <span class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2 py-1"><?php esc_html_e( 'add keys first', 'yoapy-social-poster' ); ?></span>
                        <?php endif; ?>
                    </form>
                    <p class="text-xs text-slate-500 mt-3"><?php esc_html_e( 'The result appears in the notices on this page.', 'yoapy-social-poster' ); ?></p>
                </div>
            </div>

            <!-- Card: Ajuda -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="dashicons dashicons-editor-help"></span>
                    <h3 class="m-0 text-base font-semibold"><?php esc_html_e( 'Quick help', 'yoapy-social-poster' ); ?></h3>
                </div>
                <div class="px-5 py-5 text-sm space-y-2 leading-6">
                    <p>
                        <?php
                        /* translators: %s: link to the YoApy website (HTML <a> tag). */
                        printf( esc_html__( '1. Generate the keys on the %s.', 'yoapy-social-poster' ), '<a class="text-sky-700 hover:underline" href="https://yoapy.com" target="_blank" rel="noopener">' . esc_html__( 'YoApy website', 'yoapy-social-poster' ) . '</a>' );
                        ?>
                    </p>
                    <p>
                        <?php
                        /* translators: 1: 'Key ID' label wrapped in <strong> tags; 2: 'Secret' label wrapped in <strong> tags. */
                        printf( esc_html__( '2. Paste the %1$s and %2$s above.', 'yoapy-social-poster' ), '<strong>' . esc_html__( 'Key ID', 'yoapy-social-poster' ) . '</strong>', '<strong>' . esc_html__( 'Secret', 'yoapy-social-poster' ) . '</strong>' );
                        ?>
                    </p>
                    <p>
                        <?php
                        /* translators: %s: 'default @' label wrapped in <strong> tags. */
                        printf( esc_html__( '3. Set your %s and, if desired, network-specific @.', 'yoapy-social-poster' ), '<strong>' . esc_html__( 'default @', 'yoapy-social-poster' ) . '</strong>' );
                        ?>
                    </p>
                    <p>
                        <?php
                        /* translators: 1: 'Save' text wrapped in <em> tags; 2: 'Test connection' text wrapped in <em> tags. */
                        printf( esc_html__( '4. Click %1$s and then %2$s.', 'yoapy-social-poster' ), '<em>' . esc_html__( 'Save', 'yoapy-social-poster' ) . '</em>', '<em>' . esc_html__( 'Test connection', 'yoapy-social-poster' ) . '</em>' );
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 👇 FIX 2: imprimir TODAS as mensagens e aplicar estilos legíveis -->
    <div class="mt-6 space-y-3">
        <?php settings_errors(); ?>
    </div>
</div>

<!-- Avisos do WP: garantir contraste e herança de cor -->
<style>
    .wrap .notice { border-radius:12px; padding:12px 14px; }
    .wrap .notice p { margin:0; color:inherit !important; }
    .wrap .notice a { color:inherit; text-decoration: underline; }
    .wrap .notice-success { background:#ecfdf5; border-color:#a7f3d0; color:#065f46; }
    .wrap .notice-warning { background:#fffbeb; border-color:#fcd34d; color:#92400e; }
    .wrap .notice-error { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
    .wrap .is-dismissible .notice-dismiss { filter: invert(0); }
</style>

<!-- Scripts pequenos: revelar/copiar + toast -->
<script>
    (function(){
        // Toggle secret
        var btn = document.getElementById('ysp_secret_toggle');
        var inp = document.getElementById('ysp_secret');
        if(btn && inp){
            btn.addEventListener('click', function(){
                var showing = inp.type === 'text';
                inp.type = showing ? 'password' : 'text';
                btn.innerHTML = (showing
                    ? '<span class="dashicons dashicons-visibility"></span> ' + <?php echo wp_json_encode( __( 'Show', 'yoapy-social-poster' ) ); ?>
                    : '<span class="dashicons dashicons-hidden"></span> ' + <?php echo wp_json_encode( __( 'Hide', 'yoapy-social-poster' ) ); ?>);
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
                    toast(<?php echo wp_json_encode( __( 'Copied!', 'yoapy-social-poster' ) ); ?>);
                } catch(e){
                    toast(<?php echo wp_json_encode( __( 'Copy manually.', 'yoapy-social-poster' ) ); ?>);
                }
            });
        });
    })();
</script>
