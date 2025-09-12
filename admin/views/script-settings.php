<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Drag and Drop & Media Upload Functionality Script
?>
<!-- Scripts pequenos: revelar/copiar + toast -->
<script>
    (function(){
        // Toggle secret
        var btn = document.getElementById('yoapsopo_secret_toggle');
        var inp = document.getElementById('yoapsopo_secret');
        if(btn && inp){
            btn.addEventListener('click', function(){
                var showing = inp.type === 'text';
                inp.type = showing ? 'password' : 'text';
                btn.innerHTML = (showing
                    ? '<span class="dashicons dashicons-visibility"></span> ' + <?php echo wp_json_encode( __( 'Show', 'yoapy-social-poster' ) ); ?>
                    : '<span class="dashicons dashicons-hidden"></span> ' + <?php echo wp_json_encode( __( 'Hide', 'yoapy-social-poster' ) ); ?>);
            });
        }

        // Copy helpers
        function toast(msg){
            var t = document.createElement('div');
            t.className = 'fixed z-50 bottom-5 right-5 bg-slate-900 text-white text-sm rounded-lg px-3 py-2 shadow-lg transition-all';
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(function(){ t.style.opacity='0'; t.style.transform='translateY(6px)'; }, 1400);
            setTimeout(function(){ t.remove(); }, 1800);
        }
        document.querySelectorAll('[data-clip]').forEach(function(btn){
            btn.addEventListener('click', function(){
                var sel = btn.getAttribute('data-clip');
                var el = document.querySelector(sel);
                if(!el) return;
                if (el.select) el.select();
                try {
                    navigator.clipboard.writeText(el.value || '');
                    toast(<?php echo wp_json_encode( __( 'Copied!', 'yoapy-social-poster' ) ); ?>);
                } catch(e){
                    toast(<?php echo wp_json_encode( __( 'Copy manually.', 'yoapy-social-poster' ) ); ?>);
                }
            });
        });
    })();
</script>
