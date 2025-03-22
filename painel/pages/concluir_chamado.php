<?
//Verifica access
checkaccesspageoperator('denied');

if (isset($_GET['ch'])) {
    $id = (int)$_GET['ch'];
    $chamado = select::seleciona('chamados', 'idchamado = ?', array($id));
} else {
    painel::alert('erro', 'Você precisa passar o parametro ID.');
    die();
}

$buscaandamento = select::select('chamados_andamento ca', "ca.seq_pla_chamado = {$id} ORDER BY ca.seq_pla_atendimento DESC LIMIT 1");
$seq_pla_atendimento = NULL;
if (!empty($buscaandamento)) {
    foreach ($buscaandamento as $key => $resultandamento) {
        $seq_pla_atendimento = $resultandamento['seq_pla_atendimento'];
        break; // Sair do loop após obter o primeiro valor
    }
}

//pega seq_pla do atendente logado
$seq_pla_operador = $_SESSION['iduser'];
$queryOp = select::selecione('operadores o', 'o.id_user = '.$seq_pla_operador.'');
foreach ($queryOp as $key => $resultOp) {
    $seq_pla_operador = $resultOp['idoperator'];
    $seq_pla_usuario = $resultOp['id_user'];
}
?>

<div class="box_content">
    <h2><i class="fa fa-pen"></i> Concluir Chamado</h2>
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
            <span><? $idOperador = $chamado['id_operator'];
			$query = select::selected('operadores','idoperator = '.$idOperador.'');
			foreach($query as $key => $result){
			echo $result['surname'];
            $idAtendente = $result['idoperator'];
            $oldStatus = $chamado['id_status'];
            $idAccess = $result['id_access'];
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
                $status = $_POST['status'];
                $acao = 'concluir';
        
                if (empty($resposta)) {
                    painel::alert('erro', 'Você precisa dizer seu feedback final!');
                    return;
                }
        
                // Definir as operações para a função dinâmica
                $operacoes = [
                    [
                        'funcao' => 'insert::finalizaChamado',
                        'parametros' => [$idchamado, $acao, $idAtendente, $resposta],
                    ],
                    [
                        'funcao' => 'update::concluirchamado',
                        'parametros' => [$status, $idchamado],
                    ],
                    [
                        'funcao' => 'update::fechaAtendimento',
                        'parametros' => ['', 'F', $seq_pla_atendimento],
                    ],
                    [
                        'funcao' => 'insert::movimentacoesChamados',
                        'parametros' => [
                            [
                                'seq_pla_chamado' => $idchamado,
                                'seq_pla_operador_mov' => $seq_pla_operador,
                                'seq_pla_tipo_movimentacao' => 6,
                                'descricao_movimentacao' => 'Chamado concluído',
                                'seq_pla_atendimento' => $seq_pla_atendimento,
                                'seq_pla_operador_atual' => $idAtendente,
                                'seq_pla_status_anterior' => $oldStatus,
                                'seq_pla_status_atual' => $status
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

            if (!is_array($resultado)) {
                painel::alert('erro', 'Erro: Resultado inválido da transação.');
                error_log("Erro: O resultado da transação não é um array. Valor recebido: " . json_encode($resultado));
            } elseif (in_array(false, $resultado, true)) {
                painel::alert('erro', 'Erro: Uma ou mais operações falharam.');
                error_log("Erro: Uma ou mais operações falharam. Resultado: " . json_encode($resultado));
            } else {
                painel::alert('sucesso', 'Sucesso: Transação concluída com sucesso!');
                error_log("Sucesso: Todas as operações foram concluídas com sucesso. Resultado: " . json_encode($resultado));
            
                // Envio de e-mail
                try {
                    // Determinar função para e-mail
                    $funcao = ($idAccess == 3) ? 'gestor' : 'atendente';
            
                    // Preparando dados para enviar e-mail
                    $dadosEnvio = select::dadosparaenvio($idchamado);
                    foreach ($dadosEnvio as $resultEmail) {
                        $mailSolicitante = $resultEmail['email'];
                        $mailNome = $resultEmail['solicitante'];
                        $IDmessage = $resultEmail['idmessage'];
                        $assunto = $resultEmail['descbreve'];
                        $descricao = $resultEmail['descricao'];
            
                        // Valida se o solicitante tem e-mail cadastrados
                        if (!$mailSolicitante) {
                            alert::alertaEmailNulo($idchamado, 'concluirChamado');
                            error_log("Erro: Solicitante do chamado {$idchamado} não possui e-mail cadastrado.");
                            continue; // Pula para o próximo destinatário, se existir
                        }
            
                        // Configuração do e-mail
                        $mail = new Email();
            
                        $mail->addReplyTo('ti@sementestropical.com.br', 'Departamento de TI');
                        $mail->addCustomHeader('In-Reply-To', $IDmessage);
                        $mail->addAdress($mailSolicitante, $mailNome);
                        $mail->addAdressCC(EMAIL_TI, 'TI');
            
                        $img = '../images/logo.png';
                        $mail->addEmbeddedImage($img, 'logo_ref');
            
                        $info = array(
                            'assunto' => $assunto,
                            'corpo' => parametros::assinaturach('concluirchamado', null, $idchamado, null, null, null, null),
                        );
            
                        $mail->formatarEmail($info);
            
                        // Envia o e-mail
                        $envioSolicitante = $mail->enviarEmail();
            
                        if ($envioSolicitante) {
                            alert::alertaRespondeChamado($idchamado, $acao, $funcao);
                            error_log("Sucesso: E-mail enviado para {$mailNome} (Chamado: {$idchamado}).");
                        } else {
                            error_log("Falha ao enviar o e-mail para {$mailNome} (Chamado: {$idchamado}).");
                            painel::alert('erro', 'Chamado concluído, mas houve um problema ao enviar o e-mail.');
                        }
                    }
                } catch (Exception $e) {
                    error_log("Erro ao enviar e-mail após a conclusão: " . $e->getMessage());
                    painel::alert('erro', 'Chamado finalizado, mas houve um problema ao enviar o e-mail.');
                }
            }            
        }
        ?>
        <div>
            <textarea name="resposta"></textarea>
        </div>
        <div class="form_group btn-atualiza">
            <input type="hidden" name="id" value="<? echo $chamado['idchamado'] ?>" />
            <input type="hidden" name="status" value="4" />
            <input type="submit" name="acao" value="Concluir" />
            <input class="btn-voltar" type="button" value="Voltar" onClick="GoBackWithRefresh();return false;">
        </div>
    </form>
</div>