<?php
session_start();
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // Recebe e valida os dados JSON
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['idchamado'], $data['atendente'], $data['acao']) || $data['acao'] !== 'atender') {
        throw new Exception('Parâmetros inválidos.');
    }

    $seq_pla_chamado = intval($data['idchamado']);
    $seq_pla_operador = $data['atendente'];
    $status = 3; // Status "Em atendimento" definido como 3

    // Verifica o usuário solicitante do chamado
    $pega_usuario = select::selected('chamados c', 'c.idchamado = '.$seq_pla_chamado.'');
    foreach($pega_usuario as $key => $result_usuario){
        $seq_pla_usuario = $result_usuario['id_user'];
        $seq_pla_status = $result_usuario['id_status'];
    }
    if (!$seq_pla_usuario) {
        echo json_encode(['status' => 'error', 'message' => 'usuário solicitante não encontrado.']);
        exit;
    }

    // Verifica se o chamado já está atendido
    $statusAtual = consulta::chamadoAtendido($seq_pla_chamado);
    if ($statusAtual != 1) {
        throw new Exception('Este chamado já está sendo atendido.', 400);
    }

    // Define as operações que devem ser executadas dentro da transação
    $operacoes = [
        [
            'funcao' => 'update::atendeChamado',
            'parametros' => [$seq_pla_chamado, $seq_pla_operador, $status],
        ],
        [
            'funcao' => 'insert::andamentoChamado',
            'parametros' => [$seq_pla_chamado, $seq_pla_operador, $seq_pla_usuario, $status, 'A'],
        ],
        [
            'funcao' => 'insert::movimentacoesChamados',
            'parametros' => [
                [
                    'seq_pla_chamado' => $seq_pla_chamado,
                    'seq_pla_operador_mov' => $seq_pla_operador,
                    'seq_pla_tipo_movimentacao' => 5,
                    'descricao_movimentacao' => 'Chamado iniciando o atendimento',
                    'seq_pla_atendimento' => '$1', // Referência ao resultado da operação anterior
                    'seq_pla_status_anterior' => $seq_pla_status,
                    'seq_pla_status_atual' => $status
                ],
            ],
        ],
    ];
    

    // Chama a função operacaoDinamicaConjunta com as operações
    $resultado = transacoes::operacaoDinamicaConjunta($operacoes);

    if (!$resultado) {
        throw new Exception('Uma ou mais operações falharam. A transação foi revertida.');
    }

    //Enviar e-mail
    $mail = new Email();

    $dadosEnvio = select::dadosparaenvio($seq_pla_chamado);
    foreach ($dadosEnvio as $key => $resultEmail) {
        $mailSolicitante = $resultEmail['email'];
        $mailNome = $resultEmail['solicitante'];
        $atendente = $resultEmail['atendente'];
        $IDmessage = $resultEmail['idmessage'];
        $assunto = $resultEmail['descbreve'];
        $descricao = $resultEmail['descricao'];
        $funcao = 'atendente';
        $acao = 'atender';

        if (empty($mailSolicitante)) {
            error_log("E-mail do solicitante está vazio. Não foi possível enviar.");
            echo json_encode([
                'status' => 'success',
                'emailnull' => 'sim',
                'message' => 'Chamado atendido, mas não enviou e-mail por falta do e-mail do solicitante, porém, registrado com sucesso.',
                'id' => $seq_pla_chamado
            ]);
            exit;
        }

        try {
            //$mail->addReplyTo('ti@sementestropical.com.br', 'Departamento de TI');
            $mail->addCustomHeader('In-Reply-To', $IDmessage);
            $mail->addAdress($mailSolicitante, $mailNome);
            $mail->addAdressCC(EMAIL_TI, 'TI');

            $img = '../images/logo.png';
            $mail->addEmbeddedImage($img, 'logo_ref');

            // Corpo do e-mail
            $info = array(
                'assunto' => $assunto,
                'corpo' => parametros::assinaturach('atender', NULL, NULL, NULL, NULL, $atendente, NULL)
            );
            $mail->formatarEmail($info);

            if (!$mail->enviarEmail()) {
                throw new Exception("Erro ao enviar e-mail para $mailSolicitante.");
                error_log("Erro ao enviar e-mail para: ". $e->getMessage());
            }
        } catch (Exception $e) {
            error_log("Erro no envio do e-mail: " . $e->getMessage());
        }
    }

    // Sucesso
    echo json_encode([
        'status' => 'success',
        'message' => 'Chamado atendido e realizado registro com sucesso.',
        'id' => $seq_pla_chamado
    ]);
    exit;

} catch (Exception $e) {
    // Garante uma única resposta de erro
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
    error_log("Erro no processamento do chamado: " . $e->getMessage());
    exit;
}
?>