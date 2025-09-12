<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Utility Styles for modern controls
?>
<style>
    /* Toggles modernos para checkboxes */
    .yoapsopo-track {
        position: relative;
        height: 1.5rem; /* 24px */
        width: 2.75rem; /* 44px */
        border-radius: 9999px;
        background-color: #e5e7eb; /* cinza-200 */
        transition: background-color 0.2s ease-in-out;
    }
    .yoapsopo-thumb {
        position: absolute;
        left: 2px;
        top: 2px;
        height: 1.25rem; /* 20px */
        width: 1.25rem; /* 20px */
        border-radius: 9999px;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: transform 0.2s ease-in-out;
    }
    input:checked + .yoapsopo-track .yoapsopo-thumb {
        transform: translateX(1.25rem); /* 20px */
    }

    /* Dropzone (Área de Arrastar e Soltar) */
    .yoapsopo-dz {
        border: 2px dashed #d1d5db;
        border-radius: 0.75rem;
        padding: 1rem;
        text-align: center;
        background-color: #f9fafb;
        cursor: pointer;
    }
    .yoapsopo-dz-inner { color: #6b7280; }
    .yoapsopo-dz-ic { font-size: 1.5rem; }
    .yoapsopo-dz-title { font-weight: 600; color: #374151; margin-top: 0.5rem; }
    .yoapsopo-dz-sub { font-size: 0.875rem; margin-top: 0.25rem; }
    .yoapsopo-dz-browse { color: #0ea5e9; font-weight: 500; background:none; border:0; text-decoration: underline; cursor:pointer; }
    .yoapsopo-dz-progress { margin-top: 0.75rem; height: 6px; background: #e5e7eb; border-radius: 999px; overflow:hidden; }
    .yoapsopo-dz-progress > span { display:block; height:100%; width:0%; background: #0ea5e9; transition: width .2s ease; }

    .yoapsopo-net-chip { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 500; border: 1px solid; text-transform: capitalize; }
    .yoapsopo-net--facebook { background-color: #eef2ff; color: #3b82f6; border-color: #c7d2fe; }
    .yoapsopo-net--instagram { background-color: #fdf2f8; color: #e11d48; border-color: #fbcfe8; }
    .yoapsopo-net--youtube { background-color: #fee2e2; color: #ef4444; border-color: #fecaca; }
    .yoapsopo-net--tiktok { background-color: #f1f5f9; color: #1e293b; border-color: #e2e8f0; }

    .yoapsopo-status-badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 500; text-transform: capitalize; }
    .yoapsopo-status--pending { background-color: #f1f5f9; color: #475569; }
    .yoapsopo-status--processing { background-color: #fffbeb; color: #b45309; }
    .yoapsopo-status--complete { background-color: #f0fdf4; color: #16a34a; }
    .yoapsopo-status--error { background-color: #fef2f2; color: #dc2626; }

    .yoapsopo-result-link { display: inline-flex; align-items: center; gap: 4px; color: #0ea5e9; text-decoration: none; padding: 2px 6px; border-radius: 4px; background-color: #f0f9ff; transition: background-color .2s; }
    .yoapsopo-result-link:before { content: '🔗'; }
    .yoapsopo-result-link:hover { background-color: #e0f2fe; text-decoration: none; }

    .yoapsopo-result-error { display: inline-flex; align-items: center; gap: 4px; color: #ef4444; cursor: help; padding: 2px 6px; border-radius: 4px; background-color: #fef2f2; }
    .yoapsopo-result-error:before { content: '⚠️'; }
</style>
