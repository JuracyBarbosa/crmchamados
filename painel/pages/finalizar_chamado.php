<?
//Verifica access
checkAccessPage('abre_chamado', 's', 'denied');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $chamado = select::seleciona('chamados', 'idchamado = ?', array($id));
} else {
    painel::alert('erro', 'Você precisa passar o parametro ID.');
    die();
}

$buscaandamento = select::selected('chamados_andamento', 'seq_pla_chamado = ' . $id . '');
foreach ($buscaandamento as $key => $resultandamento) {
    $idatendimento = $resultandamento['seq_pla_atendimento'];
    $idoperadorantigo = $resultandamento['seq_pla_operador'];
    $status_registro = $resultandamento['status_registro'];
}

//pega ultimo operador que não é prestador de serviço.
$query = "SELECT
	cm.seq_pla_atendimento 
FROM
	chamados_movimentacoes cm
	INNER JOIN chamados_andamento ca ON cm.seq_pla_atendimento = ca.seq_pla_atendimento 
WHERE
	cm.seq_pla_chamado = :idChamado
ORDER BY
	cm.data_movimentacao DESC 
	LIMIT 1;";

$parametros = [':idChamado' => $id];
$queryAt = select::selectJoin($query, $parametros);

// Verifica se há resultados antes de acessar índices
if (!empty($queryAt) && isset($queryAt[0]['seq_pla_atendimento'])) {
    $seq_pla_atendimento = $queryAt[0]['seq_pla_atendimento'] ?? NULL;
}

?>

