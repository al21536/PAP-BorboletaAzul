<?php
session_start();
$page_title = 'Início - Fundação Borboleta Azul';
include 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 20px;">Transformar a Saudade em Esperança</h1>
        <p style="font-size: 1.3rem; max-width: 700px; margin: 0 auto 30px auto; line-height: 1.6;">
            Apoiamos crianças e jovens na luta contra o cancro, honrando a memória e construindo um futuro com mais sorrisos.
        </p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="registo.php" class="btn btn-cta">Juntar-se à Causa</a>
        <?php else: ?>
            <a href="eventos.php" class="btn btn-cta">Ver Próximos Eventos</a>
        <?php endif; ?>
    </div>
</section>

<section class="secao secao-branca" style="padding: 40px 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px;">
            <div style="text-align: center;">
                <h2 style="color: var(--azul-principal); font-size: 2.5rem; margin-bottom: 5px;">+500</h2>
                <p style="color: #666; font-weight: bold;">Membros Ativos</p>
            </div>
            <div style="text-align: center;">
                <h2 style="color: var(--azul-principal); font-size: 2.5rem; margin-bottom: 5px;">12</h2>
                <p style="color: #666; font-weight: bold;">Eventos Anuais</p>
            </div>
            <div style="text-align: center;">
                <h2 style="color: var(--azul-principal); font-size: 2.5rem; margin-bottom: 5px;">100%</h2>
                <p style="color: #666; font-weight: bold;">Solidariedade</p>
            </div>
        </div>
    </div>
</section>

<section class="secao" style="background-color: var(--azul-claro);">
    <div class="container">
        <div style="display: flex; flex-wrap: wrap; gap: 40px; align-items: center;">
            <div style="flex: 1; min-width: 300px;">
                <h2 style="color: var(--azul-escuro); margin-bottom: 20px;">A Nossa Missão</h2>
                <p style="text-align: justify; line-height: 1.8;">
                    A Fundação Borboleta Azul foca-se em proporcionar momentos de alegria e suporte prático a famílias que enfrentam o cancro pediátrico. Através da nossa rede de membros, organizamos eventos que angariam fundos e espalham luz em momentos de escuridão.
                </p>
            </div>
            <div style="flex: 1; min-width: 300px;">
                <div class="homenagem-box" style="background: white; padding: 30px; border-radius: 15px; box-shadow: var(--sombra);">
                    <p style="font-size: 1.1rem; color: #444; margin-bottom: 15px;">
                        "A Joana ensinou-nos que a coragem não é a ausência de medo, mas a força para seguir em frente com um sorriso."
                    </p>
                    <cite style="color: var(--azul-principal); font-weight: bold;">— Família Baptista</cite>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>