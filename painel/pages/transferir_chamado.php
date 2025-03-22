<?
session_start();
$user = $_SESSION['iduser'];
$pegaOperador = select::selecione('operadores o', 'o.id_user = '.$user.'');
foreach ($pegaOperador as $key => $value) {
    $seq_pla_operador_origem = $value['idoperator'];
    $seq_pla_operador = $value['idoperator'];
    $nomeOperador = $value['surname'];
}
//Verifica access
//checkAccessPageOperador('id_access', '4');

if (!isset($_GET['ch'])) {
    painel::alert('erro', 'Você precisa passar o parametro ID.');
    exit;
}
$seq_pla_chamado = intval($_GET['ch']); // Converte o ID para inteiro, prevenindo injeções

try {
    // Pega dados do chamado
    $dadosCh = select::selecione('chamados', "idchamado = {$seq_pla_chamado}");
    if ($dadosCh === false || empty($dadosCh)) {
        throw new Exception("Nenhum registro encontrado para o chamado com ID {$seq_pla_chamado}.");
    }

    // Verifica se os dados retornados são multidimensionais
    $chamadoData = is_array($dadosCh[0]) ? $dadosCh[0] : $dadosCh;

    // Extração segura de dados do chamado
    $idUser = $chamadoData['id_user'] ?? null;
    $idStatus = $chamadoData['id_status'] ?? null;

    if ($idUser === null || $idStatus === null) {
        throw new Exception("Campos do chamado estão incompletos. Dados recebidos: " . json_encode($chamadoData));
    }

    // Pega o andamento do chamado
    $andamentoCh = select::selecione('chamados_andamento ca', "ca.seq_pla_chamado = {$seq_pla_chamado} ORDER BY ca.seq_pla_atendimento DESC LIMIT 1");
    if ($andamentoCh === false || empty($andamentoCh)) {
        throw new Exception("Nenhum registro encontrado para o andamento do chamado: {$seq_pla_chamado}.<br />Será feito novo registro!");
    }

    // Acessa o primeiro elemento do array retornado
    $andamentoData = $andamentoCh[0];


    // Extração segura de dados do andamento
    $idatendimento = $andamentoData['seq_pla_atendimento'] ?? null;
    $idoperadorantigo = $andamentoData['seq_pla_operador'] ?? null;
    $status_registro = $andamentoData['status_registro'] ?? null;

    if ($idatendimento === null || $idoperadorantigo === null || $status_registro === null) {
        throw new Exception("Campos do andamento do chamado estão incompletos. Dados recebidos: " . json_encode($andamentoData));
    }

    // Código adicional pode ser adicionado aqui para manipular os dados obtidos

} catch (PDOException $e) {
    painel::alert('erro', 'Erro ao consultar o banco de dados: ' . $e->getMessage());
} catch (Exception $e) {
    if ($andamentoCh === false) {
        painel::alert('aviso', '' . $e->getMessage());
    } else {
        painel::alert('erro', 'Erro: ' . $e->getMessage());
    }
}

?>

