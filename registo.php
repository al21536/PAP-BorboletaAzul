<?php
session_start(); 
include 'config/db.php';
include 'assets/libs/phpqrcode/qrlib.php'; 

if (isset($_SESSION['user_id'])) {
    header("Location: perfil.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $data_nascimento = $conn->real_escape_string($_POST['data_nascimento']); 
    
    $senha_plana = $_POST['senha'];

    $uppercase = preg_match('@[A-Z]@', $senha_plana); 
    $lowercase = preg_match('@[a-z]@', $senha_plana); 
    $number    = preg_match('@[0-9]@', $senha_plana); 
    $specialChars = preg_match('@[^\w]@', $senha_plana); 

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($senha_plana) < 8) {
        $erro = "A senha deve ter pelo menos 8 caracteres, incluir uma letra maiúscula, um número e um caractere especial.";
    } else {
        
        $senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);

        $checkEmail = $conn->query("SELECT id FROM utilizadores WHERE email='$email'");
        
        if ($checkEmail->num_rows == 0) {
            
            $sql = "INSERT INTO utilizadores (nome, email, data_nascimento, senha) VALUES ('$nome', '$email', '$data_nascimento', '$senha_hash')";
            
            if ($conn->query($sql) === TRUE) {
                $last_id = $conn->insert_id;

                $conteudoQR = "MEMBRO-" . $last_id . "-" . md5($email); 
                
                if (!file_exists('qrcodes')) {
                    mkdir('qrcodes', 0777, true);
                }

                $caminhoFicheiro = "qrcodes/qr_" . $last_id . ".png";
                QRcode::png($conteudoQR, $caminhoFicheiro, QR_ECLEVEL_L, 4, 4);

                $conn->query("UPDATE utilizadores SET qrcode_path='$caminhoFicheiro' WHERE id=$last_id");

                $sucesso = "Registo efetuado com sucesso! Já pode fazer login.";
            } else {
                $erro = "Erro na base de dados: " . $conn->error;
            }
        } else {
            $erro = "Este email já está registado.";
        }
    }
}

$page_title = 'Registo - Fundação Borboleta Azul';
include 'includes/header.php';
?>

<div class="container" style="padding-bottom: 40px;">
    <div class="form-box" style="max-width: 500px; margin: 40px auto;">
        <h2 style="text-align: center; margin-bottom: 20px; color: var(--azul-escuro);">Criar Conta</h2>
        
        <?php 
        if(isset($erro)) {
            echo "<div style='color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; border-radius: 5px; text-align: center; margin-bottom: 20px; font-weight: bold;'>⚠️ $erro</div>";
        }
        if(isset($sucesso)) {
            echo "<div style='color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; text-align: center; margin-bottom: 20px; font-weight: bold;'>✅ $sucesso</div>";
        }
        ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label>Nome Completo:</label>
                <input type="text" name="nome" required placeholder="Ex: Maria Silva">
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required placeholder="seu@email.com">
            </div>
            
            <div class="form-group">
                <label>Data de Nascimento:</label>
                <input type="date" name="data_nascimento" required>
            </div>

            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="senha" required minlength="8" placeholder="Crie uma senha forte">
                
                <small style="color: #666; display: block; margin-top: 5px; font-size: 0.85rem;">
                    🔒 Pelo menos 8 caracteres, 1 maiúscula, 1 número e 1 caractere especial (ex: !@#$%).
                </small>
            </div>
            
            <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Registar Membro</button>
        </form>
        <p style="text-align: center; margin-top: 20px; color: #555;">
            Já tem conta? <a href="login.php" style="color: var(--azul-principal); font-weight: bold;">Faça Login</a>
        </p>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
