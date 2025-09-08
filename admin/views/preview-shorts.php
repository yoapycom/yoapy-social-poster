<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// YouTube Shorts Preview Template
?>
<div id="shorts_preview" class="preview-container hidden bg-black">
    <div class="shorts-container">
        <!-- Shorts Header -->
        <div class="shorts-header">
            <div class="shorts-search">
                <svg fill="none" height="24" viewBox="0 0 24 24" width="24"><path d="M15.5 14H14.71L14.43 13.73C15.41 12.59 16 11.11 16 9.5C16 5.91 13.09 3 9.5 3C5.91 3 3 5.91 3 9.5C3 13.09 5.91 16 9.5 16C11.11 16 12.59 15.41 13.73 14.43L14 14.71V15.5L19 20.49L20.49 19L15.5 14ZM9.5 14C7.01 14 5 11.99 5 9.5C5 7.01 7.01 5 9.5 5C11.99 5 14 7.01 14 9.5C14 11.99 11.99 14 9.5 14Z" fill="white"></path></svg>
            </div>
            <div class="shorts-menu">
                <svg fill="none" height="24" viewBox="0 0 24 24" width="24"><path d="M12 8C13.1 8 14 7.1 14 6C14 4.9 13.1 4 12 4C10.9 4 10 4.9 10 6C10 7.1 10.9 8 12 8ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM12 16C10.9 16 10 16.9 10 18C10 19.1 10.9 20 12 20C13.1 20 14 19.1 14 18C14 16.9 13.1 16 12 16Z" fill="white"></path></svg>
            </div>
        </div>

        <!-- Shorts Media -->
        <div class="shorts-media">
            <img id="shorts_prev_img" class="shorts-image hidden" alt="">
            <video id="shorts_prev_vid" class="shorts-video hidden" playsinline muted loop></video>
        </div>

        <!-- Shorts Right Actions -->
        <div class="shorts-right-actions">
            <div class="shorts-action"><svg fill="none" height="24" viewBox="0 0 24 24" width="24"><path d="M1 21H4V9H1V21ZM23 10C23 8.9 22.1 8 21 8H14.69L15.64 3.43L15.67 3.11C15.67 2.7 15.5 2.32 15.23 2.05L14.17 1L7.59 7.59C7.22 7.95 7 8.45 7 9V19C7 20.1 7.9 21 9 21H18C18.83 21 19.54 20.5 19.84 19.78L22.86 12.73C22.95 12.5 23 12.26 23 12V10Z" fill="white"></path></svg><div class="action-count">1.2K</div></div>
            <div class="shorts-action"><svg fill="none" height="24" viewBox="0 0 24 24" width="24"><path d="M23 4H20V16H23V4ZM1 15C1 16.1 1.9 17 3 17H9.31L8.36 21.57L8.33 21.89C8.33 22.3 8.5 22.68 8.77 22.95L9.83 24L16.41 17.41C16.78 17.05 17 16.55 17 16V6C17 4.9 16.1 4 15 4H6C5.17 4 4.46 4.5 4.16 5.22L1.14 12.27C1.05 12.5 1 12.74 1 13V15Z" fill="white"></path></svg><div class="action-count"><?php echo esc_html__('Dislike', 'yoapy-social-poster'); ?></div></div>
            <div class="shorts-action"><svg fill="none" height="24" viewBox="0 0 24 24" width="24"><path d="M12 14C11.45 14 11 13.55 11 13C11 12.45 11.45 12 12 12C12.55 12 13 12.45 13 13C13 13.55 12.55 14 12 14ZM12 10C11.45 10 11 9.55 11 9C11 8.45 11.45 8 12 8C12.55 8 13 8.45 13 9C13 9.55 12.55 10 12 10ZM12 6C11.45 6 11 5.55 11 5C11 4.45 11.45 4 12 4C12.55 4 13 4.45 13 5C13 5.55 12.55 6 12 6Z" fill="white"></path></svg><div class="action-count">89</div></div>
            <div class="shorts-action"><svg fill="none" height="24" viewBox="0 0 24 24" width="24"><path d="M12 3L13.89 7.33L18.67 7.5L15.19 10.6L16.42 15.33L12 12.67L7.58 15.33L8.81 10.6L5.33 7.5L10.11 7.33L12 3Z" fill="white"></path></svg><div class="action-count"><?php echo esc_html__('Share', 'yoapy-social-poster'); ?></div></div>
            <div class="shorts-avatar"></div>
        </div>

        <!-- Shorts Bottom Info -->
        <div class="shorts-bottom">
            <div class="shorts-title" id="shorts_text_content"></div>
            <div class="flex items-center gap-2 mt-2">
                <div class="shorts-avatar !w-8 !h-8"></div>
                <div class="shorts-channel" id="shorts_username"><?php echo esc_html__('Your Channel', 'yoapy-social-poster'); ?></div>
            </div>
        </div>
    </div>
</div>
