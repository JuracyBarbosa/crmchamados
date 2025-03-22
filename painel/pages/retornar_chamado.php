<?
//Verifica access
checkAccessPage('abre_chamado','s','denied');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $chamado = select::seleciona('chamados', 'idchamado = ?', array($id));
    if (!$chamado) {
        painel::alert('erro', 'Nenhum chamado encontrado com o ID fornecido.');
        die();
    }
} else {
    painel::alert('erro', 'Você precisa passar o parametro ID.');
    die();
}

//pega ultimo operador que não é prestador de serviço.
$query = "(
    SELECT
        cm.seq_pla_chamado,
        cm.seq_pla_operador_atual 
    FROM
        chamados_movimentacoes cm
        INNER JOIN operadores o ON cm.seq_pla_operador_atual = o.idoperator 
    WHERE
        cm.seq_pla_chamado = :idChamado 
        AND o.prestador_servico != 'S' 
    ORDER BY
        cm.data_movimentacao DESC 
        LIMIT 1 
    ) UNION ALL
    (
    SELECT
        ca.seq_pla_chamado,
        ca.seq_pla_operador
    FROM
        chamados_andamento ca
        INNER JOIN operadores o ON ca.seq_pla_operador = o.idoperator 
    WHERE
        ca.seq_pla_chamado = :idChamado 
        AND o.prestador_servico != 'S' 
    ORDER BY
        ca.data_inicio DESC 
        LIMIT 1 
    ) UNION ALL
    (
    SELECT
        :idChamado AS seq_pla_chamado,-- Preenche o valor do WHERE
        o.idoperator AS seq_pla_operador_atual 
    FROM
        operadores o 
    WHERE
        o.prestador_servico != 'S' 
    ORDER BY
        RAND() 
        LIMIT 1 
    ) 
    LIMIT 1;";

$parametros = [':idChamado' => $id];
$queryOp = select::selectJoin($query, $parametros);

// Verifica se há resultados antes de acessar índices
if (!empty($queryOp) && isset($queryOp[0]['seq_pla_operador_atual'])) {
    $operadorAtual = $queryOp[0]['seq_pla_operador_atual'];
}
?>

