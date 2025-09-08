<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

// Media Upload Functionality Script - Versão Corrigida e Final
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Garante que a biblioteca de mídia do WordPress foi carregada
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            console.error('YSP: WordPress media library not loaded.');
            return;
        }

        // Usaremos uma única instância do seletor de mídia para melhor performance
        let mediaFrame;

        // Usa delegação de evento para ouvir cliques em qualquer botão .ysp-pick
        document.body.addEventListener('click', function(e) {
            const button = e.target.closest('.ysp-pick');
            if (!button) {
                return;
            }

            e.preventDefault();

            const targetSelector = button.dataset.target;
            const targetInput = document.querySelector(targetSelector);
            if (!targetInput) {
                console.error('YSP Media Picker: Input de destino não encontrado para o seletor:', targetSelector);
                return;
            }

            // Cria o frame de mídia apenas na primeira vez que for necessário
            if (!mediaFrame) {
                mediaFrame = wp.media({
                    title: '<?php esc_html_e( "Choose or Upload Media", 'yoapy-social-poster' ); ?>',
                    button: {
                        text: '<?php esc_html_e( "Use this media", 'yoapy-social-poster' ); ?>'
                    },
                    multiple: false
                });
            }

            // *** A CORREÇÃO CRÍTICA ESTÁ AQUI ***
            // 1. Remove qualquer "ouvinte" do evento 'select' que possa existir de cliques anteriores.
            //    Isso impede que ele atualize o campo de input antigo.
            mediaFrame.off('select');

            // 2. Adiciona um novo "ouvinte" para o evento 'select'. Este novo ouvinte
            //    sabe exatamente qual é o 'targetInput' ATUAL, pois foi definido no clique do botão.
            mediaFrame.on('select', function() {
                const attachment = mediaFrame.state().get('selection').first().toJSON();

                // Atualiza o valor do campo de input correto
                targetInput.value = attachment.url;

                // Dispara o evento 'input' para que o script de preview seja ativado
                targetInput.dispatchEvent(new Event('input', { bubbles: true }));
            });

            // 3. Abre o seletor de mídia.
            mediaFrame.open();
        });
    });
</script>
