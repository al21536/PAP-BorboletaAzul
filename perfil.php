<?php
session_start();
include 'config/db.php';

// 1. SEGURANÇA BÁSICA
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];

// --- LÓGICA DE CANCELAMENTO DE INSCRIÇÃO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancelar_inscricao'])) {
    $id_inscricao = (int)$_POST['id_inscricao'];
    $id_evento = (int)$_POST['id_evento'];
    
    // Verificar se a inscrição pertence mesmo a este utilizador (Segurança)
    $stmt_check = $conn->prepare("SELECT id FROM inscricoes WHERE id = ? AND id_utilizador = ?");
    $stmt_check->bind_param("ii", $id_inscricao, $id_user);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Apagar a inscrição
        $stmt_delete = $conn->prepare("DELETE FROM inscricoes WHERE id = ?");
        $stmt_delete->bind_param("i", $id_inscricao);
        
        if ($stmt_delete->execute()) {
            // Devolver a vaga ao evento
            $conn->query("UPDATE eventos SET vagas_ocupadas = vagas_ocupadas - 1 WHERE id = $id_evento");
            $mensagem_sucesso = "Inscrição cancelada com sucesso. Esperamos vê-lo num próximo evento!";
        } else {
            $mensagem_erro = "Ocorreu um erro ao cancelar a sua inscrição.";
        }
    } else {
        $mensagem_erro = "Ação não autorizada.";
    }
}

// --- LÓGICA DE ATUALIZAR PERFIL ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar_perfil'])) {
    $novo_nome = $_POST['nome'];
    $novo_email = $_POST['email'];
    $nova_pass = trim($_POST['nova_password']);
    $pode_atualizar = true; // Variável de controlo

    if (!empty($nova_pass)) {
        // --- REGRAS DE SEGURANÇA DA SENHA ---
        $uppercase = preg_match('@[A-Z]@', $nova_pass); // Pelo menos uma maiúscula
        $lowercase = preg_match('@[a-z]@', $nova_pass); // Pelo menos uma minúscula
        $number    = preg_match('@[0-9]@', $nova_pass); // Pelo menos um número
        $specialChars = preg_match('@[^\w]@', $nova_pass); // Pelo menos um caractere especial (!@#$%)

        // Verifica se cumpre todas as regras e tem pelo menos 8 caracteres
        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($nova_pass) < 8) {
            $mensagem_erro = "A palavra-passe deve ter pelo menos 8 caracteres, incluir uma maiúscula, uma minúscula, um número e um caractere especial.";
            $pode_atualizar = false;
        } else {
            // Atualiza com nova password (segura)
            $pass_hash = password_hash($nova_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE utilizadores SET nome = ?, email = ?, senha = ? WHERE id = ?");
            $stmt->bind_param("sssi", $novo_nome, $novo_email, $pass_hash, $id_user);
        }
    } else {
        // Atualiza apenas nome e email
        $stmt = $conn->prepare("UPDATE utilizadores SET nome = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $novo_nome, $novo_email, $id_user);
    }

    if ($pode_atualizar) {
        if ($stmt->execute()) {
            $_SESSION['nome'] = $novo_nome; // Atualiza a sessão
            $mensagem_sucesso = "Perfil atualizado com sucesso!";
        } else {
            $mensagem_erro = "Erro ao atualizar perfil. O email já pode estar em uso.";
        }
    }
}

// 2. BUSCAR DADOS DO UTILIZADOR
$sql_user = "SELECT * FROM utilizadores WHERE id = '$id_user'";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
} else {
    echo "Erro: Utilizador não encontrado.";
    exit;
}

// 3. BUSCAR AS INSCRIÇÕES DO MEMBRO
$sql_inscricoes = "SELECT i.id as id_inscricao, e.id as id_evento, e.titulo, e.data_evento 
                   FROM inscricoes i 
                   JOIN eventos e ON i.id_evento = e.id 
                   WHERE i.id_utilizador = '$id_user' 
                   ORDER BY e.data_evento DESC";
$result_inscricoes = $conn->query($sql_inscricoes);

$page_title = 'Meu Cartão - Borboleta Azul';
include 'includes/header.php';
?>

