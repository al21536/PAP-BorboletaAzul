<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['criar_evento'])) {
    $titulo = $_POST['titulo'];
    $data = $_POST['data_evento'];
    $vagas = $_POST['vagas_totais'];
    $descricao = $_POST['descricao'];

    $sql = "INSERT INTO eventos (titulo, descricao, data_evento, vagas_totais) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $titulo, $descricao, $data, $vagas);

    if ($stmt->execute()) {
        $mensagem_sucesso = "Evento criado com sucesso!";
    } else {
        $mensagem_erro = "Erro ao criar evento: " . $conn->error;
    }
}

if (isset($_GET['delete_id'])) {
    $id_apagar = $_GET['delete_id'];
    
    $conn->query("DELETE FROM inscricoes WHERE id_evento = $id_apagar");
    
    if ($conn->query("DELETE FROM eventos WHERE id = $id_apagar")) {
        header("Location: admin_painel.php");
        exit;
    } else {
        $mensagem_erro = "Erro ao apagar evento.";
    }
}

$sql_mensagens = "SELECT * FROM mensagens_contactos ORDER BY data_envio DESC";
$result_mensagens = $conn->query($sql_mensagens);

$total_membros = $conn->query("SELECT COUNT(*) as total FROM utilizadores WHERE nivel_acesso = 'membro'")->fetch_assoc()['total'];
$total_inscricoes = $conn->query("SELECT COUNT(*) as total FROM inscricoes")->fetch_assoc()['total'];
$total_mensagens = $conn->query("SELECT COUNT(*) as total FROM mensagens_contactos")->fetch_assoc()['total'];
$proximo_evento = $conn->query("SELECT titulo FROM eventos WHERE data_evento >= NOW() ORDER BY data_evento ASC LIMIT 1")->fetch_assoc();

$page_title = 'Painel de Administração - Borboleta Azul';
include 'includes/header.php';
?>

<div class="container">

    <div class="admin-header-box">
        <div class="admin-header-title">
            <h2>Painel de Gestão</h2>
            <p>Olá, <strong><?php echo $_SESSION['nome']; ?></strong>. O que deseja fazer hoje?</p>
        </div>

        <div class="admin-header-actions">
            <a href="admin_admins.php" class="btn btn-auto">👮 Gerir Admins</a>
            <a href="exportar_csv.php" class="btn btn-auto btn-green">📥 Exportar Excel (CSV)</a>
        </div>
    </div>

    <?php if(isset($mensagem_sucesso)) echo "<div class='alert-box alert-success'>✅ $mensagem_sucesso</div>"; ?>
    <?php if(isset($mensagem_erro)) echo "<div class='alert-box alert-error'>⚠️ $mensagem_erro</div>"; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3>Membros</h3>
                <p><?php echo $total_membros; ?></p>
            </div>
        </div>
        <div class="stat-card border-green">
            <div class="stat-icon">🎟️</div>
            <div class="stat-info">
                <h3>Inscrições</h3>
                <p><?php echo $total_inscricoes; ?></p>
            </div>
        </div>
        <div class="stat-card border-yellow">
            <div class="stat-icon">📩</div>
            <div class="stat-info">
                <h3>Mensagens</h3>
                <p><?php echo $total_mensagens; ?></p>
            </div>
        </div>
        <div class="stat-card border-purple">
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <h3>Próximo</h3>
                <p class="evento-titulo"><?php echo $proximo_evento ? htmlspecialchars($proximo_evento['titulo']) : "Nenhum"; ?></p>
            </div>
        </div>
    </div>

    <div class="admin-grid">
        
        <div class="form-box form-box-full">
            <h3 class="admin-section-title">📅 Publicar Novo Evento</h3>
            
            <form method="post" action="">
                <div class="form-row">
                    <div class="form-group flex-2">
                        <label>Título do Evento:</label>
                        <input type="text" name="titulo" required placeholder="Ex: Gala Solidária de Natal">
                    </div>
                    
                    <div class="form-group flex-1">
                        <label>Data e Hora:</label>
                        <input type="datetime-local" name="data_evento" required>
                    </div>
                    
                    <div class="form-group flex-1">
                        <label>Vagas:</label>
                        <input type="number" name="vagas_totais" required min="1" placeholder="50">
                    </div>
                </div>

                <div class="form-group">
                    <label>Descrição:</label>
                    <textarea name="descricao" rows="3" placeholder="Detalhes importantes do evento..."></textarea>
                </div>
                
                <button type="submit" name="criar_evento" class="btn">Criar Evento</button>
            </form>
        </div>

        <div>
            <h3 class="admin-section-title">📋 Eventos Criados</h3>
            
            <div class="tabela-responsiva">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Evento</th>
                            <th>Data</th>
                            <th>Ocupação</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_eventos = "SELECT * FROM eventos ORDER BY data_evento DESC";
                        $result = $conn->query($sql_eventos);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $data_formatada = date('d/m/Y H:i', strtotime($row['data_evento']));
                                $percentagem = ($row['vagas_totais'] > 0) ? round(($row['vagas_ocupadas'] / $row['vagas_totais']) * 100) : 0;
                                
                                $classe_estado = (strtotime($row['data_evento']) < time()) ? "text-red" : "text-green";
                                $texto_estado = (strtotime($row['data_evento']) < time()) ? "Terminado" : "Ativo";

                                echo "<tr>";
                                echo "<td class='td-id'>#" . $row['id'] . "</td>";
                                echo "<td><strong>" . htmlspecialchars($row['titulo']) . "</strong></td>";
                                echo "<td class='td-data'>" . $data_formatada . "</td>";
                                echo "<td>" . $row['vagas_ocupadas'] . " / " . $row['vagas_totais'] . " (" . $percentagem . "%)</td>";
                                echo "<td class='$classe_estado fw-bold'>" . $texto_estado . "</td>";
                                echo "<td>
                                        <a href='admin_painel.php?delete_id=".$row['id']."' 
                                           class='btn-delete-small'
                                           onclick=\"return confirm('ATENÇÃO: Isto apagará também todas as inscrições deste evento. Tem a certeza?');\">
                                           🗑️ Eliminar
                                        </a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='td-empty'>Ainda não criou nenhum evento.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h3 class="admin-section-title">📬 Mensagens de Contacto</h3>
            
            <div class="tabela-responsiva">
                <table>
                    <thead>
                        <tr>
                            <th class="th-escuro">Data</th>
                            <th class="th-escuro">Nome</th>
                            <th class="th-escuro">Contacto</th>
                            <th class="th-escuro">Assunto</th>
                            <th class="th-escuro th-msg-larga">Mensagem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_mensagens && $result_mensagens->num_rows > 0) {
                            while($msg = $result_mensagens->fetch_assoc()) {
                                $data_msg = date('d/m/Y H:i', strtotime($msg['data_envio']));
                                
                                echo "<tr>";
                                echo "<td class='td-data-msg'>" . $data_msg . "</td>";
                                echo "<td class='fw-bold'>" . htmlspecialchars($msg['nome']) . "</td>";
                                echo "<td><a href='mailto:" . htmlspecialchars($msg['email']) . "' class='link-msg'>" . htmlspecialchars($msg['email']) . "</a></td>";
                                echo "<td class='fw-bold'>" . htmlspecialchars($msg['assunto']) . "</td>";
                                echo "<td class='msg-preview'>" . nl2br(htmlspecialchars($msg['mensagem'])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='td-empty'>Nenhuma mensagem recebida ainda.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php
include 'includes/footer.php';
?>