<?php
require_once __DIR__ . '/../../config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

// Verifica se o usuário está autenticado
if (!isset($_SESSION['iduser'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
    exit;
}

// Consulta ao banco de dados
$iduser = $_SESSION['iduser'];
try {
    $row = consulta::operador($iduser);

    if ($row) {
        // Calcula `isGestor` com base no nível de acesso
        $atendente = [
            'idoperator' => $row['idoperator'],
            'nome' => $row['surname'],
            'isGestor' => $row['id_access'] == 3 // true se id_access for 3
        ];

        echo json_encode(['status' => 'success', 'atendente' => $atendente]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Atendente não encontrado.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
