<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Utility Styles for modern controls
?>
<style>
    /* Toggles modernos para checkboxes */
    .ysp-track {
        position: relative;
        height: 1.5rem; /* 24px */
        width: 2.75rem; /* 44px */
        border-radius: 9999px;
        background-color: #e5e7eb; /* cinza-200 */
        transition: background-color 0.2s ease-in-out;
    }
    .ysp-thumb {
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
    input:checked + .ysp-track .ysp-thumb {
        transform: translateX(1.25rem); /* 20px */
    }

    /* Dropzone (Área de Arrastar e Soltar) */
    .ysp-dz {
        border: 2px dashed #d1d5db;
        border-radius: 0.75rem;
        padding: 1rem;
        text-align: center;
        background-color: #f9fafb;
        cursor: pointer;
    }
    .ysp-dz-inner { color: #6b7280; }
    .ysp-dz-ic { font-size: 1.5rem; }
    .ysp-dz-title { font-weight: 600; color: #374151; margin-top: 0.5rem; }
    .ysp-dz-sub { font-size: 0.875rem; margin-top: 0.25rem; }
    .ysp-dz-browse { color: #0ea5e9; font-weight: 500; background:none; border:0; text-decoration: underline; cursor:pointer; }
    .ysp-dz-progress { margin-top: 0.75rem; height: 6px; background: #e5e7eb; border-radius: 999px; overflow:hidden; }
    .ysp-dz-progress > span { display:block; height:100%; width:0%; background: #0ea5e9; transition: width .2s ease; }
</style>
