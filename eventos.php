<?php
session_start();
include 'config/db.php';

// 1. VERIFICA SE ESTÁ LOGADO (Se não estiver, a variável fica null em vez de expulsar)
$id_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$mensagem = "";

// 2. PROCESSAR INSCRIÇÃO (Apenas quem tem login pode executar isto)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_evento'])) {
    if (!$id_user) {
        // Por segurança, se um visitante tentar forçar um POST, manda-o para o login
        header("Location: login.php");
        exit;
    }

    $id_evento = $_POST['id_evento'];

    // Verificar se já está inscrito
    $check = $conn->query("SELECT id FROM inscricoes WHERE id_utilizador = '$id_user' AND id_evento = '$id_evento'");
    
    // Verificar vagas
    $evento_check = $conn->query("SELECT vagas_totais, vagas_ocupadas FROM eventos WHERE id = '$id_evento'");
    $dados_evento = $evento_check->fetch_assoc();

    if ($check->num_rows > 0) {
        $mensagem = "<div style='background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:20px; text-align:center;'>Já está inscrito neste evento!</div>";
    } elseif ($dados_evento['vagas_ocupadas'] >= $dados_evento['vagas_totais']) {
        $mensagem = "<div style='background:#fff3cd; color:#856404; padding:10px; border-radius:5px; margin-bottom:20px; text-align:center;'>Lamentamos, mas as vagas esgotaram.</div>";
    } else {
        // Realizar Inscrição
        $conn->query("INSERT INTO inscricoes (id_utilizador, id_evento) VALUES ('$id_user', '$id_evento')");
        $conn->query("UPDATE eventos SET vagas_ocupadas = vagas_ocupadas + 1 WHERE id = '$id_evento'");
        
        $mensagem = "<div style='background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:20px; text-align:center;'>Inscrição realizada com sucesso! Vemo-nos lá.</div>";
    }
}

// Configuração da Página
$page_title = 'Eventos - Borboleta Azul';
include 'includes/header.php';
?>

<div class="container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2 style="color: var(--azul-escuro);">Eventos Solidários</h2>
        <p>Participe, ajude e conviva. A sua presença faz a diferença.</p>
    </div>

    <?php echo $mensagem; ?>

    <div class="features" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
        <?php
        // Buscar eventos futuros (ordenados por data mais próxima)
        $sql = "SELECT * FROM eventos ORDER BY data_evento ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $id_evento = $row['id'];
                $data = date('d/m/Y', strtotime($row['data_evento']));
                $hora = date('H:i', strtotime($row['data_evento']));
                $vagas_restantes = $row['vagas_totais'] - $row['vagas_ocupadas'];
                
                // Verificar estado para o botão apenas se o utilizador estiver logado
                $ja_inscrito = false;
                if ($id_user) {
                    $ja_inscrito = $conn->query("SELECT id FROM inscricoes WHERE id_utilizador = '$id_user' AND id_evento = '$id_evento'")->num_rows > 0;
                }
                
                $evento_passado = (strtotime($row['data_evento']) < time());
                $esgotado = ($vagas_restantes <= 0);

                ?>
                
                <div class="feature-card" style="text-align: left; position: relative;">
                    <div style="background: var(--azul-claro); color: var(--azul-escuro); padding: 5px 10px; border-radius: 4px; display: inline-block; font-weight: bold; margin-bottom: 10px;">
                        📅 <?php echo $data; ?> às <?php echo $hora; ?>
                    </div>

                    <h3 style="margin-bottom: 10px;"><?php echo htmlspecialchars($row['titulo']); ?></h3>
                    
                    <p style="color: #666; font-size: 0.95rem; margin-bottom: 20px; min-height: 50px;">
                        <?php echo htmlspecialchars($row['descricao']); ?>
                    </p>

                    <div style="margin-bottom: 20px; font-size: 0.9rem; color: #555;">
                        <strong>Vagas:</strong> 
                        <?php 
                        if ($esgotado) {
                            echo "<span style='color: red; font-weight: bold;'>Esgotado</span>";
                        } else {
                            echo "$vagas_restantes disponíveis";
                        }
                        ?>
                    </div>

                    <?php if ($evento_passado): ?>
                        <button class="btn" style="background-color: #ccc; cursor: not-allowed; width: 100%;" disabled>Evento Terminado</button>
                    
                    <?php elseif ($ja_inscrito): ?>
                        <button class="btn" style="background-color: #28a745; cursor: default; width: 100%;" disabled>✅ Já Inscrito</button>
                    
                    <?php elseif ($esgotado): ?>
                        <button class="btn" style="background-color: #dc3545; cursor: not-allowed; width: 100%;" disabled>Esgotado</button>
                    
                    <?php elseif (!$id_user): ?>
                        <a href="login.php" class="btn" style="display: block; text-align: center; width: 100%;">Fazer Login para Inscrever</a>
                    
                    <?php else: ?>
                        <form method="post" action="">
                            <input type="hidden" name="id_evento" value="<?php echo $id_evento; ?>">
                            <button type="submit" class="btn" style="width: 100%;">Inscrever-me</button>
                        </form>
                    <?php endif; ?>

                </div>

                <?php
            }
        } else {
            // ==========================================
            // ESTADO VAZIO (EMPTY STATE) INTELIGENTE
            // ==========================================
            ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <span style="font-size: 4rem; display: block; margin-bottom: 20px; animation: float 3s ease-in-out infinite;">🦋</span>
                <h3 style="color: var(--azul-escuro); margin-bottom: 15px; font-size: 1.5rem;">A preparar novos momentos...</h3>
                <p style="color: #666; max-width: 500px; margin: 0 auto 30px auto; font-size: 1.1rem;">
                    De momento não temos novos eventos com inscrições abertas. A nossa equipa está a trabalhar em novas iniciativas solidárias. Fique atento!
                </p>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    
                    <?php 
                    // BOTÃO DINÂMICO
                    if ($id_user): 
                    ?>
                        <a href="perfil.php" class="btn" style="width: auto; background-color: var(--azul-principal);">Voltar ao Meu Cartão</a>
                    <?php else: ?>
                        <a href="index.php" class="btn" style="width: auto; background-color: var(--azul-principal);">Voltar à Página Inicial</a>
                    <?php endif; ?>

                    <a href="contactos.php" class="btn" style="width: auto; background-color: #6c757d;">Falar Connosco</a>
                </div>
            </div>

            <style>
                @keyframes float {
                    0% { transform: translateY(0px); }
                    50% { transform: translateY(-10px); }
                    100% { transform: translateY(0px); }
                }
            </style>
            <?php
        }
        ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>