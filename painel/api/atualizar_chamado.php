<?php
header('Content-Type: application/json');

require_once '../../classes/painel.php';
require_once __DIR__ . '/../../config.php';
session_start();

try {
    // Recebe os dados enviados no corpo da requisição
    $input = json_decode(file_get_contents('php://input'), true);

    // Valida os dados recebidos
    if (!isset($input['idchamado'], $input['categoria'], $input['subcategoria'], $input['ocorrencia'], $input['prioridade'])) {
        throw new Exception('Dados incompletos.');
    }

    $result = update::ajustaChamados($input['idchamado'],$input['categoria'],$input['subcategoria'],$input['ocorrencia'],$input['prioridade']);

    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
