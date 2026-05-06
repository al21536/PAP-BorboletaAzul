<?php
session_start();
include 'config/db.php';

// 1. SEGURANÇA: Apenas admin
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'admin') {
    die("Acesso negado.");
}

if (isset($_POST['importar'])) {
    $arquivo = $_FILES['ficheiro_csv']['tmp_name'];
    $extensao = pathinfo($_FILES['ficheiro_csv']['name'], PATHINFO_EXTENSION);

    // Validação de extensão
    if ($extensao != 'csv') {
        die("Erro: Por favor, carregue apenas ficheiros .csv");
    }

    if (($handle = fopen($arquivo, "r")) !== FALSE) {
        // Ignorar a primeira linha (cabeçalho) se existir
        fgetcsv($handle, 1000, ";"); 

        $sucesso = 0;
        $erros = 0;

        // Usar Prepared Statement para máxima segurança contra SQL Injection
        $stmt = $conn->prepare("INSERT INTO eventos (titulo, descricao, data_evento, vagas_totais) VALUES (?, ?, ?, ?)");

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            // Garantir que a linha tem os 4 campos necessários
            if (count($data) == 4) {
                $titulo = htmlspecialchars($data[0]);
                $descricao = htmlspecialchars($data[1]);
                $data_evento = $data[2]; // Formato esperado: YYYY-MM-DD HH:MM:SS
                $vagas = intval($data[3]);

                $stmt->bind_param("sssi", $titulo, $descricao, $data_evento, $vagas);
                
                if ($stmt->execute()) {
                    $sucesso++;
                } else {
                    $erros++;
                }
            }
        }
        fclose($handle);
        
        echo "<script>
                alert('Importação concluída! Sucesso: $sucesso | Erros: $erros');
                window.location = 'admin_painel.php';
              </script>";
    }
}
?>