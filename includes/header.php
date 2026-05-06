<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Fundação Borboleta Azul'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <?php if (isset($extra_css)) { echo $extra_css; } ?>
</head>
<body>

    <header>
        <div class="container">
            <a href="index.php" style="text-decoration:none; color:white;"><h1>Borboleta Azul 🦋</h1></a>
            <nav>
                <a href="index.php">Início</a>
                <a href="sobre.php">Sobre</a>
                <a href="contactos.php">Contactos</a>
                <a href="eventos.php">Eventos </a> <?php 
                // VERIFICAÇÃO: O utilizador está logado?
                if (isset($_SESSION['user_id'])): 
                ?>

                    <?php if (isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] === 'admin'): ?>
                        
                        <a href="admin_painel.php" class="btn-admin-nav">⚙️ Painel de Gestão</a>
                        <a href="logout.php">Sair</a>

                    <?php else: ?>

                        <a href="perfil.php" class="btn-perfil-nav">👤 Meu Cartão</a>
                        <a href="logout.php">Sair</a>
                        
                    <?php endif; ?>

                <?php 
                // CENÁRIO C: NÃO ESTÁ LOGADO (VISITANTE)
                else: 
                ?>
                    <a href="login.php">Entrar</a>
                    <a href="registo.php" class="btn-nav-destaque">Juntar-se</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>