<div class="box_content">
    <h2><i class="fa fa-pen"></i> Finalizar Chamado</h2>
    <div class="form_group edit-chamado">
        <div class="form-chamado">
            <label>Solicitante:</label>
            <span><?
                    $result = $chamado['id_user'];
                    $sql = select::selected('usuarios', 'iduser = ' . $result . '');
                    foreach ($sql as $key => $query) {
                        echo $query['nomeuser'];
                    } ?></span>
            <label>Departamento:</label>
            <span><?
                    $dp = $query['id_dept'];
                    $dpt = select::selected('departamento', 'iddept = ' . $dp . '');
                    foreach ($dpt as $key => $result) {
                        echo $result['nomedept'];
                    } ?>
            </span>
        </div>
        <div class="form-chamado">
            <label>Data Abertura:</label>
            <span><?
                    $data = date_create($chamado['data_abertura']);
                    echo date_format($data, "d/m/Y H:i"); ?>
            </span>
            <label>Prioridade:</label>
            <span>
                <? $prioridade = $chamado['id_prioridade'];
                $prioridade = select::selected('prioridade', 'idprioridade =' . $prioridade . '');
                foreach ($prioridade as $key => $resultprd) {
                    echo $resultprd['nomeprioridade'];
                }
                ?>
            </span>
        </div>
        <div class="form-chamado">
            <label>Atendente:</label>
            <span><? $atendente = $chamado['id_operator'];
                    $queryAt = select::selected('operadores', 'idoperator = ' . $atendente . '');
                    foreach ($queryAt as $key => $resultAt) {
                        echo $resultAt['surname'];
                    } ?></span>
        </div>
        <div class="form-chamado">
            <label>Descrição:</label>
            <span><? echo $chamado['descricao']; ?></span>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data">
        <?
        if (isset($_POST['acao'])) {
            try {
                $idchamado = $_POST['id'];
                $resposta = $_POST['resposta'];
                $status = $_POST['status'];
                @$nota = $_POST['nota'];
                $acao = 'finalizar';
                $solicitante = $_SESSION['iduser'];
                $oldStatus = $chamado['id_status'];

                if ($nota == '') {
                    painel::alert('aviso', 'Precisa ser informado a nota para esse atendimento');
                    exit;
                }
                $operacoes = [
                    [
                        'funcao' => 'insert::finalizachamado',
                        'parametros' => [$idchamado, $acao, $atendente, $resposta],
                    ],
                    [
                        'funcao' => 'update::finalizarchamado',
                        'parametros' => [$status, $nota, $idchamado],
                    ],
                    [
                        'funcao' => 'insert::movimentacoesChamados',
                        'parametros' => [
                            [
                                'seq_pla_chamado' => $idchamado,
                                'seq_pla_usuario_mov' => $solicitante,
                                'seq_pla_tipo_movimentacao' => 7,
                                'descricao_movimentacao' => 'Chamado finalizado',
                                'seq_pla_atendimento' => $seq_pla_atendimento,
                                'seq_pla_operador_atual' => $atendente,
                                'seq_pla_status_anterior' => $oldStatus,
                                'seq_pla_status_atual' => $status,
                            ],
                        ],
                    ],
                ];

                // Executa as operações dinâmicas
                $resultado = transacoes::operacaoDinamicaConjunta($operacoes);

                if (!$resultado) {
                    throw new Exception('Uma ou mais operações falharam. A transação foi revertida.');
                }
            } catch (Exception $e) {
                echo painel::alert('erro', 'Algo deu errado na troca: ' . $e->getMessage());
            }

            if (!is_array($resultado || in_array(false, $resultado, true))) {

                $dadosEnvio = select::dadosparaenvio($idchamado);
                foreach ($dadosEnvio as $key => $resultEmail) {
                    $mailSolicitante = $resultEmail['email'];
                    $mailNome = $resultEmail['solicitante'];
                    $IDmessage = $resultEmail['idmessage'];
                    $assunto = $resultEmail['descbreve'];
                    $descricao = $resultEmail['descricao'];

                    // Valida se o solicitante tem e-mail cadastrado.
                    if (!$mailSolicitante) {
                        alert::alertaEmailNulo($idchamado, 'finalizaChamado');
                    }

                    //Enviar e-mail
                    $mail = new Email();

                    $mail->addReplyTo('ti@sementestropical.com.br', 'Departamento de TI');
                    $mail->addCustomHeader('In-Reply-To', $IDmessage);
                    $mail->addAdress($mailSolicitante, $mailNome);
                    $mail->addAdressCC(EMAIL_TI, 'Departamento de TI');

                    $img = '../images/logo.png';
                    $mail->addEmbeddedImage($img, 'logo_ref');

                    $info = array(
                        'assunto' => $assunto,
                        'corpo' => parametros::assinaturach('finalizarchamado', $nota, $idchamado, NULL, NULL, NULL, $mailNome)
                    );

                    $mail->formatarEmail($info);

                    $enviaSolicitante = $mail->enviarEmail();

                    if ($nota < 3) {
                        //enviar e-mail de nota baixa.
                        $assunto = 'Chamado ' . $idchamado . ' com nota baixa!';

                        $mail->ClearAllRecipients();
                        $info = array('assunto' => $assunto, 'corpo' => parametros::assinaturach('notabaixa', $nota, $idchamado, NULL, NULL, NULL, $mailNome));
                        $mail->addAdress(EMAIL_COORDENADOR, 'Coordenador Dep. TI');
                        $mail->formatarEmail($info);

                        $enviadocoordenador = $mail->enviarEmail();
                    }
                    if ($enviaSolicitante) {
                        alert::alertaRespondeChamado($idchamado, $acao, null);
                    }
                }
            } else {
                alert::Error('Aconteceu algo de errado na finalização do chamado!');
            }
        }
        ?>
        <div>
            <textarea placeholder="Se possível, Gostariamos que nos desse um feedback sobre esse atendimento!" name="resposta"></textarea>
        </div>
        <div class="form-chamado">
            <label>Nota:</label>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-sad-tear" style="color: #FF4200;"></i><br /><input type="radio" name="nota" value="3" /><label></label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-meh" style="color: #ffa500;"></i><br /><input type="radio" name="nota" value="7" /><label></label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-grin-wide" style="color: #84D712;"></i><br /><input type="radio" name="nota" value="10" /><label></label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-grin-wink" style="color: #1CE800;"></i><br /><input type="radio" name="nota" value="10" /><label></label>
            </div>
            <div class="form-chamado-nota">
                <i class="nota-fc fa-regular fa-face-grin-stars" style="color: green;"></i><br /><input type="radio" name="nota" value="10" /><label></label>
            </div>
        </div>
        <div class="clear"></div>

        <div class="form_group btn-atualiza">
            <input type="hidden" name="id" value="<? echo $chamado['idchamado'] ?>" />
            <input type="hidden" name="status" value="5" />
            <input type="submit" name="acao" value="Finalizar" />
            <input class="btn-voltar" type="button" value="Voltar" onClick="GoBackWithRefresh();return false;">
        </div>
    </form>
</div>