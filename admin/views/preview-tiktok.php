<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// TikTok Preview Template
?>
<div id="tiktok_preview" class="preview-container hidden bg-black">
  <div class="tiktok-container">
    <!-- TikTok Header -->
    <div class="tiktok-header">
      <div class="tiktok-live">Live</div>
      <div class="tiktok-following">Following</div>
      <div class="tiktok-foryou active">For You</div>
      <div class="tiktok-search">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/>
          <path d="M21 21l-4.35-4.35"/>
        </svg>
      </div>
    </div>
    
    <!-- TikTok Media -->
    <div class="tiktok-media">
      <img id="tiktok_prev_img" class="tiktok-image hidden" alt="">
      <video id="tiktok_prev_vid" class="tiktok-video hidden" playsinline muted loop></video>
    </div>
    
    <!-- TikTok Right Actions -->
    <div class="tiktok-right-actions">
      <div class="tiktok-avatar">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
      </div>
      <button class="tiktok-action">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <div class="action-count">1.2K</div>
      </button>
      <button class="tiktok-action">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <div class="action-count">89</div>
      </button>
      <button class="tiktok-action">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
          <path d="M16 6l-4-4-4 4"/>
          <path d="M12 2v13"/>
        </svg>
        <div class="action-count"><?php echo esc_html__('Share', 'yoapy-social-poster'); ?></div>
      </button>
      <button class="tiktok-action">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
        </svg>
      </button>
      <div class="tiktok-music">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M9 18V5l12-2v13M9 9l12-2"/>
          <circle cx="6" cy="18" r="3"/>
          <circle cx="18" cy="16" r="3"/>
        </svg>
      </div>
    </div>
    
    <!-- TikTok Bottom Info -->
    <div class="tiktok-bottom">
      <div class="tiktok-user">
        <span class="tiktok-username" id="tiktok_username">@yourusername</span>
      </div>
      <div class="tiktok-caption" id="tiktok_text_content"></div>
      <div class="tiktok-sound">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 18V5l12-2v13"/>
          <circle cx="6" cy="18" r="3"/>
          <circle cx="18" cy="16" r="3"/>
        </svg>
        <?php echo esc_html__('original sound - yourusername', 'yoapy-social-poster'); ?>
      </div>
    </div>
  </div>
</div>
