<?php
session_start();
header('Content-Type: application/json');

// Simula autenticação e recuperação do usuário logado
try {
    // Verifica se existe uma sessão ativa com a chave do usuário
    if (isset($_SESSION['iduser']) && isset($_SESSION['loginuser'])) {
        echo json_encode([
            "status" => "success",
            "usuario" => [
                "id" => $_SESSION['iduser'],      // ID do usuário logado
                "nome" => $_SESSION['loginuser']  // Nome do usuário logado
            ]
        ]);
    } else {
        // Caso não exista sessão ativa, retorna erro
        echo json_encode([
            "status" => "error",
            "message" => "Usuário não está logado ou sessão expirada."
        ]);
    }
} catch (Exception $e) {
    // Tratamento de erros no servidor
    echo json_encode([
        "status" => "error",
        "message" => "Erro ao buscar usuário logado: " . $e->getMessage()
    ]);
}
exit;
?>