<div class="box_content">
    <h2><i class="fa fa-pen"></i> Devolver chamado ao Atendente</h2>
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
			$atendente = select::selected('operadores','idoperator = '.$atendente.'');
			foreach($atendente as $key => $atendente){
			echo $atendente['surname'];
			}?></span>
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
                $arquivo = $_FILES['arquivo'];
                $status = $_POST['status'];
                $retorno = 'S';
                $solicitante = $_SESSION['iduser'];
                $atendente = $chamado['id_operator'];
                $oldStatus = $chamado['id_status'];
                $acao = 'devolverchamado';

                // pega categoria do chamado para registro de anexo.
                $pegaCategoria = $chamado['id_categoria'];
                $pegaCategoria = select::selected('chamados_categoria', 'idcategoria =' . $pegaCategoria . '');
                foreach ($pegaCategoria as $key => $categoria) {
                    $categoria = $categoria['nomecategoria'];
                }

                if ($resposta == '') {
                    painel::alert('erro', 'Não pode ficar o campo vazio!');
                    throw new Exception('Resposta não pode ser vazia.');
                }

                if (!chamado::imagemValida($arquivo, $categoria)) {
                    painel::alert(
                        'aviso',
                        'O tipo de anexo não é valido!<br /> Extenções permitidas <br />imagem .png, .jpg, .jpeg<br />Arquivos .pdf, .xls, xlsx, .docx, .txt<br /> e tem que ser menor que 2MB!<br />');
                } else {
                    // Definir as operações para a função dinâmica
                    $operacoes = [
                        [
                            'funcao' => 'insert::andamentoChamado',
                            'parametros' => [$idchamado, $operadorAtual, $solicitante, $status, 'A'],
                        ],
                        [
                            'funcao' => 'insert::devolveHistoricoChamado',
                            'parametros' => [$idchamado, $atendente, $resposta],
                        ],
                        [
                            'funcao' => 'update::devolveChamado',
                            'parametros' => [$status, $operadorAtual, $retorno, $idchamado],
                        ],
                        [
                            'funcao' => 'insert::movimentacoesChamados',
                            'parametros' => [
                                [
                                    'seq_pla_chamado' => $idchamado,
                                    'seq_pla_usuario_mov' => $solicitante,
                                    'seq_pla_tipo_movimentacao' => 4,
                                    'descricao_movimentacao' => 'Chamado retornado para o atendente',
                                    'seq_pla_atendimento' => '$0',
                                    'seq_pla_operador_anterior' => $atendente,
                                    'seq_pla_operador_atual' => $operadorAtual,
                                    'seq_pla_status_anterior' => $oldStatus,
                                    'seq_pla_status_atual' => $status,
                                ],
                            ],
                        ],
                    ];

                    // Executa as operações dinâmicas
                    $resultado = transacoes::operacaoDinamicaConjunta($operacoes);

                    if (!is_array($resultado) || in_array(false, $resultado, true)) {
                        throw new Exception('Uma ou mais operações falharam. A transação foi revertida.');
                    }

                    // Verificar se há arquivo enviado
                    if (isset($arquivo) && $arquivo['error'][0] === UPLOAD_ERR_OK) {
                        $anexo = chamado::uploadFiles($arquivo, $idchamado, $solicitante, null, 'devolverchamado');
                        if (!$anexo) {
                            error_log("Falha ao anexo arquivo: {$anexo}.");
                            throw new Exception('Falha ao anexar arquivo.');
                        }
                    }

                    // Pega dados para envio de e-mail
                    $dadosEnvio = select::dadosparaenvio($idchamado);
                    foreach ($dadosEnvio as $resultEmail) {
                        $mailSolicitante = $resultEmail['email'];
                        $mailNome = $resultEmail['solicitante'];
                        $IDmessage = $resultEmail['idmessage'];
                        $assunto = $resultEmail['descbreve'];
                        $descricao = $resultEmail['descricao'];

                        // Enviar e-mail
                        $mail = new Email();

                        //$mail->addReplyTo('ti@sementestropical.com.br', 'Departamento de TI');
                        $mail->addCustomHeader('In-Reply-To', $IDmessage);
                        $mail->addAdress($mailSolicitante, $mailNome);
                        $mail->addAdressCC(EMAIL_TI, 'TI');

                        $img = INCLUDE_PATH_PAINEL.'images/logo.png';
                        $mail->addEmbeddedImage($img, 'logo_ref');

                        $info = array(
                            'assunto' => $assunto,
                            'corpo' => parametros::assinaturach('concluirchamado', null, $idchamado, null, null, null, null)
                        );

                        $mail->formatarEmail($info);

                        if ($mail->enviarEmail()) {
                            alert::alertaRespondeChamado($idchamado, $acao, NULL);
                        } else {
                            alert::alertaRespondeChamado($idchamado, 'emailNuloParaRetorno', NULL);
                        }
                    }
                }
            } catch (Exception $e) {
                echo painel::alert('erro', 'Algo deu errado na troca: ' . $e->getMessage());
            }
        }
        ?>
        <div>
            <textarea name="resposta"></textarea>
        </div>
        <div class="form-chamado">
			<label>Imagem:</label>
			<input type="file" name="arquivo[]" multiple="multiple" />
		</div>
        <div class="form_group btn-atualiza">
            <input type="hidden" name="id" value="<? echo $chamado['idchamado'] ?>" />
            <input type="hidden" name="status" value="3" />
            <input type="submit" name="acao" value="Retornar" />
            <input class="btn-voltar" type="button" value="Voltar" onClick="GoBackWithRefresh();return false;">
        </div>
    </form>
</div>