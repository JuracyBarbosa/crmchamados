<?
//Verifica access a pagina
checkaccesspageoperator('denied');

if (isset($_GET['ch'])) {
	$idchamado = (int)$_GET['ch'];
	$chamado = select::seleciona('chamados', 'idchamado = ?', array($idchamado));
	$trocou_operador = $chamado['trocou_operador'];
	$setchexterno = select::seleciona('chamados_externo chex', 'chex.seq_pla_chamado = ?', array($idchamado));
	@$registrouChamado = $setchexterno['registrou_chamado'] ?? null;
	@$numeroChamadoExterno = $setchexterno['cod_chamado'];
} else {
	painel::alert('erro', 'Você precisa passar o parametro ID.');
	die();
}

//pega seq_pla do atendente logado
$seq_pla_operador = $_SESSION['iduser'];
$queryOp = select::selecione('operadores o', 'o.id_user = ' . $seq_pla_operador . '');
foreach ($queryOp as $key => $resultOp) {
	$seq_pla_operador = $resultOp['idoperator'];
	$seq_pla_usuario = $resultOp['id_user'];
}
?>

<div class="box_content">
	<h2><i class="fa-sharp fa-solid fa-pen-to-square"></i> Responder Chamado ao Solicitante</h2>
	<div class="form_group edit-chamado">
		<div class="form-chamado">
			<label>Solicitante:</label>
			<span><?
					$result = $chamado['id_user'];
					$sql = select::selected('usuarios', 'iduser = ' . $result . '');
					foreach ($sql as $key => $query) {
						echo $query['nomeuser'];
						$seq_pla_usuario = $query['iduser'];
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
			<label>Status:</label>
			<span>
				<? $status = $chamado['id_status'];
				$query = select::selected('status', 'idstatus =' . $status . '');
				foreach ($query as $key => $resultSt) {
					$seq_pla_status = $resultSt['idstatus'];
					echo $resultSt['nomestatus'];
				}
				?>
			</span>
		</div>
		<div class="form-chamado">
			<label>Prioridade:</label>
			<span>
				<? $prioridade = $chamado['id_prioridade'];
				$prioridade = select::selected('prioridade', 'idprioridade =' . $prioridade . '');
				foreach ($prioridade as $key => $resultprd) {
					$seq_pla_prioridade = $resultprd['idprioridade'];
					echo $resultprd['nomeprioridade'];
				}
				?>
			</span>
			<label>Atendente:</label>
			<span><? $atendente = $chamado['id_operator'];
					$atendente = select::selected('operadores', 'idoperator = ' . $atendente . '');
					foreach ($atendente as $key => $atendente) {
						$seq_pla_operador_origem = $atendente['idoperator'];
						echo $atendente['surname'];
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

			$arquivo = $_FILES['arquivo'];
			$solicitante = null;
			$resposta = $_POST['resposta'];
			$seq_pla_status = $_POST['status'] ?? null;
			$seq_pla_prioridade = $_POST['prioridade'];

			@$registro_chamado = $_POST['minhadiv'];
			@$seq_pla_chamado = $_POST['chexterno'];
			@$troca_operador = $_POST['divoperador'];
			@$trocaoperador = $_POST['trocaoperador'] ?? null;

			// Se trocaoperador for '2', força seq_pla_status para '2'
			if ($troca_operador == 'S') {
				$seq_pla_status = 2;
			}

			$pass = true;

			try {
				$pegaCategoria = $chamado['id_categoria'];
				$pegaCategoria = select::selected('chamados_categoria', 'idcategoria =' . $pegaCategoria . '');
				foreach ($pegaCategoria as $key => $categoria)
					$categoria = $categoria['nomecategoria'];

				if (empty($registro_chamado) && $setchexterno == false && $chamado['id_categoria'] == 2) {
					painel::alert('aviso', 'Selecione se registrou chamado externo!');
					$pass = false;
				} else {
					if ($registro_chamado == 'S' && $seq_pla_chamado == '' && $setchexterno == false) {
						painel::alert('erro', 'Você marcou que registrou um chamado externo, por favor, coloque o número do chamado registrado!');
						$pass = false;
					}
				}
				if (empty($troca_operador) && $setchexterno == false && $chamado['id_categoria'] == 2) {
					painel::alert('aviso', 'Selecione se deseja trocar de operador!');
					$pass = false;
				} else {
					if ($troca_operador == 'S' && $trocaoperador == '' && $setchexterno == false) {
						painel::alert('aviso', 'Você marcou que deseja trocar de operador mas não selecionou um dentre a lista!');
						$pass = false;
					}
				}
				if (chamado::imagemValida($arquivo, $categoria) == false) {
					painel::alert('erro', 'O tipo de anexo não é valido!<br> Verifique se é uma imagem .PNG, .JPG, .JPEG ou se é um arquivo .PDF e menor que 1.2MB');
				}
			} catch (Exception $e) {
				return $e->getMessage();
			}
			if ($pass == true) {
				//Verificação de quem responde
				$verificaoperador = select::operator($chamado['id_operator']);
				foreach ($verificaoperador as $key => $resultop) {
					$setoperador = $resultop['id_access'];

					if ($setoperador == 3 || 4) {
						$acao = 'resposta_atendente';
						$resposta1 = $_POST['resposta'];
						$resposta2 = '';
					}
				}

				if ($troca_operador == 'S') {
					$buscaandamento = select::select('chamados_andamento', 'seq_pla_chamado = ' . $idchamado . '');
					$seq_pla_atendimento = NULL;
					if (!empty($buscaandamento)) {
						foreach ($buscaandamento as $key => $resultandamento) {
							$seq_pla_atendimento = $resultandamento['seq_pla_atendimento'];
							break; // Sair do loop após obter o primeiro valor
						}
					}

					$operacoes = [
						[
							'funcao' => 'update::fechaAtendimento',
							'parametros' => ['', 'F', $seq_pla_atendimento],
						],
						[
							'funcao' => 'insert::andamentoChamado',
							'parametros' => [$idchamado, $trocaoperador, $seq_pla_usuario, $seq_pla_status, 'A'],
						],
						[
							'funcao' => 'insert::registraTrocaOperador',
							'parametros' => [
								[
									'seq_pla_chamado' => $idchamado,
									'seq_pla_atendimento' => '$1',
									'seq_pla_operador' => $seq_pla_operador,
									'seq_pla_operador_origem' => $seq_pla_operador_origem,
									'seq_pla_operador_destino' => $trocaoperador
								],
							],
						],
						[
							'funcao' => 'insert::movimentacoesChamados',
							'parametros' => [
								[
									'seq_pla_chamado' => $idchamado,
									'seq_pla_operador_mov' => $seq_pla_operador,
									'seq_pla_tipo_movimentacao' => null,
									'descricao_movimentacao' => 'Operador transferindo para outro',
									'seq_pla_atendimento' => '$1',
									'seq_pla_operador_anterior' => $seq_pla_operador,
									'seq_pla_operador_atual' => $trocaoperador,
									'seq_pla_status_anterior' => 3,
									'seq_pla_status_atual' => $seq_pla_status
								],
							],
						],
					];
					// Executa as operações dinâmicas
					$resultado = transacoes::operacaoDinamicaConjunta($operacoes);

					if (!$resultado) {
						throw new Exception('Uma ou mais operações falharam. A transação foi revertida.');
						error_log("Erro: resultado da operação: " . json_encode($resultado));
					}
				}

				insert::movimentacaochamado($idchamado, $atendente['idoperator'], $resposta1, $resposta2);
				if ($registrouChamado == 'N' || $registro_chamado == 'S') {
					update::chamadoExterno($seq_pla_chamado, $idchamado);
				}
				$dadosAtualizar = [
					'id_status' => $seq_pla_status,
					'id_prioridade' => $seq_pla_prioridade,
					'trocou_operador' => $troca_operador
				];
				update::atualizarChamado($dadosAtualizar, $idchamado);
				$nameAnexo = chamado::uploadFiles($arquivo, $idchamado, $solicitante, $atendente['idoperator'], $acao);
				if ($trocaoperador != '') {
					update::alteraOperador('2', $trocaoperador, null, $idchamado);
				}

				$dadosEnvio = select::dadosparaenvio($idchamado);
				foreach ($dadosEnvio as $key => $resultEmail) {
					$mailSolicitante = $resultEmail['email'];
					$mailNome = $resultEmail['solicitante'];
					$IDmessage = $resultEmail['idmessage'];
					$assunto = $resultEmail['descbreve'];
					$descricao = $resultEmail['descricao'];

					// Valida se o solicitante tem e-mail cadastrado.
					if (!$mailSolicitante) {
						alert::alertaEmailNulo($idchamado, 'responderSolicitante');
					}

					//Enviar e-mail
					$mail = new Email();

					$mail->addAttachment($nameAnexo);
					$mail->addReplyTo(EMAIL_TI, 'Departamento de TI');
					$mail->addCustomHeader('In-Reply-To', $IDmessage);
					$mail->addAdress($mailSolicitante, $mailNome);
					$mail->addAdressCC(EMAIL_TI, 'TI');

					$img = '../images/logo.png';
					$mail->addEmbeddedImage($img, 'logo_ref');

					$info = array(
						'assunto' => $assunto,
						'corpo' => parametros::assinaturach('respondersolicitante', NULL, $idchamado, NULL, NULL, NULL, NULL)
					);

					$mail->formatarEmail($info);

					$envioSolicitante = $mail->enviarEmail();

					if ($envioSolicitante) {
						if ($setoperador == 3) {
							$funcao = 'gestor';
						} else {
							$funcao = 'atendente';
						}
						alert::alertaRespondeChamado($idchamado, $acao, $funcao);
					}
				}
			}
		}
		?>
		<div>
			<textarea required name="resposta"></textarea>
		</div>
		<? if ($chamado['id_categoria'] == 2) { ?>
			<div class="form-group-chamado">
				<?php $checkedRegChS = ($registrouChamado === 'S') ? 'checked' : ''; ?>
				<?php $checkedRegChN = ($registrouChamado === 'N') ? 'checked' : ''; ?>
				<span>Registrou chamado</span>
				<ul class="form-chamado-radio">
					<li>
						<input type="radio" name="minhadiv" value="S" onclick="mostraDIV('registrachamado')" <?= $checkedRegChS ?> /><label>Sim</label>
					</li>
					<li>
						<input type="radio" name="minhadiv" value="N" onclick="ocultaDIV('registrachamado')" <?= $checkedRegChN ?> /> <label>Não</label>
					</li>
				</ul>
			</div>
			<?php $mostrarDiv = ($registrouChamado === 'S') ? 'block' : 'none'; ?>
			<div id="minhadiv" style="display: <?= $mostrarDiv ?>; padding: 20px;" class="form-group-chamado">
				<label>Número do chamado externo:</label>
				<?php $numeroChamadoExterno = isset($numeroChamadoExterno) ? htmlspecialchars($numeroChamadoExterno) : ''; ?>
				<input type="text" name="chexterno" maxlength="6" value="<?= $numeroChamadoExterno ?>" />
			</div>
			<div class="form-group-chamado">
				<?php $checkedTrocOpS = ($trocou_operador === 'S') ? 'checked' : ''; ?>
				<?php $checkedTrocOpN = ($trocou_operador === 'N') ? 'checked' : ''; ?>
				<span>Trocar de Operador</span>
				<ul class="form-chamado-radio">
					<li>
						<input type="radio" name="divoperador" value="S" onclick="mostraDIV('trocaroperador')" <?= $checkedTrocOpS ?>/><label>Sim</label>
					</li>
					<li>
						<input type="radio" name="divoperador" value="N" onclick="ocultaDIV('trocaroperador')" <?= $checkedTrocOpN ?>/><label>Não</label>
					</li>
				</ul>
			</div>
		<? } ?>
		<?php $mostrarDivOp = ($trocou_operador === 'S') ? 'block' : 'none'; ?>
		<div id="divoperador" style="display: <?= $mostrarDivOp ?>; padding: 20px;" class="form-group-chamado">
			<label>Operador:</label>
			<select name="trocaoperador">
				<option value=""></option>
				<?
				$atendente = select::atendente();
				foreach ($atendente as $key => $result) {
					echo '<option value="' . $result['idoperator'] . '">' . $result['surname'] . '</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group-chamado">
			<span>
				Status
			</span>
			<ul class="form-chamado-radio">
				<?php
				// Lista de status permitidos
				$statusPermitidos = [3, 8];
				$conn = MySql::conectar();

				// Consulta ao banco com prepared statements
				$placeholders = implode(',', array_fill(0, count($statusPermitidos), '?'));
				$sql = "SELECT idstatus, nomestatus FROM status WHERE idstatus IN ($placeholders)";
				$stmt = $conn->prepare($sql);
				$stmt->execute($statusPermitidos);

				// Renderização dinâmica dos rádios
				while ($row = $stmt->fetch()) {
					$checked = ((int)$row['idstatus'] === (int)$seq_pla_status) ? 'checked' : '';
					echo '<li><input type="radio" id="status_' . $row['idstatus'] . '" name="status" value="' . $row['idstatus'] . '"' . $checked . '> 
        			<label for="status_' . $row['idstatus'] . '">' . htmlspecialchars($row['nomestatus']) . '</label></li>';
				}
				?>
			</ul>
		</div>
		<div class="form-group-chamado">
			<span>
				Prioridade
			</span>
			<ul class="form-chamado-radio">
				<?php
				// Lista de prioridades permitidos (você pode editar essa lista dinamicamente)
				$prioridadePermitidos = [1, 2, 3];

				// Consulta ao banco
				$conn = MySql::conectar();
				$sql = "SELECT idprioridade, nomeprioridade FROM prioridade WHERE idprioridade IN ('" . implode("','", $prioridadePermitidos) . "')";
				$consultaPrioridade = $conn->query($sql);

				// Renderização dinâmica dos rádios
				while ($row = $consultaPrioridade->fetch()) {
					$checked = ($row['idprioridade'] === $seq_pla_prioridade) ? 'checked' : '';
					echo '<li><input type="radio" id="prioridade_' . $row['idprioridade'] . '" name="prioridade" value="' . $row['idprioridade'] . '"' . $checked . '> 
                  <label for="prioridade_' . $row['idprioridade'] . '">' . htmlspecialchars($row['nomeprioridade']) . '</label></li>';
				}
				?>
			</ul>
		</div>
		<div class="form-chamado">
			<label>Imagem:</label>
			<input type="file" name="arquivo[]" multiple="multiple" />
		</div>
		<div class="form_group btn-atualiza">
			<? $data = date('Y-m-d H:i:s') ?>
			<input type="hidden" name="datah" value="<? echo $data ?>" />
			<input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="GoBackWithRefresh();return false;">
		</div>
	</form>
</div>