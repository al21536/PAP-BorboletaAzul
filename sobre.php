<?php
session_start();
$page_title = 'Sobre Nós - Borboleta Azul';
include 'includes/header.php';
?>

<div class="container" style="padding: 60px 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="color: var(--azul-escuro); text-align: center; margin-bottom: 30px;">A Inspiração: Joana Baptista</h2>
        
        <div style="background: white; border-radius: 20px; overflow: hidden; box-shadow: var(--sombra); margin-bottom: 40px;">
            <div style="padding: 40px;">
                <p style="font-size: 1.1rem; line-height: 1.8; color: #444; margin-bottom: 20px;">
                    A Joana era uma jovem cheia de vida, sonhos e uma determinação inabalável. O diagnóstico de cancro pulmonar não lhe roubou a luz; pelo contrário, tornou-a um farol para todos os que a rodeavam.
                </p>
                <p style="font-size: 1.1rem; line-height: 1.8; color: #444;">
                    Esta fundação nasceu para dar continuidade à sua onda de otimismo. A "Borboleta Azul" simboliza a transformação e a liberdade do espírito, valores que a Joana carregava consigo todos os dias.
                </p>
            </div>
        </div>

        <h3 style="color: var(--azul-principal); text-align: center; margin: 50px 0 30px 0; font-size: 1.8rem;">Os Nossos Valores</h3>
        <div class="features" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            
            <div class="feature-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px;">
                <span style="font-size: 3rem; margin-bottom: 15px;">🤝</span>
                <h4 style="color: var(--azul-escuro); margin-bottom: 10px;">Proximidade</h4>
                <p style="font-size: 0.95rem; color: #666;">Apoio direto, humano e constante a cada família que cruza o nosso caminho.</p>
            </div>

            <div class="feature-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px;">
                <span style="font-size: 3rem; margin-bottom: 15px;">🛡️</span>
                <h4 style="color: var(--azul-escuro); margin-bottom: 10px;">Transparência</h4>
                <p style="font-size: 0.95rem; color: #666;">Gestão ética e rigorosa de todos os recursos e donativos recebidos pela fundação.</p>
            </div>

            <div class="feature-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px;">
                <span style="font-size: 3rem; margin-bottom: 15px;">💙</span>
                <h4 style="color: var(--azul-escuro); margin-bottom: 10px;">Dedicação</h4>
                <p style="font-size: 0.95rem; color: #666;">Compromisso total com o bem-estar e a felicidade dos nossos jovens guerreiros.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>