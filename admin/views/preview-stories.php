<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Instagram Stories Preview Template
?>
<div id="stories_preview" class="preview-container hidden bg-black">
  <div class="story-container">
    <!-- Story Progress -->
    <div class="story-progress">
      <div class="progress-bar active"></div>
      <div class="progress-bar"></div>
      <div class="progress-bar"></div>
    </div>
    
    <!-- Story Header -->
    <div class="story-header">
      <div class="story-avatar"></div>
      <div class="story-username" id="story_username">yourusername</div>
      <div class="story-time">2m</div>
      <div class="story-close">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </div>
    </div>
    
    <!-- Story Media -->
    <div class="story-media">
      <img id="story_prev_img" class="story-image hidden" alt="">
      <video id="story_prev_vid" class="story-video hidden" playsinline muted loop></video>
    </div>
    
    <!-- Story Text -->
    <div class="story-text" id="story_text_content"></div>
    
    <!-- Story Actions -->
    <div class="story-actions">
      <input class="story-reply" placeholder="Send message">
      <button class="story-heart">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      </button>
      <button class="story-share">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
          <path d="M16 6l-4-4-4 4"/>
          <path d="M12 2v13"/>
        </svg>
      </button>
    </div>
  </div>
</div>