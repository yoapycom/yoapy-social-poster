<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<style>
    /* ================== ESTILOS GERAIS PARA MÍDIA ================== */

    /* O "Palco" da Mídia */
    .fb-media, .ig-media, .story-media, .reels-media, .tiktok-media, .youtube-media-layer, .shorts-media {
        position: relative; /* Essencial para o posicionamento absoluto dos filhos */
        inset: 0;
        width: 100%;
        height: 100%;
        background-color: #000;
        overflow: hidden;
    }

    /* A Mídia em si (Imagem ou Vídeo) */
    .fb-image, .fb-video, .ig-image, .ig-video, .story-image, .story-video,
    .reels-image, .reels-video, .tiktok-image, .tiktok-video,
    .youtube-image, .youtube-video, .shorts-image, .shorts-video {
        /* ===== A MÁGICA ESTÁ AQUI ===== */
        position: absolute; /* Sobrepõe os elementos */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        /* Começam invisíveis para evitar a imagem quebrada */
        display: none;
    }

    /* ================== FACEBOOK PREVIEW ================== */
    .facebook-post { display: flex; flex-direction: column; width: 100%; height: 100%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #fff; }
    .fb-header { display: flex; align-items: center; padding: 12px 16px; gap: 12px; }
    .fb-avatar { width: 40px; height: 40px; border-radius: 50%; background: #e4e6eb; }
    .fb-info { flex: 1; }
    .fb-name { font-size: 15px; font-weight: 600; color: #050505; }
    .fb-time { font-size: 13px; color: #65676b; }
    .fb-menu { font-size: 20px; color: #65676b; cursor: pointer; }
    .fb-text { padding: 4px 16px 16px; font-size: 15px; line-height: 1.33; color: #050505; white-space: pre-wrap; }
    .fb-media { position: relative; flex: 1; } /* Mantido para ocupar espaço */
    .fb-actions { padding: 4px 16px 12px; background: #fff; flex-shrink: 0; }
    .fb-stats { font-size: 13px; color: #65676b; padding: 10px 0; display: flex; justify-content: space-between; border-bottom: 1px solid #e4e6ea; }
    .fb-buttons { display: flex; justify-content: space-around; padding-top: 6px; }
    .fb-btn { color: #65676b; font-size: 14px; font-weight: 600; padding: 8px; cursor: pointer; flex: 1; text-align: center; border-radius: 4px; transition: background-color 0.2s; }
    .fb-btn:hover { background-color: #f2f3f4; }

    /* ================== INSTAGRAM FEED STYLES ================== */
    .ig-post { display: flex; flex-direction: column; width: 100%; height: 100%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #fff; }
    .ig-header { display: flex; align-items: center; padding: 12px 16px; gap: 10px; border-bottom: 1px solid #dbdbdb; }
    .ig-avatar { width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; background: #e4e6eb; }
    .ig-username-container { flex-grow: 1; display: flex; align-items: center; gap: 4px; }
    .ig-username { font-size: 14px; font-weight: 600; color: #262626; }
    .ig-verified { width: 14px; height: 14px; }
    .ig-menu { width: 24px; height: 24px; color: #262626; cursor: pointer; }
    .ig-media { position: relative; flex: 1; }
    .ig-actions { padding: 12px 16px; background: #fff; text-align: left; }
    .ig-action-buttons { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .ig-left-actions { display: flex; gap: 16px; }
    .ig-action-btn, .ig-save { background: none; border: none; padding: 0; cursor: pointer; }
    .ig-action-btn svg, .ig-save svg { width: 24px; height: 24px; color: #262626; }
    .ig-likes { font-size: 14px; font-weight: 500; color: #262626; margin-bottom: 8px; }
    .ig-caption { font-size: 14px; color: #262626; margin-bottom: 8px; line-height: 1.4; white-space: pre-wrap; }
    .ig-caption-username { margin-right: 6px; }
    .ig-time { font-size: 12px; color: #8e8e8e; text-transform: uppercase; }

    /* ================== YOUTUBE PLAYER STYLES ================== */
    .youtube-player { position: relative; width: 100%; height: 100%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #000; color: #fff; }
    .youtube-gradient-top, .youtube-gradient-bottom { position: absolute; left: 0; right: 0; height: 50%; pointer-events: none; z-index: 1; }
    .youtube-gradient-top { top: 0; background: linear-gradient(180deg, rgba(0,0,0,0.6), transparent); }
    .youtube-gradient-bottom { bottom: 0; background: linear-gradient(0deg, rgba(0,0,0,0.6), transparent); }
    .youtube-ui-overlay { position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: space-between; padding: 14px 18px; z-index: 2; text-shadow: 0 1px 3px rgba(0,0,0,0.6); }
    .youtube-top-bar .youtube-title { font-size: 18px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .youtube-center-play { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 68px; height: 48px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 3; }
    .youtube-bottom-bar { display: flex; justify-content: space-between; align-items: flex-end; }
    .youtube-channel-info-overlay { display: flex; align-items: center; gap: 10px; }
    .youtube-avatar { width: 40px; height: 40px; border-radius: 50%; background: #aaa; flex-shrink: 0; }
    .youtube-channel-name { font-size: 14px; font-weight: 500; }
    .youtube-subscribers { font-size: 12px; color: rgba(255,255,255,0.8); }
    .youtube-actions-overlay .youtube-subscribe { background: #fff; color: #0f0f0f; border: none; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background-color 0.2s; }
    .youtube-subscribe:hover { background-color: #f2f2f2; }

    /* ================== ESTILOS VERTICAIS (REELS, STORIES, ETC.) ================== */
    .story-container, .reels-container, .tiktok-container, .shorts-container { position: relative; width: 100%; height: 100%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #fff; }
    .story-header, .reels-header, .tiktok-header, .shorts-header { position: absolute; top: 15px; left: 15px; right: 15px; z-index: 10; display: flex; align-items: center; gap: 10px; text-shadow: 0 1px 3px rgba(0,0,0,0.5); }
    .story-avatar, .reels-avatar, .tiktok-avatar, .shorts-avatar { width: 32px; height: 32px; border-radius: 50%; background: #555; border: 2px solid #fff; }
    .story-username, .reels-username, .tiktok-username, .shorts-channel { font-weight: 600; }
    .story-text, .reels-caption, .tiktok-caption, .shorts-title { position: absolute; bottom: 80px; left: 15px; right: 15px; z-index: 10; text-align: center; text-shadow: 0 1px 3px rgba(0,0,0,0.7); white-space: pre-wrap; }
    .reels-right-actions, .tiktok-right-actions, .shorts-right-actions { position: absolute; bottom: 80px; right: 10px; z-index: 10; display: flex; flex-direction: column; align-items: center; gap: 20px; }
    .action-count { font-size: 12px; }
</style>
