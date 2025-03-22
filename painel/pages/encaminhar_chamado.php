<?
session_start();
//pega parametro chamado
if (isset($_GET['ch'])) {
    $idchamado = (int)$_GET['ch'];
} else {
    painel::alert('erro', 'Você precisa passar o parametro ID.');
    exit;
}

//pega seq_pla do atendente logado
$seq_pla_operador = $_SESSION['iduser'];
$queryOp = select::selecione('operadores o', 'o.id_user = '.$seq_pla_operador.'');
foreach ($queryOp as $key => $resultOp) {
    $seq_pla_operador = $resultOp['idoperator'];
    $seq_pla_usuario = $resultOp['id_user'];
}

$queryCh = select::selected('chamados', 'idchamado = '.$idchamado.'');
foreach($queryCh as $key => $resultCh){
    $idsolicitante = $resultCh['id_user'];
    $seq_pla_status = $resultCh['id_status'];
    
    $querySo = select::selected('usuarios', 'iduser = '.$idsolicitante.'');
    foreach($querySo as $key => $resultSol){
        $nomeSolicitante = $resultSol['loginuser'];
    }
}

?>
<div class="box_content">
    <h2><i class="fa fa-pen"></i> Encaminhar Chamado</h2>
    <div class="form_group edit-chamado">
        <form method="post" enctype="multipart/form-data">
            <?
            if (isset($_POST['acao'])) {
                try {
                    // Validação dos dados do formulário
                    $atendente = verificar::valida_dados($_POST['atendente'] ?? '');
                    $observacao = verificar::valida_dados($_POST['observacao'] ?? '');

                    //Regra para status
                    $query = select::selecionar('operadores', 'idoperator = ?', [$atendente], 'o');

                    // Verifica os resultados
                    if (!empty($query)) {
                        $prestadorServico = $query[0]['prestador_servico'] ?? null;
                        $idStatus = ($prestadorServico === 'S') ? 2 : 3;
                        $nomeOperador = $query[0]['surname'] ?? null;
                        $seq_pla_atendente = $query[0]['id_user'];
                    } else {
                        $idStatus = 3; // Valor padrão caso a consulta não retorne resultados
                    }

                    // Verifica se um atendente foi selecionado
                    if (empty($atendente)) {
                        throw new Exception('Você precisa escolher um atendente!');
                        exit;
                    }

                    // Conexão com o banco de dados
                    $pdo = MySql::conectar();

                    // Define as operações que devem ser executadas dentro da transação
                    $operacoes = [
                        [
                            'funcao' => 'insert::andamentoChamado',
                            'parametros' => [$idchamado, $atendente, $idsolicitante, $idStatus, 'A'],
                        ],
                        [
                            'funcao' => 'insert::movimentacoesChamados',
                            'parametros' => [
                                [
                                    'seq_pla_chamado' => $idchamado,
                                    'seq_pla_operador_mov' => $seq_pla_operador,
                                    'seq_pla_tipo_movimentacao' => 1,
                                    'descricao_movimentacao' => 'Chamado encaminhado para atendimento',
                                    'seq_pla_atendimento' => '$0',
                                    'seq_pla_operador_atual' => $atendente,
                                    'seq_pla_status_anterior' => $seq_pla_status,
                                    'seq_pla_status_atual' => $idStatus
                                ],
                            ],
                        ],
                        [
                            'funcao' => 'update::alteraOperador',
                            'parametros' => [$idStatus, $atendente, null, $idchamado],
                        ],
                    ];


                    // Chama a função operacaoDinamicaConjunta com as operações
                    $resultado = transacoes::operacaoDinamicaConjunta($operacoes);

                    if (!$resultado) {
                        throw new Exception('Uma ou mais operações falharam. A transação foi revertida.');
                    }
                } catch (Exception $e) {
                    echo painel::alert('erro', 'Algo deu errado na troca: ' . $e->getMessage());
                }

                try {
                    $dadosEnvio = select::dadosparaenvio($idchamado);
                    foreach ($dadosEnvio as $key => $resultEmail) {
                        $mailSolicitante = $resultEmail['email'];
                        $mailNome = $resultEmail['solicitante'];
                        $IDmessage = $resultEmail['idmessage'];
                        $assunto = $resultEmail['descbreve'];
                        $descricao = $resultEmail['descricao'];
                        $assunto = $resultEmail['descbreve'];
                        $descricao = $resultEmail['descricao'];

                        // Pega dados para enviar e-mail para o prestador selecionado.
                        $buscaemailprestador = select::selected('usuarios', 'iduser = ' . $seq_pla_atendente . '');
                        foreach ($buscaemailprestador as $key => $pegaprestador) {
                            $emailprestador = $pegaprestador['email'];
                            $nomeprestador = $pegaprestador['loginuser'];
                        }

                        // Valida se o operador e solicitante tem e-mails cadastrados
                        if (!$mailSolicitante && !$emailprestador) {
                            alert::alertaEmailNulo($idchamado, 'encaminharSolOpe');
                        }

                        // Valida se o solicitante tem e-mail cadastrado.
                        if (!$mailSolicitante) {
                            alert::alertaEmailNulo($idchamado, 'encaminharSolicitante');
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
                            'corpo' => parametros::assinaturach('encaminharchamado', NULL, $idchamado, NULL, NULL, $nomeOperador, NULL)
                        );
                        $mail->formatarEmail($info);

                        $envioSolicitante = $mail->enviarEmail();
                        //Fim do envio de email para solicitante

                        // Valida se o operador tem e-mail cadastrado.
                        if (!$emailprestador) {
                            alert::alertaEmailNulo($idchamado, 'encaminharPrestador');
                        }
                        
                        // Caso o operador tenha e-mail, será enviado e-mail para ele.
                        $mail = new Email();

                        //pega os anexos do e-mail
                        $pegaAnexo = select::pegaAnexo($idchamado);
                        foreach ($pegaAnexo as $key => $resultanexo) {
                            $nameAnexo = $resultanexo['nomeanexo'];
                            $mail->addAttachment($nameAnexo);
                        }

                        $mail->addReplyTo(EMAIL_TI, 'Departamento de TI');
                        $mail->addCustomHeader('In-Reply-To', $IDmessage);
                        $mail->addAdress($emailprestador, $nomeprestador);
                        $mail->addAdressCC(EMAIL_TI, 'TI');

                        $img = '../images/logo.png';
                        $mail->addEmbeddedImage($img, 'logo_ref');

                        $info = array('assunto' => $assunto, 'corpo' => parametros::assinaturach('enviachamadoaoprestador', NULL, NULL, $descricao, $observacao, $nomeOperador, $nomeSolicitante));
                        $mail->formatarEmail($info);

                        $envioOperador = $mail->enviarEmail();
                        
                        if ($envioSolicitante && $envioOperador) {
                            alert::alertaRespondeChamado($idchamado, 'encaminhar', 'encaminhado');
                        }
                    }
                } catch (Exception $e) {
                    echo painel::alert('erro', 'Algo deu errado no envio de e-mail: ' . $e->getMessage());
                }
            }
            ?>
            <div class="form_group">
                <label>Usuário:</label>
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
            <div class="form_group">
                <label>Observação</label>
			    <textarea name="observacao"></textarea>
            </div>
            <div class="form_group btn-atualiza">
                <input type="submit" name="acao" value="Atribuir" />
                <input class="btn-voltar" type="button" value="Voltar" onClick="GoBackWithRefresh();return false;">
            </div>
        </form>
    </div>
</div>