<div class="container" style="padding-bottom: 40px;">
    
    <div style="text-align: center; margin: 40px 0;">
        <h2 style="color: var(--azul-escuro); margin-bottom: 5px;">Olá, <?php echo htmlspecialchars($user['nome']); ?>!</h2>
        <p style="color: #666; font-size: 1.1rem;">Bem-vindo(a) à sua área reservada.</p>
    </div>

    <?php if(isset($mensagem_sucesso)): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; text-align: center; margin-bottom: 20px; font-weight: bold;">
            ✅ <?php echo $mensagem_sucesso; ?>
        </div>
    <?php endif; ?>
    <?php if(isset($mensagem_erro)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; text-align: center; margin-bottom: 20px; font-weight: bold;">
            ⚠️ <?php echo $mensagem_erro; ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start; justify-content: center;">
        
        <div style="flex: 1 1 400px; max-width: 500px;">
            <h3 style="text-align: left; margin-bottom: 15px; color: var(--azul-principal); border-bottom: 2px solid var(--azul-secundario); padding-bottom: 5px;">
                🆔 O Seu Cartão Digital
            </h3>
            
            <div class="cartao-membro" style="box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 10px; overflow: hidden; background: white;">
                
                <div class="cartao-header" style="background: var(--azul-principal); color: white; padding: 15px; text-align: center;">
                    <h3 style="margin: 0; font-size: 1.3rem;">Membro Borboleta Azul 🦋</h3>
                </div>
                
                <div class="cartao-body" style="padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap;">
                    
                    <div class="cartao-info" style="flex: 1; min-width: 150px;">
                        <p style="margin: 0 0 5px 0; font-size: 0.85rem; color: #777;">Nome do Membro</p>
                        <p style="margin: 0 0 15px 0; font-weight: bold; font-size: 1.1rem;"><?php echo htmlspecialchars($user['nome']); ?></p>
                        
                        <p style="margin: 0 0 5px 0; font-size: 0.85rem; color: #777;">Email</p>
                        <p style="margin: 0 0 15px 0; font-size: 0.95rem; word-break: break-all;"><?php echo htmlspecialchars($user['email']); ?></p>
                        
                        <p style="margin: 0 0 5px 0; font-size: 0.85rem; color: #777;">Nº de Membro</p>
                        <p style="margin: 0; font-weight: bold; font-size: 1.1rem; color: var(--azul-principal);">#<?php echo str_pad($user['id'], 6, '0', STR_PAD_LEFT); ?></p>
                    </div>
                    
                    <div class="cartao-qr" style="background: #f9f9f9; padding: 10px; border-radius: 8px; border: 1px solid #eee;">
                        <?php 
                        if (!empty($user['qrcode_path']) && file_exists($user['qrcode_path'])) {
                            echo "<img src='" . $user['qrcode_path'] . "' alt='QR Code' style='width: 120px; height: 120px; display: block;'>";
                        } else {
                            echo "<div style='width: 120px; height: 120px; display: flex; align-items: center; text-align: center; color: red; font-size: 0.8rem; border: 1px dashed red;'>QR Code não gerado</div>";
                        }
                        ?>
                    </div>
                </div>

                <div class="cartao-footer" style="background: #f1f1f1; padding: 10px; text-align: center; font-size: 0.85rem; color: #555; border-top: 1px solid #e0e0e0;">
                    Apresente este código nos eventos para check-in.
                </div>
            </div>

            <button onclick="abrirModalPerfil()" class="btn" style="width: 100%; margin-top: 20px; box-sizing: border-box; background-color: var(--azul-principal); color: var(--branco);">⚙️ Editar o Meu Perfil</button>
        </div>

        <div style="flex: 1 1 400px; max-width: 500px;">
            <h3 style="text-align: left; margin-bottom: 15px; color: var(--azul-principal); border-bottom: 2px solid var(--azul-secundario); padding-bottom: 5px;">
                📅 As Minhas Inscrições
            </h3>
            
            <div class="feature-card" style="text-align: left; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <?php if ($result_inscricoes->num_rows > 0): ?>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <?php while($evento = $result_inscricoes->fetch_assoc()): 
                            $data = date('d/m/Y', strtotime($evento['data_evento']));
                            $hora = date('H:i', strtotime($evento['data_evento']));
                            $passou = (strtotime($evento['data_evento']) < time());
                        ?>
                            <li style="border-bottom: 1px solid #eee; padding: 15px 0; display: flex; flex-direction: column;">
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                    <strong style="font-size: 1.05rem; color: #333;">
                                        <?php echo htmlspecialchars($evento['titulo']); ?>
                                    </strong>
                                    <?php if($passou): ?>
                                        <span style="background: #f8d7da; color: #721c24; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">Realizado</span>
                                    <?php else: ?>
                                        <span style="background: #d4edda; color: #155724; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">Confirmado</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.9rem; color: #666;">
                                        🕒 <?php echo $data . ' às ' . $hora; ?>
                                    </span>
                                    
                                    <?php if(!$passou): ?>
                                        <form method="POST" action="" onsubmit="return confirm('Tem a certeza que deseja cancelar a inscrição neste evento?');" style="margin: 0;">
                                            <input type="hidden" name="id_inscricao" value="<?php echo $evento['id_inscricao']; ?>">
                                            <input type="hidden" name="id_evento" value="<?php echo $evento['id_evento']; ?>">
                                            <button type="submit" name="cancelar_inscricao" class="btn-cancelar-pequeno">
                                                Cancelar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div style="text-align: center; padding: 30px 0; color: #888;">
                        <p style="font-size: 2rem; margin-bottom: 10px;">🎫</p>
                        <p>Ainda não se inscreveu em nenhum evento.</p>
                    </div>
                <?php endif; ?>

                <div style="text-align: center; margin-top: 25px;">
                    <a href="eventos.php" class="btn" style="width: 100%; display: block; box-sizing: border-box;">Ver Próximos Eventos</a>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="modalPerfil" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Gerir Perfil</h3>
            <button type="button" onclick="fecharModalPerfil()" class="btn-close-modal">&times;</button>
        </div>
        
        <form method="post" action="">
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Nome Completo:</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">E-mail:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Nova Palavra-passe (Opcional):</label>
                    <input type="password" name="nova_password" id="inputPass" onkeyup="validarSenha()" placeholder="Deixa em branco para manter a atual" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                    <div id="avisoSenha" style="font-size: 0.8rem; margin-top: 5px; color: #666;">
                        A senha deve ter pelo menos 8 caracteres, uma maiúscula, um número e um símbolo.
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="fecharModalPerfil()" class="btn-cinza">Cancelar</button>
                <button type="submit" name="atualizar_perfil" id="btnGuardar" class="btn">💾 Guardar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalPerfil() {
    document.getElementById('modalPerfil').style.display = 'flex';
}

function fecharModalPerfil() {
    document.getElementById('modalPerfil').style.display = 'none';
}

// Fechar automaticamente se o utilizador clicar fora da caixa branca
window.onclick = function(event) {
    var modal = document.getElementById('modalPerfil');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Validação da senha com as tuas regras rigorosas
function validarSenha() {
    var pass = document.getElementById('inputPass').value;
    var aviso = document.getElementById('avisoSenha');
    
    // Regras (Expressões Regulares no JavaScript)
    var temMaiuscula = /[A-Z]/.test(pass);
    var temMinuscula = /[a-z]/.test(pass);
    var temNumero = /[0-9]/.test(pass);
    var temEspecial = /[^\w]/.test(pass);
    var temTamanho = pass.length >= 8;

    if (pass.length === 0) {
        aviso.innerHTML = "A senha deve ter pelo menos 8 caracteres, uma maiúscula, um número e um símbolo.";
        aviso.style.color = "#666";
    } else if (!temTamanho || !temMaiuscula || !temMinuscula || !temNumero || !temEspecial) {
        aviso.innerHTML = "❌ Faltam requisitos! (Precisa de 8+ caracteres, Maiúscula, Minúscula, Número e Símbolo Especial)";
        aviso.style.color = "red";
    } else {
        aviso.innerHTML = "✅ Palavra-passe forte e válida!";
        aviso.style.color = "green";
    }
}
</script>

<?php include 'includes/footer.php'; ?>