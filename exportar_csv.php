<?php
session_start();
include 'config/db.php';

// Segurança: Apenas admin pode exportar
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'admin') {
    die("Acesso negado.");
}

// Definir cabeçalhos para download de ficheiro CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=inscricoes_borboleta_azul.csv');

// Abrir "saída" do PHP para escrita
$output = fopen('php://output', 'w');

// Adicionar a linha de cabeçalho no CSV (Excel vai ler isto)
fputcsv($output, array('ID Inscricao', 'Nome Membro', 'Email', 'Evento', 'Data Evento', 'Data Inscricao'));

// Query complexa (JOIN) para buscar dados de 3 tabelas: Inscricoes, Utilizadores e Eventos
$sql = "SELECT 
            i.id as id_inscricao,
            u.nome as nome_membro,
            u.email,
            e.titulo as nome_evento,
            e.data_evento,
            i.data_inscricao
        FROM inscricoes i
        JOIN utilizadores u ON i.id_utilizador = u.id
        JOIN eventos e ON i.id_evento = e.id
        ORDER BY e.data_evento DESC";

$result = $conn->query($sql);

// Escrever linhas no CSV
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit;
?>