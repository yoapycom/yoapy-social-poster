<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<style>
    /* ================== ESTRUTURA DO PREVIEW ================== */

    /* Container do "Celular" (Device) */
    .ysp-device {
        position: relative;
        background: #111;
        border: 1px solid #444;
        border-radius: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3), inset 0 0 2px #fff3;
        padding: 12px;
        transition: width 0.4s ease-in-out, max-width 0.4s ease-in-out;
        width: 100%;
        max-width: 360px; /* Largura padrão para modo vertical */
    }

    /* Orientação do Celular (Controlado por data-mode via JS) */
    .ysp-device[data-mode="youtube"] {
        max-width: 640px; /* Largura para modo horizontal */
    }

    /* A "Tela" do Celular */
    .ysp-ar {
        position: relative;
        width: 100%;
        height: 0;
        padding-top: var(--ysp-ar, 177.77%); /* Proporção padrão 9:16 */
        background: #000;
        border-radius: 28px;
        overflow: hidden;
    }

    .ysp-ar-obj {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    /* Container de cada preview individual (ex: Facebook, Instagram) */
    .preview-container {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1;
    }

    .preview-container:not(.hidden) {
        opacity: 1;
        z-index: 2;
    }


    /* ================== BOTÕES DE MODO (PILLS) ================== */
    .ysp-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        border-radius: 999px;
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .ysp-pill:hover {
        border-color: #38bdf8;
        background: #f0f9ff;
        color: #0369a1;
    }

    .ysp-pill.ysp-pill--active {
        background: #0ea5e9;
        color: #fff;
        border-color: #0ea5e9;
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
    }
</style>
