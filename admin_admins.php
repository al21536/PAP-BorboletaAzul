<?php
session_start();
include 'config/db.php';

// 1. SEGURANÇA: Apenas Admins podem entrar aqui
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// 2. PROCESSAR A CRIAÇÃO DE NOVO ADMIN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografia obrigatória

    // Verifica se email já existe
    $check = $conn->query("SELECT id FROM utilizadores WHERE email='$email'");
    
    if ($check->num_rows == 0) {
        // Inserimos com nivel_acesso = 'admin' e qrcode_path a NULL (ou vazio)
        $sql = "INSERT INTO utilizadores (nome, email, senha, nivel_acesso) VALUES ('$nome', '$email', '$senha', 'admin')";
        
        if ($conn->query($sql) === TRUE) {
            $sucesso = "Novo administrador criado com sucesso!";
        } else {
            $erro = "Erro ao criar: " . $conn->error;
        }
    } else {
        $erro = "Esse email já está registado no sistema.";
    }
}

$page_title = 'Gerir Administradores - Borboleta Azul';
include 'includes/header.php';
?>

<div class="container">
    <div class="form-box" style="max-width: 800px;"> <a href="admin_painel.php" class="btn" style="width: auto; background-color: #6c757d; margin-bottom: 20px;">&larr; Voltar ao Painel de Eventos</a>

        <h2 style="text-align: center; margin-bottom: 20px; color: var(--azul-escuro);">Gerir Equipa Administrativa</h2>

        <?php if(isset($sucesso)) echo "<p style='color:green; text-align:center; font-weight:bold; background:#d4edda; padding:10px; border-radius:5px;'>$sucesso</p>"; ?>
        <?php if(isset($erro)) echo "<p style='color:red; text-align:center; font-weight:bold; background:#f8d7da; padding:10px; border-radius:5px;'>$erro</p>"; ?>

        <div style="display: flex; flex-wrap: wrap; gap: 40px; margin-top: 30px;">
            
            <div style="flex: 1; min-width: 300px;">
                <h3 style="border-bottom: 2px solid var(--azul-principal); padding-bottom: 10px; margin-bottom: 20px;">➕ Adicionar Novo Admin</h3>
                <form method="post" action="">
                    <div class="form-group">
                        <label>Nome:</label>
                        <input type="text" name="nome" required placeholder="Nome do colega">
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required placeholder="email@fundacao.pt">
                    </div>
                    <div class="form-group">
                        <label>Senha:</label>
                        <input type="password" name="senha" required placeholder="Senha segura">
                    </div>
                    <button type="submit" class="btn">Criar Administrador</button>
                </form>
            </div>

            <div style="flex: 1; min-width: 300px;">
                <h3 style="border-bottom: 2px solid var(--azul-principal); padding-bottom: 10px; margin-bottom: 20px;">👥 Administradores Atuais</h3>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--azul-claro);">
                            <th style="padding: 10px; text-align: left;">Nome</th>
                            <th style="padding: 10px; text-align: left;">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Buscar todos os admins
                        $sql_admins = "SELECT nome, email FROM utilizadores WHERE nivel_acesso = 'admin'";
                        $result_admins = $conn->query($sql_admins);

                        if ($result_admins->num_rows > 0) {
                            while($admin = $result_admins->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td style='padding: 10px; border-bottom: 1px solid #ddd;'>" . $admin['nome'] . "</td>";
                                echo "<td style='padding: 10px; border-bottom: 1px solid #ddd;'>" . $admin['email'] . "</td>";
                                echo "</tr>";
                            }
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