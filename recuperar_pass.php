<?php
session_start();
include 'config/db.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $data_nasc = $_POST['data_nascimento'];

    // Verificar se o utilizador existe com esses dados exatos
    $stmt = $conn->prepare("SELECT id FROM utilizadores WHERE email = ? AND data_nascimento = ?");
    $stmt->bind_param("ss", $email, $data_nasc);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['recuperar_user_id'] = $user['id']; // Guarda o ID na sessão para o próximo passo
        header("Location: nova_pass.php");
        exit;
    } else {
        $mensagem = "<div style='color:red; margin-bottom:15px;'>Dados não encontrados. Verifique o email e a data de nascimento.</div>";
    }
}

$page_title = "Recuperar Password";
include 'includes/header.php';
?>

<div class="container">
    <div class="form-box">
        <h2>Recuperar Password</h2>
        <p style="margin-bottom: 20px; color: #666;">Introduza os seus dados para validar a sua identidade.</p>
        
        <?php echo $mensagem; ?>

        <form method="post">
            <div class="form-group">
                <label>Email de Registo:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Data de Nascimento:</label>
                <input type="date" name="data_nascimento" required>
            </div>
            <button type="submit" class="btn">Validar Identidade</button>
        </form>
        <br>
        <a href="login.php" style="display:block; text-align:center; color: var(--azul-principal);">Voltar ao Login</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>