<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['recuperar_user_id'])) {
    header("Location: recuperar_pass.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $senha_plana = $_POST['password'];

    $uppercase = preg_match('@[A-Z]@', $senha_plana);
    $lowercase = preg_match('@[a-z]@', $senha_plana);
    $number    = preg_match('@[0-9]@', $senha_plana);
    $specialChars = preg_match('@[^\w]@', $senha_plana);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($senha_plana) < 8) {
        $mensagem = "A senha deve ter pelo menos 8 caracteres, incluir uma letra maiúscula, um número e um caractere especial.";
    } else {

        $nova_pass = password_hash($senha_plana, PASSWORD_DEFAULT);
        $user_id = $_SESSION['recuperar_user_id'];

        $stmt = $conn->prepare("UPDATE utilizadores SET senha = ? WHERE id = ?");
        $stmt->bind_param("si", $nova_pass, $user_id);

        if ($stmt->execute()) {
            unset($_SESSION['recuperar_user_id']);
            echo "<script>alert('Password alterada com sucesso! Já pode fazer login.'); window.location='login.php';</script>";
            exit;
        } else {
            $mensagem = "Erro ao atualizar a password. Tente novamente.";
        }
    }
}

$page_title = "Definir Nova Password - Borboleta Azul";
include 'includes/header.php';
?>

<div class="container" style="padding-bottom: 40px;">
    <div class="form-box" style="max-width: 500px; margin: 40px auto;">
        <h2 style="text-align: center; color: var(--azul-escuro); margin-bottom: 10px;">Definir Nova Password</h2>
        <p style="text-align: center; margin-bottom: 20px; color: #666;">Escolha a sua nova palavra-passe segura.</p>

        <?php 
        if(!empty($mensagem)) {
            echo "<div style='color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; border-radius: 5px; text-align: center; margin-bottom: 20px; font-weight: bold;'>⚠️ $mensagem</div>";
        }
        ?>

        <form method="post">
            <div class="form-group">
                <label>Nova Senha:</label>
                <input type="password" name="password" required minlength="8" placeholder="Crie uma nova senha forte">
                
                <small style="color: #666; display: block; margin-top: 5px; font-size: 0.85rem;">
                    🔒 Pelo menos 8 caracteres, 1 maiúscula, 1 número e 1 caractere especial (ex: !@#$%).
                </small>
            </div>
            <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Atualizar Password</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
