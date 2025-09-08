<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Enhanced Preview Script
?>
<script>
    (function(){
        // DOM Elements
        const previewDevice = document.getElementById('ysp_device');
        const previewPills = document.querySelectorAll('#ysp_prev_modes .ysp-pill');
        const textInput = document.getElementById('ysp_text');
        const imageInput = document.getElementById('ysp_image_url');
        const videoInput = document.getElementById('ysp_video_url');
        const typeSelect = document.querySelector('select[name="type"]');
        const networkCheckboxes = document.querySelectorAll('input[name="networks[]"]');

        // Preview containers
        const facebookPreview = document.getElementById('facebook_preview');
        const instagramPreview = document.getElementById('instagram_preview');
        const storiesPreview = document.getElementById('stories_preview');
        const reelsPreview = document.getElementById('reels_preview');
        const tiktokPreview = document.getElementById('tiktok_preview');
        const youtubePreview = document.getElementById('youtube_preview');
        const shortsPreview = document.getElementById('shorts_preview');

        // Handles data
        const handles = {
            facebook: '@yourfacebook',
            instagram: '@yourinstagram',
            youtube: '@youryoutube',
            tiktok: '@yourtiktok'
        };

        // Get handles from meta element if available
        const metaElement = document.getElementById('ysp_prev_meta');
        if (metaElement) {
            handles.facebook = metaElement.dataset.facebook || handles.facebook;
            handles.instagram = metaElement.dataset.instagram || handles.instagram;
            handles.youtube = metaElement.dataset.youtube || handles.youtube;
            handles.tiktok = metaElement.dataset.tiktok || handles.tiktok;
        }

        // Content filtering rules - networks that DON'T support certain content types
        const contentRestrictions = {
            image: ['tiktok', 'youtube'], // TikTok and YouTube primarily focus on video
            video: [], // All platforms support video
            story: ['tiktok', 'youtube'] // Only Facebook and Instagram have stories
        };

        // Current preview mode
        let currentMode = 'feed';

        // Preview mode mappings
        const previewModes = {
            feed: { container: instagramPreview, aspectRatio: '125%' }, // 4:5 Instagram feed
            story: { container: storiesPreview, aspectRatio: '177.78%' }, // 9:16
            reels: { container: reelsPreview, aspectRatio: '177.78%' }, // 9:16
            tiktok: { container: tiktokPreview, aspectRatio: '177.78%' }, // 9:16
            youtube: { container: youtubePreview, aspectRatio: '56.25%' }, // 16:9
            shorts: { container: shortsPreview, aspectRatio: '177.78%' } // 9:16
        };

        // Initialize preview system
        function initPreview() {
            // Set up mode switching buttons
            for (let i = 0; i < previewPills.length; i++) {
                previewPills[i].addEventListener('click', function(e) {
                    e.preventDefault();
                    const mode = this.dataset.mode;
                    switchPreviewMode(mode);
                });
            }

            // Set up input listeners
            if (textInput) {
                // Real-time text updates with 150ms debounce
                let textUpdateTimeout;
                textInput.addEventListener('input', function() {
                    clearTimeout(textUpdateTimeout);
                    textUpdateTimeout = setTimeout(() => {
                        updatePreviewContent();
                        // Auto-switch preview when text is added
                        if (this.value.trim()) {
                            autoSwitchPreviewMode();
                        }
                    }, 150);
                });
            }

            if (imageInput) {
                imageInput.addEventListener('input', function() {
                    updatePreviewMedia();
                    filterNetworksByContent();
                    // IMMEDIATE auto-preview when image is selected
                    if (this.value.trim()) {
                        setTimeout(() => autoSwitchPreviewMode(), 50);
                    }
                });
            }

            if (videoInput) {
                videoInput.addEventListener('input', function() {
                    updatePreviewMedia();
                    filterNetworksByContent();
                    // IMMEDIATE auto-preview when video is selected
                    if (this.value.trim()) {
                        setTimeout(() => autoSwitchPreviewMode(), 50);
                    }
                });
            }

            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    handleTypeChange();
                    filterNetworksByContent();
                    autoSwitchPreviewMode();
                });
            }

            // Set up network checkbox listeners
            for (let j = 0; j < networkCheckboxes.length; j++) {
                networkCheckboxes[j].addEventListener('change', handleNetworkChange);
            }

            // Initial setup
            updatePreviewContent();
            updatePreviewMedia();
            filterNetworksByContent();
            autoSwitchPreviewMode();

            // Ensure action overlays are visible from start
            showActionOverlays(true);
        }

        // Switch between preview modes
        function switchPreviewMode(mode) {
            if (!previewModes[mode]) return;

            currentMode = mode;

            // Update active pill
            for (let i = 0; i < previewPills.length; i++) {
                previewPills[i].classList.toggle('ysp-pill--active', previewPills[i].dataset.mode === mode);
            }

            // Animate pill selection
            const activePill = document.querySelector(`.ysp-pill[data-mode="${mode}"]`);
            if (activePill) {
                activePill.style.transform = 'translateY(-2px) scale(1.05)';
                setTimeout(() => {
                    activePill.style.transform = activePill.classList.contains('ysp-pill--active') ? 'translateY(-1px)' : '';
                }, 200);
            }

            // Update device data-mode and aspect ratio with animation
            if (previewDevice) {
                previewDevice.dataset.mode = mode;
                const frame = document.getElementById('ysp_prev_frame');
                if (frame) {
                    frame.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                    frame.style.setProperty('--ysp-ar', previewModes[mode].aspectRatio);
                }
            }

            // Hide all previews with fade effect
            const allModes = Object.values(previewModes);
            for (let i = 0; i < allModes.length; i++) {
                const container = allModes[i].container;
                if (container) {
                    container.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                    container.style.opacity = '0';
                    container.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        if (container) container.classList.add('hidden');
                    }, 200);
                }
            }

            // Show current preview with fade in effect
            setTimeout(() => {
                const current = previewModes[mode].container;
                if (current) {
                    current.classList.remove('hidden');
                    setTimeout(() => {
                        if (current) {
                            current.style.opacity = '1';
                            current.style.transform = 'scale(1)';
                        }
                    }, 50);
                }
            }, 250);

            // Update content for current mode
            setTimeout(() => {
                updatePreviewContent();
                updatePreviewMedia();
            }, 100);
        }

        // Handle type selection change with enhanced UX
        function handleTypeChange() {
            const type = typeSelect ? typeSelect.value : '';

            // Auto-switch preview mode based on type with animation
            switch (type) {
                case 'image':
                    switchPreviewMode('feed');
                    break;
                case 'video':
                    switchPreviewMode('reels');
                    break;
                case 'story':
                    switchPreviewMode('story');
                    break;
                case 'live_schedule':
                    switchPreviewMode('youtube');
                    break;
            }
        }

        // Enhanced content filtering with visual feedback
        function filterNetworksByContent() {
            const currentType = typeSelect ? typeSelect.value : 'image';
            const hasImage = imageInput ? imageInput.value.trim() : '';
            const hasVideo = videoInput ? videoInput.value.trim() : '';

            // Determine content type from inputs if type is mixed
            let contentType = currentType;
            if (currentType === 'video' || hasVideo) {
                contentType = 'video';
            } else if (hasImage && !hasVideo) {
                contentType = 'image';
            }

            const restrictedNetworks = contentRestrictions[contentType] || [];

            // Update network checkboxes with smooth animations
            for (let i = 0; i < networkCheckboxes.length; i++) {
                const checkbox = networkCheckboxes[i];
                const network = checkbox.value;
                const isRestricted = restrictedNetworks.includes(network);

                // Disable checkbox and provide visual feedback
                checkbox.disabled = isRestricted;

                // Find the parent label/container
                const label = checkbox.closest('label');
                if (label) {
                    label.style.transition = 'all 0.3s ease';

                    if (isRestricted) {
                        label.style.opacity = '0.4';
                        label.style.pointerEvents = 'none';
                        label.style.transform = 'scale(0.95)';
                        label.title = `${network.charAt(0).toUpperCase() + network.slice(1)} doesn't support ${contentType} posts`;
                        // Uncheck if currently checked
                        if (checkbox.checked) {
                            checkbox.checked = false;
                        }
                    } else {
                        label.style.opacity = '1';
                        label.style.pointerEvents = 'auto';
                        label.style.transform = 'scale(1)';
                        label.title = '';
                    }
                }
            }
        }

        // Enhanced auto-switch with smart logic
        function autoSwitchPreviewMode() {
            const hasImage = imageInput ? imageInput.value.trim() : '';
            const hasVideo = videoInput ? videoInput.value.trim() : '';

            // Get selected networks
            const selectedNetworks = [];
            for (let i = 0; i < networkCheckboxes.length; i++) {
                if (networkCheckboxes[i].checked) {
                    selectedNetworks.push(networkCheckboxes[i].value);
                }
            }

            // If no media, don't auto-switch
            if (!hasImage && !hasVideo) return;

            // ENHANCED Priority logic for auto-switching
            if (hasVideo) {
                // Video content - prioritize video platforms
                if (selectedNetworks.includes('tiktok')) {
                    switchPreviewMode('tiktok');
                } else if (selectedNetworks.includes('youtube')) {
                    switchPreviewMode('shorts');
                } else if (selectedNetworks.includes('instagram')) {
                    switchPreviewMode('reels');
                } else {
                    // Default to most popular video format
                    switchPreviewMode('reels');
                }
            } else if (hasImage) {
                // Image content - check type and networks
                const currentType = typeSelect ? typeSelect.value : '';
                if (currentType === 'story') {
                    switchPreviewMode('story');
                } else if (selectedNetworks.includes('instagram')) {
                    switchPreviewMode('feed');
                } else if (selectedNetworks.includes('facebook')) {
                    switchPreviewMode('feed');
                } else {
                    // Default to Instagram feed format
                    switchPreviewMode('feed');
                }
            }

            // Update content after switching
            setTimeout(() => {
                updatePreviewContent();
                updatePreviewMedia();
            }, 50);
        }

        // Handle network change events
        function handleNetworkChange() {
            // Auto-switch preview when networks change
            autoSwitchPreviewMode();
        }

        // Enhanced content updates with real-time typing effects
        function updatePreviewContent() {
            const text = textInput ? textInput.value : '';
            const cleanText = text.trim();

            // Update all text content areas with immediate updates
            const textElements = [
                'fb_text_content',
                'ig_text_content',
                'story_text_content',
                'reels_text_content',
                'tiktok_text_content',
                'youtube_text_content',
                'shorts_text_content'
            ];

            for (let i = 0; i < textElements.length; i++) {
                const id = textElements[i];
                const el = document.getElementById(id);
                if (el) {
                    // Immediate text update for real-time feedback
                    el.textContent = cleanText || 'Your content here...';
                }
            }

            // Update character counter
            const charCounter = document.getElementById('ysp_char');
            if (charCounter) {
                charCounter.textContent = text.length;
            }

            // Update usernames
            updateUsernames();
        }

        // Update usernames with smooth transitions
        function updateUsernames() {
            // Remove @ symbol from handles for consistent display
            const cleanHandles = {
                facebook: handles.facebook.replace(/^@/, ''),
                instagram: handles.instagram.replace(/^@/, ''),
                youtube: handles.youtube.replace(/^@/, ''),
                tiktok: handles.tiktok.replace(/^@/, '')
            };

            // Facebook
            const fbName = document.getElementById('fb_display_name');
            if (fbName) {
                updateElementWithFade(fbName, cleanHandles.facebook || 'Your Page Name');
            }

            // Instagram
            const igUsername = document.getElementById('ig_username');
            const igCaptionUsername = document.getElementById('ig_caption_username');
            if (igUsername) updateElementWithFade(igUsername, cleanHandles.instagram || 'yourusername');
            if (igCaptionUsername) updateElementWithFade(igCaptionUsername, cleanHandles.instagram || 'yourusername');

            // Stories
            const storyUsername = document.getElementById('story_username');
            if (storyUsername) updateElementWithFade(storyUsername, cleanHandles.instagram || 'yourusername');

            // Reels
            const reelsUsername = document.getElementById('reels_username');
            if (reelsUsername) updateElementWithFade(reelsUsername, cleanHandles.instagram || 'yourusername');

            // TikTok (always keep @ symbol for TikTok)
            const tiktokUsername = document.getElementById('tiktok_username');
            if (tiktokUsername) updateElementWithFade(tiktokUsername, '@' + cleanHandles.tiktok || '@yourusername');

            // YouTube
            const youtubeUsername = document.getElementById('youtube_username');
            if (youtubeUsername) updateElementWithFade(youtubeUsername, cleanHandles.youtube || 'Your Channel');

            // Shorts
            const shortsUsername = document.getElementById('shorts_username');
            if (shortsUsername) updateElementWithFade(shortsUsername, cleanHandles.youtube || 'Your Channel');
        }

        // Helper function for smooth element updates
        function updateElementWithFade(element, newText) {
            if (!element) return;

            element.style.transition = 'opacity 0.15s ease';
            element.style.opacity = '0.6';

            setTimeout(() => {
                element.textContent = newText;
                element.style.opacity = '1';
            }, 75);
        }

        // Update preview media with 100% width and vertical centering
        function updatePreviewMedia() {
            const imageUrl = imageInput ? imageInput.value.trim() : '';
            const videoUrl = videoInput ? videoInput.value.trim() : '';

            // Get current media type based on type selection or available media
            const type = typeSelect ? typeSelect.value : 'image';
            const isVideo = type === 'video' || (videoUrl && !imageUrl);
            const mediaUrl = isVideo ? videoUrl : imageUrl;

            // Update all preview images with 100% width and vertical centering
            const imageElements = [
                'fb_prev_img', 'ig_prev_img', 'story_prev_img',
                'reels_prev_img', 'tiktok_prev_img', 'youtube_prev_img', 'shorts_prev_img'
            ];

            const videoElements = [
                'fb_prev_vid', 'ig_prev_vid', 'story_prev_vid',
                'reels_prev_vid', 'tiktok_prev_vid', 'youtube_prev_vid', 'shorts_prev_vid'
            ];

            // Show/hide and style media elements with 100% width
            for (let i = 0; i < imageElements.length; i++) {
                const id = imageElements[i];
                const el = document.getElementById(id);
                if (el) {
                    if (!isVideo && mediaUrl) {
                        el.src = mediaUrl;
                        el.style.width = '100%';
                        el.style.height = '100%';
                        el.style.objectFit = 'contain';
                        el.style.display = 'block';
                        el.classList.remove('hidden');

                        // Show action overlays when media is displayed
                        showActionOverlays(true);
                    } else {
                        el.classList.add('hidden');
                    }
                }
            }

            for (let i = 0; i < videoElements.length; i++) {
                const id = videoElements[i];
                const el = document.getElementById(id);
                if (el) {
                    if (isVideo && mediaUrl) {
                        el.src = mediaUrl;
                        el.style.width = '100%';
                        el.style.height = '100%';
                        el.style.objectFit = 'contain';
                        el.style.display = 'block';
                        el.muted = true;
                        el.loop = true;
                        el.classList.remove('hidden');

                        // Auto-play video for preview
                        try {
                            el.play();
                        } catch(e) {
                            console.log('Autoplay failed:', e);
                        }

                        // Show action overlays when media is displayed
                        showActionOverlays(true);
                    } else {
                        el.classList.add('hidden');
                    }
                }
            }

            // Hide action overlays if no media
            if (!mediaUrl) {
                showActionOverlays(false);
            }
        }

        // Show/hide action overlays for horizontal formats
        function showActionOverlays(show) {
            const overlays = [
                document.querySelector('.fb-action-overlay'),
                document.querySelector('.ig-action-overlay'),
                document.querySelector('.youtube-action-overlay')
            ];

            for (let i = 0; i < overlays.length; i++) {
                const overlay = overlays[i];
                if (overlay) {
                    overlay.style.display = show ? 'flex' : 'none';
                    overlay.style.opacity = show ? '1' : '0';
                }
            }
        }

        // Initialize when DOM is ready with New Task auto-preview
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initPreview();

                // Auto-start with "New Task" preview mode
                switchPreviewMode('feed');

                // Show empty state with sample content
                updatePreviewContent();
                updatePreviewMedia();
            });
        } else {
            // DOM is already ready
            initPreview();

            // Auto-start with "New Task" preview mode
            switchPreviewMode('feed');

            // Show empty state with sample content
            updatePreviewContent();
            updatePreviewMedia();
        }

        // Also initialize on load for safety
        window.addEventListener('load', function() {
            initPreview();

            // Ensure auto-preview is working
            if (!currentMode) {
                switchPreviewMode('feed');
            }
        });

        // Expose autoSwitchPreviewMode globally for upload integration
        window.autoSwitchPreviewMode = autoSwitchPreviewMode;

    })();
</script>
