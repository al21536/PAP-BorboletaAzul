<?php
session_start();
include 'config/db.php';

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 'admin') {
        header("Location: admin_painel.php");
    } else {
        header("Location: perfil.php");
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM utilizadores WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if (password_verify($senha, $row['senha'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nome'] = $row['nome'];
            $_SESSION['nivel_acesso'] = $row['nivel_acesso'];

            if ($row['nivel_acesso'] == 'admin') {
                header("Location: admin_painel.php");
            } else {
                header("Location: perfil.php");
            }
            exit;
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Email não encontrado.";
    }
}

$page_title = 'Login - Fundação Borboleta Azul';
include 'includes/header.php';
?>

<div class="container">
    <div class="form-box">
        <h2 style="text-align: center; margin-bottom: 20px;">Entrar na Área de Membro</h2>
        
        <?php if(isset($erro)) echo "<p style='color:red; text-align:center; font-weight:bold;'>$erro</p>"; ?>

        <form method="post" action="">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required placeholder="seu@email.com">
            </div>
            
            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="senha" required placeholder="******">
            </div>
            
            <button type="submit" class="btn">Entrar</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            Ainda não é membro? <a href="registo.php">Registe-se aqui</a>
        </p>
        <div style="text-align: center; margin-top: 15px;">
            <a href="recuperar_pass.php" style="color: #666; font-size: 0.9rem; text-decoration: none;">Esqueceu-se da password?</a>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
