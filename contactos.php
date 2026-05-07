<?php
session_start();
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $assunto = $conn->real_escape_string($_POST['assunto']);
    $mensagem = $conn->real_escape_string($_POST['mensagem']);

    $sql = "INSERT INTO mensagens_contactos (nome, email, assunto, mensagem) VALUES ('$nome', '$email', '$assunto', '$mensagem')";

    if ($conn->query($sql) === TRUE) {
        $sucesso = "Mensagem enviada com sucesso! Responderemos o mais breve possível.";
    } else {
        $erro = "Erro ao enviar a mensagem: " . $conn->error;
    }
}

$page_title = 'Contactos - Borboleta Azul';
include 'includes/header.php';
?>

<div class="container" style="padding: 60px 20px;">
    <h2 style="text-align: center; color: var(--azul-escuro); margin-bottom: 40px;">Fale Connosco</h2>

    <div style="display: flex; flex-wrap: wrap; gap: 40px;">
        
        <div class="form-box" style="flex: 2; margin: 0; max-width: 100%;">
            <h3 style="margin-bottom: 20px; color: var(--azul-principal);">Envie uma Mensagem</h3>
            
            <?php 
            if(isset($erro)) {
                echo "<p style='color:red; font-weight:bold; margin-bottom:15px;'>$erro</p>";
            }
            if(isset($sucesso)) {
                echo "<p style='color:green; font-weight:bold; margin-bottom:15px;'>✅ $sucesso</p>";
            }
            ?>

            <form action="" method="post">
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="form-group" style="flex: 1;">
                        <label>Seu Nome:</label>
                        <input type="text" name="nome" required placeholder="Nome Completo">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Seu Email:</label>
                        <input type="email" name="email" required placeholder="email@exemplo.com">
                    </div>
                </div>
                <div class="form-group">
                    <label>Assunto:</label>
                    <input type="text" name="assunto" required placeholder="Como podemos ajudar?">
                </div>
                <div class="form-group">
                    <label>Mensagem:</label>
                    <textarea name="mensagem" required rows="5" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;" placeholder="Escreva aqui a sua mensagem..."></textarea>
                </div>
                <button type="submit" class="btn">Enviar Mensagem</button>
            </form>
        </div>

        <div style="flex: 1; min-width: 280px;">
            <div class="feature-card" style="text-align: left; background: var(--azul-claro); margin-bottom: 20px;">
                <h3 style="color: var(--azul-escuro); margin-bottom: 20px;">Informações Adicionais</h3>
                
                <p style="margin-bottom: 15px;"><strong>📍 Morada:</strong><br>Av. da Liberdade, 123, 1250-001 Lisboa</p>
                <p style="margin-bottom: 15px;"><strong>📞 Telefone:</strong><br>+351 210 000 000</p>
                <p style="margin-bottom: 15px;"><strong>📧 Email:</strong><br>geral@borboletaazul.pt</p>
            </div>

            <div style="width: 100%; height: 300px; border-radius: 15px; overflow: hidden; box-shadow: var(--sombra);">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3113.116524855474!2d-9.14498302424424!3d38.718610571761664!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd1933783d73516f%3A0x690e8f0012e87c0a!2sAv.%20da%20Liberdade%2C%20Lisboa!5e0!3m2!1spt-PT!2spt!4v1700000000000!5m2!1spt-PT!2spt" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
