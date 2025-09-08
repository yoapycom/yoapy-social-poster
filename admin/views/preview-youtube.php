<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// YouTube Preview Template - REBUILT for Player View
?>
<div id="youtube_preview" class="preview-container hidden bg-black">
    <div class="youtube-player">
        <!-- Camada da Mídia -->
        <div class="youtube-media-layer">
            <img id="youtube_prev_img" class="youtube-image hidden" alt="YouTube Preview">
            <video id="youtube_prev_vid" class="youtube-video hidden" playsinline muted loop></video>
        </div>

        <!-- Gradientes para legibilidade -->
        <div class="youtube-gradient-top"></div>
        <div class="youtube-gradient-bottom"></div>

        <!-- Camada da Interface do Usuário -->
        <div class="youtube-ui-overlay">
            <!-- Barra Superior com Título -->
            <div class="youtube-top-bar">
                <h3 class="youtube-title" id="youtube_text_content"><?php echo esc_html__('Your video title here...', 'yoapy-social-poster'); ?></h3>
            </div>

            <!-- Ícone de Play Central -->
            <div class="youtube-center-play">
                <svg viewBox="0 0 24 24" fill="currentColor" width="60%" height="60%"><path d="M8 5v14l11-7z"></path></svg>
            </div>

            <!-- Barra Inferior com Informações do Canal -->
            <div class="youtube-bottom-bar">
                <div class="youtube-channel-info-overlay">
                    <div class="youtube-avatar"></div>
                    <div>
                        <div class="youtube-channel-name" id="youtube_username"><?php echo esc_html__('Your Channel', 'yoapy-social-poster'); ?></div>
                        <div class="youtube-subscribers"><?php echo esc_html__('1.2K subscribers', 'yoapy-social-poster'); ?></div>
                    </div>
                </div>
                <div class="youtube-actions-overlay">
                    <button class="youtube-subscribe"><?php echo esc_html__('SUBSCRIBE', 'yoapy-social-poster'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
