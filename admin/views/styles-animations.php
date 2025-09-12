<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Enhanced Animations and Visual Effects Styles
?>
<style>
    /* SMOOTH ANIMATIONS FOR UI ELEMENTS */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulseGlow {
        0%, 100% {
            box-shadow: 0 0 5px rgba(59, 130, 246, 0.3);
        }
        50% {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.6);
        }
    }

    /* Real-time input feedback */
    input:focus, textarea:focus, select:focus {
        animation: pulseGlow 2s infinite;
        border-color: rgba(59, 130, 246, 0.5) !important;
    }

    /* Enhanced character counter with live updates */
    #yoapsopo_char {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 6px 12px;
        border-radius: 15px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
        color: #3b82f6;
        font-weight: 600;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    /* Enhanced form elements */
    input[type="text"], input[type="url"], input[type="datetime-local"], select, textarea {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    input[type="text"]:hover, input[type="url"]:hover, input[type="datetime-local"]:hover, select:hover, textarea:hover {
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
    }

    /* Enhanced dropzone interactions */
    .yoapsopo-dz {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .yoapsopo-dz:hover {
        transform: translateY(-2px);
        border-color: rgba(59, 130, 246, 0.4);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(59, 130, 246, 0.02));
    }

    .yoapsopo-dz.dragover {
        transform: translateY(-4px) scale(1.02);
        border-color: rgba(59, 130, 246, 0.6);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
        box-shadow: 0 12px 35px rgba(59, 130, 246, 0.2);
    }
</style>
