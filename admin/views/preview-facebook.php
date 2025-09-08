<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Facebook Feed Preview Template - REBUILT
?>
<div id="facebook_preview" class="preview-container hidden bg-white">
    <div class="facebook-post">
        <!-- Cabeçalho -->
        <div class="fb-header">
            <div class="fb-avatar"></div>
            <div class="fb-info">
                <div class="fb-name" id="fb_display_name"><?php echo esc_html__('Your Page', 'yoapy-social-poster'); ?></div>
                <div class="fb-time"><?php echo esc_html__('2 min • 🌎', 'yoapy-social-poster'); ?></div>
            </div>
            <div class="fb-menu">···</div>
        </div>

        <!-- Texto -->
        <div class="fb-text" id="fb_text_content"></div>

        <!-- Mídia (ocupa o espaço restante) -->
        <div class="fb-media">
            <img id="fb_prev_img" class="fb-image hidden" alt="Facebook Preview">
            <video id="fb_prev_vid" class="fb-video hidden" playsinline muted loop></video>
        </div>

        <!-- Ações -->
        <div class="fb-actions">
            <div class="fb-stats">
                <span><?php echo esc_html__('👍❤️ 1.2K', 'yoapy-social-poster'); ?></span>
                <span><?php echo esc_html__('15 comments · 23 shares', 'yoapy-social-poster'); ?></span>
            </div>
            <div class="fb-buttons">
                <div class="fb-btn">👍 <?php echo esc_html__('Like', 'yoapy-social-poster'); ?></div>
                <div class="fb-btn">💬 <?php echo esc_html__('Comment', 'yoapy-social-poster'); ?></div>
                <div class="fb-btn">↪️ <?php echo esc_html__('Share', 'yoapy-social-poster'); ?></div>
            </div>
        </div>
    </div>
</div>
