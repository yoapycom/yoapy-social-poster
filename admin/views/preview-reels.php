<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Instagram Reels Preview Template
?>
<div id="reels_preview" class="preview-container hidden bg-black">
  <div class="reels-container">
    <!-- Reels Header -->
    <div class="reels-header">
      <div class="reels-logo">Reels</div>
      <div class="reels-camera">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
          <circle cx="12" cy="13" r="4"/>
        </svg>
      </div>
    </div>
    
    <!-- Reels Media -->
    <div class="reels-media">
      <img id="reels_prev_img" class="reels-image hidden" alt="">
      <video id="reels_prev_vid" class="reels-video hidden" playsinline muted loop></video>
    </div>
    
    <!-- Reels Right Actions -->
    <div class="reels-right-actions">
      <div class="reels-avatar"></div>
      <button class="reels-action">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <div class="action-count">1.2K</div>
      </button>
      <button class="reels-action">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <div class="action-count">89</div>
      </button>
      <button class="reels-action">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
          <path d="M16 6l-4-4-4 4"/>
          <path d="M12 2v13"/>
        </svg>
        <div class="action-count"><?php echo esc_html__('Share', 'yoapy-social-poster'); ?></div>
      </button>
      <button class="reels-action">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="1"/>
          <circle cx="19" cy="12" r="1"/>
          <circle cx="5" cy="12" r="1"/>
        </svg>
      </button>
    </div>
    
    <!-- Reels Bottom Info -->
    <div class="reels-bottom">
      <div class="reels-user">
        <span class="reels-username" id="reels_username">yourusername</span> •
        <button class="reels-follow"><?php echo esc_html__('Follow', 'yoapy-social-poster'); ?></button>
      </div>
      <div class="reels-caption" id="reels_text_content"></div>
      <div class="reels-sound">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 18V5l12-2v13"/>
          <circle cx="6" cy="18" r="3"/>
          <circle cx="18" cy="16" r="3"/>
        </svg>
        <?php echo esc_html__('Original audio', 'yoapy-social-poster'); ?>
      </div>
    </div>
  </div>
</div>
