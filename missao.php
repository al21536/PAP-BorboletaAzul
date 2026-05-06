<?php
session_start();
$page_title = 'Missão - Fundação Borboleta Azul';
include 'includes/header.php';
?>

<div class="container">
    <section class="secao">
        <h2 style="color: var(--azul-principal);">A Nossa Missão</h2>
        <p style="font-size: 1.2em; max-width: 800px; margin: 20px auto; line-height: 1.5;">
            A missão da Fundação Borboleta Azul é **proporcionar apoio integral e esperança a crianças e jovens em luta contra o cancro e às suas famílias**, honrando a memória de Joana Baptista. Trabalhamos para:
        </p>

        <ul style="list-style: none; text-align: left; max-width: 700px; margin: 30px auto; padding: 0;">
            <li style="margin-bottom: 15px; background: white; padding: 15px; border-radius: 8px; box-shadow: var(--sombra); border-left: 4px solid var(--azul-principal);">
                <strong>Apoiar Emocional e Psicologicamente:</strong> Oferecer suporte e orientação profissional para ajudar a lidar com os desafios emocionais da doença.
            </li>
            <li style="margin-bottom: 15px; background: white; padding: 15px; border-radius: 8px; box-shadow: var(--sombra); border-left: 4px solid var(--azul-principal);">
                <strong>Facilitar Recursos Essenciais:</strong> Angariar e distribuir bens e serviços que melhorem a qualidade de vida durante o tratamento e recuperação.
            </li>
            <li style="margin-bottom: 15px; background: white; padding: 15px; border-radius: 8px; box-shadow: var(--sombra); border-left: 4px solid var(--azul-principal);">
                <strong>Promover a Solidariedade:</strong> Criar uma comunidade ativa e participativa, onde membros e voluntários se unem para apoiar a causa.
            </li>
            <li style="margin-bottom: 15px; background: white; padding: 15px; border-radius: 8px; box-shadow: var(--sombra); border-left: 4px solid var(--azul-principal);">
                <strong>Inspirar através da Esperança:</strong> Partilhar histórias de superação e incentivar a esperança contínua, celebrando a vida.
            </li>
        </ul>
        <p style="margin-top: 30px;">
            Através do nosso website e eventos, conectamos corações e construímos um futuro onde o amor e o apoio são as forças dominantes.
        </p>
    </section>
</div>

<?php
include 'includes/footer.php';
?>