<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'admin') {
    die("Acesso negado.");
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=inscricoes_borboleta_azul.csv');

$output = fopen('php://output', 'w');

fputcsv($output, array('ID Inscricao', 'Nome Membro', 'Email', 'Evento', 'Data Evento', 'Data Inscricao'));

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

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit;
?>