<div class="box_content">
    <h2><i class="fa-solid fa-arrow-right-arrow-left"></i> Transferir Atendente</h2>
    <div class="form_group edit-chamado">
        <form method="post" enctype="multipart/form-data">
            <?
            if (isset($_POST['acao'])) {
                try {
                    // Validação dos dados do formulário
                    $atendente = verificar::valida_dados($_POST['atendente'] ?? '');
                    $status_registro = verificar::valida_dados($status_registro ?? '');

                    //Regra para status
                    $query = select::selecionar('operadores', 'idoperator = ?', [$atendente], 'o');

                    // Verifica os resultados
                    if (!empty($query)) {
                        $prestadorServico = $query[0]['prestador_servico'] ?? null;
                        $idStatus = ($prestadorServico === 'S') ? 2 : 3;
                        $seq_pla_atendente = $query[0]['id_user'];
                    } else {
                        $idStatus = 3; // Valor padrão caso a consulta não retorne resultados
                    }

                    // Verifica se o chamado está aberto
                    if ($status_registro === 'F') {
                        throw new Exception('O chamado precisa estar aberto para alteração de atendente!');
                    }

                    // Verifica se um atendente foi selecionado
                    if (empty($atendente)) {
                        throw new Exception('Você precisa escolher um atendente!');
                    }

                    // Conexão com o banco de dados
                    $pdo = MySql::conectar();

                    if ($andamentoCh === false) {
                        // Parâmetros para o INSERT no andamento
                        $paramsInsert = [$seq_pla_chamado, $seq_pla_operador_origem, $atendente, $idUser, $idStatus, 'A'];

                        // Parâmetros para o UPDATE do operador
                        $paramsUpdate = [$idStatus, $atendente, $seq_pla_chamado];

                        // Chamada da nova função de operação conjunta
                        $resultado = transacoes::novoAndamentoOperador($paramsInsert, $paramsUpdate);

                        if (!$resultado) {
                            throw new Exception("Erro ao registrar o andamento do chamado e atualizar o operador.");
                        }

                        alert::alertaAtribuirChamado($seq_pla_chamado); // Mensagem de sucesso
                    } else {
                        //inicia o idstatus anterior para registro
                        $statusAnterior = $chamadoData['id_status'] ?? null;
                        // Parâmetros para operacaoConjunta
                        $paramsInsert1 = [
                            'seq_pla_chamado' => $seq_pla_chamado,
                            'seq_pla_operador' => $seq_pla_operador_origem,
                            'seq_pla_operador_origem' => $idoperadorantigo,
                            'seq_pla_operador_destino' => $atendente
                        ];
                        $paramsUpdate1 = ['I', 'F', $idatendimento];
                        $paramsInsert2 = [$seq_pla_chamado, $atendente, $idUser, $idStatus, 'A'];
                        $paramsInsert3 = [
                            'seq_pla_chamado' => $seq_pla_chamado,
                            'seq_pla_operador_mov' => $seq_pla_operador_origem,
                            'seq_pla_tipo_movimentacao' => 2,
                            'descricao_movimentacao' => 'Chamado transferido para outro operador',
                            'seq_pla_operador_anterior' => $idoperadorantigo,
                            'seq_pla_operador_atual' => $atendente,
                            'seq_pla_status_anterior' => $statusAnterior,
                            'seq_pla_status_atual' => $idStatus
                        ];
                        $paramsUpdate2 = [$idStatus, $atendente, 'S', $seq_pla_chamado];

                        // Chamada da função operacaoConjunta
                        $resultado = transacoes::operacaoConjunta($paramsUpdate1, $paramsInsert2, $paramsInsert3, $paramsInsert1, $paramsUpdate2);

                        if (!$resultado) {
                            throw new Exception("Erro na troca de operador ou fechamento do atendimento.");
                        }

                        try {
                            $dadosEnvio = select::dadosparaenvio($seq_pla_chamado);
                            foreach ($dadosEnvio as $key => $resultEmail) {
                                $mailSolicitante = $resultEmail['email'];
                                $mailNome = $resultEmail['solicitante'];
                                $IDmessage = $resultEmail['idmessage'];
                                $assunto = $resultEmail['descbreve'];
                                $descricao = $resultEmail['descricao'];
                                $IDmessage = $resultEmail['idmessage'];
                                $assunto = $resultEmail['descbreve'];
                                $descricao = $resultEmail['descricao'];

                                // Pega dados para enviar e-mail para o prestador selecionado.
                                $buscaemailprestador = select::selected('usuarios', 'iduser = ' . $seq_pla_atendente . '');
                                foreach ($buscaemailprestador as $key => $pegaprestador) {
                                    $emailprestador = $pegaprestador['email'];
                                    $nomeprestador = $pegaprestador['loginuser'];
                                }

                                // Valida se o solicitante tem e-mail cadastrado.
                                if (!$mailSolicitante) {
                                    alert::alertaEmailNulo($seq_pla_chamado, 'encaminharSolicitante');
                                }

                                // Caso solicitante tenha e-mail, será enviado e-mail para o solicitante.
                                $mail = new Email();

                                $mail->addReplyTo(EMAIL_TI, 'Departamento de TI');
                                $mail->addCustomHeader('In-Reply-To', $IDmessage);
                                $mail->addAdress($mailSolicitante, $mailNome);
                                $mail->addAdressCC(EMAIL_TI, 'TI');

                                $img = '../images/logo.png';
                                $mail->addEmbeddedImage($img, 'logo_ref');

                                $info = array(
                                    'assunto' => $assunto,
                                    'corpo' => parametros::assinaturach('atribuirChamado', NULL, $seq_pla_chamado, NULL, NULL, $nomeOperador, NULL)
                                );
                                $mail->formatarEmail($info);

                                $envioSolicitante = $mail->enviarEmail();
                                //Fim do envio de email para solicitante

                                // Valida se o operador tem e-mail cadastrado.
                                if (!$emailprestador) {
                                    alert::alertaEmailNulo($seq_pla_chamado, 'encaminharPrestador');
                                }

                                // Caso o operador tenha e-mail, será enviado e-mail para ele.
                                $mail = new Email();

                                $mail->addReplyTo(EMAIL_TI, 'Departamento de TI');
                                $mail->addCustomHeader('In-Reply-To', $IDmessage);
                                $mail->addAdress($emailprestador, $nomeprestador);
                                $mail->addAdressCC(EMAIL_TI, 'TI');

                                $img = '../images/logo.png';
                                $mail->addEmbeddedImage($img, 'logo_ref');

                                $info = array(
                                    'assunto' => $assunto,
                                    'corpo' => parametros::assinaturach('atribuirChamado', NULL, $seq_pla_chamado, NULL, NULL, $nomeOperador, NULL)
                                );
                                $mail->formatarEmail($info);

                                $envioOperador = $mail->enviarEmail();

                                if ($envioSolicitante && $envioOperador) {
                                    alert::alertaAtribuirChamado($seq_pla_chamado);
                                }
                            }
                        } catch (Exception $e) {
                            echo painel::alert('erro', 'Algo deu errado no envio de e-mail: ' . $e->getMessage());
                        }
                    }
                } catch (Exception $e) {
                    echo painel::alert('erro', 'Algo deu errado na troca: ' . $e->getMessage());
                }
            }
            
            ?>
            <div class="form_group">
                <label>Operador:</label>
                <select name="atendente">
                    <option value=""></option>
                    <?
                    $atendente = select::atendente();
                    foreach ($atendente as $key => $result) {
                        echo '<option value="' . $result['idoperator'] . '">' . $result['surname'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form_group btn-atualiza">
                <input type="submit" name="acao" value="Trocar" />
                <input class="btn-voltar" type="button" value="Voltar" onClick="GoBackWithRefresh();return false;">
            </div>
        </form>
    </div>
</div>