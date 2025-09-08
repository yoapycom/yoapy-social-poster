<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Instagram Feed Preview Template - REBUILT
?>
<div id="instagram_preview" class="preview-container hidden bg-white">
    <div class="ig-post">
        <!-- Cabeçalho -->
        <div class="ig-header">
            <div class="ig-avatar"></div>
            <div class="ig-username-container">
                <span class="ig-username" id="ig_username">seunome</span>
                <svg class="ig-verified" viewBox="0 0 24 24" fill="#3797f0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg>
            </div>
            <div class="ig-menu">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
            </div>
        </div>

        <!-- Mídia -->
        <div class="ig-media">
            <img id="ig_prev_img" class="ig-image hidden" alt="Instagram Preview">
            <video id="ig_prev_vid" class="ig-video hidden" playsinline muted loop></video>
        </div>

        <!-- Ações e Legenda -->
        <div class="ig-actions">
            <div class="ig-action-buttons">
                <div class="ig-left-actions">
                    <button class="ig-action-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg></button>
                    <button class="ig-action-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></button>
                    <button class="ig-action-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" transform="rotate(20)"><path d="M22 2L11 13"></path><path d="M22 2L15 22l-4-9-9-4 21-7z"></path></svg></button>
                </div>
                <button class="ig-save"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg></button>
            </div>
            <div class="ig-likes"><?php echo esc_html__('Liked by', 'yoapy-social-poster'); ?> <strong><?php echo esc_html__('friend_1', 'yoapy-social-poster'); ?></strong> <?php echo esc_html__('and', 'yoapy-social-poster'); ?> <strong><?php echo esc_html__('1.2K others', 'yoapy-social-poster'); ?></strong></div>
            <div class="ig-caption">
                <strong class="ig-caption-username" id="ig_caption_username">seunome</strong>
                <span id="ig_text_content"></span>
            </div>
            <div class="ig-time"><?php echo esc_html__('2 MINUTES AGO', 'yoapy-social-poster'); ?></div>
        </div>
    </div>
</div>
