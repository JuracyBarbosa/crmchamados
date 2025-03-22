<?
//Verifica access
checkAccessPage('abre_chamado', 's', 'denied');

$sts = @$_POST['status'];
$usuario_id = $_SESSION['iduser'];
$chamados = select::meuschamados($sts, $usuario_id);
$row = count($chamados);

//Painel chamados
//seleciona chamados abertos
$chamadosAbertos = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_user = $usuario_id AND id_status = '1'");
$chamadosAbertos->execute();
$chamadosAbertos = $chamadosAbertos->rowCount();

//seleciona chamados encaminhados
$chamadosEncaminhados = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_user = $usuario_id AND id_status = '2'");
$chamadosEncaminhados->execute();
$chamadosEncaminhados = $chamadosEncaminhados->rowCount();

//seleciona chamados sendo atendidos
$chamadosEmatendimento = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_user = $usuario_id AND id_status = '3'");
$chamadosEmatendimento->execute();
$chamadosEmatendimento = $chamadosEmatendimento->rowCount();

//seleciona chamados concluidos
$chamadosConcluidos = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_user = $usuario_id AND id_status = '4'");
$chamadosConcluidos->execute();
$chamadosConcluidos = $chamadosConcluidos->rowCount();

//seleciona chamados finalizados
$chamadosFinalizados = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_user = $usuario_id AND id_status = '5'");
$chamadosFinalizados->execute();
$chamadosFinalizados = $chamadosFinalizados->rowCount();

//seleciona chamados cancelados
$chamadosCancelados = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_user = $usuario_id AND id_status = '6'");
$chamadosCancelados->execute();
$chamadosCancelados = $chamadosCancelados->rowCount();

//seleciona chamados aguardando retorno
$chamadosAguardando = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_user = $usuario_id AND id_status = '8'");
$chamadosAguardando->execute();
$chamadosAguardando = $chamadosAguardando->rowCount();

//seleciona chamados retornados
$chamadosRetornados = MySql::conectar()->prepare("SELECT * FROM chamados_retornados WHERE seq_pla_usuario = $usuario_id");
$chamadosRetornados->execute();
$chamadosRetornados = $chamadosRetornados->rowCount();


//realiza contagem total de chamados
$chamadostotal = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_user = $usuario_id");
$chamadostotal->execute();
$chamadostotal = $chamadostotal->rowCount();
//FIM

?>

<div class="box_content">
	<h2><i class="fa-solid fa-clipboard-list"></i>&nbsp; Meus chamados</h2>
	<div class="box_painel">
		<div class="box_painel_ch_single">
			<div class="box_painel_ch_wraper">
				<h2>Painel de chamados</h2>
				<div class="painel-list-chamados">
					<label>Abertos:</label>
					<span><? echo ($chamadosAbertos); ?></span>
				</div>
				<div class="painel-list-chamados">
					<label>Encaminhados:</label>
					<span><? echo ($chamadosEncaminhados); ?></span>
				</div>
				<div class="painel-list-chamados">
					<label>Sendo Atendido:</label>
					<span><? echo ($chamadosEmatendimento); ?></span>
				</div>
				<div class="painel-list-chamados">
					<label>Aguardando Retorno:</label>
					<span><? echo ($chamadosAguardando); ?></span>
				</div>
				<div class="painel-list-chamados">
					<label>Concluídos:</label>
					<span><? echo ($chamadosConcluidos); ?></span>
				</div>
				<div class="painel-list-chamados">
					<label>Finalizados:</label>
					<span><? echo ($chamadosFinalizados); ?></span>
				</div>
				<div class="painel-list-chamados">
					<label>Cancelados:</label>
					<span><? echo ($chamadosCancelados); ?></span>
				</div>
				<div class="painel-list-chamados">
					<label>Retornados:</label>
					<span><? echo ($chamadosRetornados); ?></span>
				</div>
				<div class="painel-list-chamados">
					<label>Total:</label>
					<span><? echo ($chamadostotal); ?></span>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="wraper_table ch-sts-btn">
		<form method="POST">
			<div class="btn-acao-ch-container">
				<button class="acao-btn" value="1" name="status">Abertos</button>
				<button class="acao-btn" value="3" name="status">Atendendo</button>
				<button class="acao-btn" value="8" name="status">Aguardando Retorno</button>
				<button class="acao-btn" value="2" name="status">Encaminhados</button>
				<button class="acao-btn" value="4" name="status">Concluido</button>
			</div>
		</form>

		<?
		if (!empty($sts == '')) {
			painel::alert('sucesso', 'Selecione o status!');
		} else if ($row == 0) {
			$stts = select::selected('status', 'idstatus = ' . $sts . '');
			foreach ($stts as $key => $resultSt)
				painel::alert('sucesso', 'Você não possui chamados ' . $resultSt['nomestatus'] . '');
		}
		if ($row > 0) {

			foreach ($chamados as $key => $value) {
				$usuario_id = $value['id_user'];
				$category = $value['id_categoria'];
				$prioridade = $value['id_prioridade'];
				$idchamado = $value['idchamado'];
				$sts = $value['id_status'];
				$atendente = $value['id_operator'];

				//Query user
				$solicitante = select::selected('usuarios', 'iduser = ' . $usuario_id . '');
				foreach ($solicitante as $key => $resultuser) {
					$sqldept = select::selected('departamento', 'iddept = ' . $resultuser['id_dept'] . '');
				}
				//result query user
				foreach ($sqldept as $key => $resultdep) {
				}
				//query atendente
				if (!empty($atendente == '')) {
				} else {
					$queryatendente = select::selected('operadores', 'idoperator = ' . $atendente . '');
					foreach ($queryatendente as $key => $resultat) {
					}
				}

				$files = select::useranexo($idchamado);
				$rowfiles = count($files);

				$categoria = select::selected('chamados_categoria', 'idcategoria = ' . $category . '');
				$prioridade = select::selected('prioridade', 'idprioridade = ' . $prioridade . '');
				$status = select::selected('status', 'idstatus = ' . $sts . '');

				foreach ($categoria as $key => $resultcat) {
				}
				foreach ($prioridade as $key => $resultp) {
				}
				foreach ($status as $key => $resultst) {
				}
				//Botão responder
				$responder = "<span><a href='" . INCLUDE_PATH_PAINEL . "responder_atendente?id=" . @$value['idchamado'] . "'>Responder</a></span>";
				$cancelar = "<span><a href='" . INCLUDE_PATH_PAINEL . "cancelar_chamado?id=" . @$value['idchamado'] . "'>Cancelar</a></span>";
				$finalizar = "<span><a href='" . INCLUDE_PATH_PAINEL . "finalizar_chamado?id=" . @$value['idchamado'] . "'>Finalizar</a></span>";
				$retornar = "<span><a href='" . INCLUDE_PATH_PAINEL . "retornar_chamado?id=" . @$value['idchamado'] . "'>Devolver</a></span>";

				if (@$_POST['status']) {
					$st = $_POST['status'];
					if ($_POST['status'] == '1') {
						$btncancelar = $cancelar;
					}
					if ($_POST['status'] == '2') {
						$btnresponder = $responder;
					}
					if ($_POST['status'] == '3') {
						$btnresponder = $responder;
					}
					if ($_POST['status'] == '4') {
						$btnfinalizar = $finalizar;
					}
					if ($_POST['status'] == '4') {
						$btnretorna = $retornar;
					}
					if ($_POST['status'] == '8') {
						$btnresponder = $responder;
					}
				}
				// FIM

				$data = date_create($value['data_abertura']);
				echo
				"<div>
				<div class='grade_chamado form_group'>
					<div>
					<label>Solicitante: </label>" . $resultuser['nomeuser'] . " <label> &nbsp Departamento: </label>" . $resultdep['nomedept'] . " <label> &nbsp Data: </label>" . date_format($data, "d/m/Y H:i") . " <label>&nbsp Nº Solicitação: </label>" . $value['idchamado'] . "
					</div>
					<div>
					<label>Status: </label>" . $resultst['nomestatus'] . " <label> &nbsp Prioridade: </label>" . $resultp['nomeprioridade'] . "";
				if (!empty($value['id_operator'])) {
					echo "<label> &nbsp Atendente: </label>" . $resultat['surname'] . "";
				}
				echo " <label> &nbsp Categoria: </label>" . $resultcat['nomecategoria'] . "";
				if (!empty($value['seq_pla_subcategoria'])) {
					echo "<label> &nbsp Subcategoria: </label>" . $value['subcategoria'] . "
						  <label> &nbsp Ocorrência: </label>" . $value['ocorrencia'] . "";
				}
				if (!empty($value['chexterno'])) {
					echo "<label> &nbsp Chamado Externo: </label>" . $value['chexterno'] . "";
				}
				echo "
					</div>
					<div>
						<label>Assunto: </label>" . $value['descbreve'] . "
					</div>
					<div align='center'>
					<label>Descrição do chamado</label>
					</div>
					<div class='descricao-chamado'>
					<span>" . $value['descricao'] . "</span>
					</div>
					";
				if ($rowfiles > 0) {
					echo "<div class='grade-ch-anexo'><label>Documentações anexas: </label>";
					foreach ($files as $key => $resultanexo) {
						$file = $resultanexo['nomeanexo'];
						$requester = $resultanexo['solicitante'];
						$operator = $resultanexo['operador'];

						if ($requester !== null) {
							echo "<a class='' target='_blank' href=" . ANEXO_CHAMADO . "" . $file . ">" . $requester . "</a> &emsp; ";
						}
						if ($operator !== null) {
							echo "<a class='' target='_blank' href=" . ANEXO_CHAMADO . "" . $file . ">" . $operator . "</a> &emsp; ";
						}
					}
					echo "</div>";
				}
				echo "
					<form action='" . INCLUDE_PATH_PAINEL . "requisicao' method='post' enctype='multipart/form-data' />
					<input type='hidden' name='id' value=" . $value['idchamado'] . " />
					<input type='hidden' name='status' value='3' />
					<input type='hidden' name='solicitante' value='" . $_SESSION['nomeuser'] . "' />
					<input type='hidden' name='nome_tabela' value='chamados' />
					" . @$btnresponder . "&emsp;" . @$btncancelar . "" . @$btnfinalizar . "&emsp;" . @$btnretorna . "
					</form>
					";

				//query historico
				$historicoh = select::selected('historico_chamados', 'id_chamado =' . $idchamado . ' ORDER BY data_movimentacao DESC');
				foreach ($historicoh as $key => $resulth) {
					$datah = date_create($resulth['data_movimentacao']);
					$pegaatendente = select::selected('operadores', 'idoperator = ' . $resulth['atendente'] . '');
					foreach ($pegaatendente as $key => $resultoperador)
						$attendant = $resultoperador['surname'];

					if ($value['id_status'] == 3 || 4) {
						if ($resulth['resposta_atendente'] > '0') {
							echo "
							<div class='grade-ch-resposta-at'>
							";
							echo "<div>
							<label>Atendente:</label> " . $attendant . " &nbsp <label>Data:</label> " . date_format($datah, "d/m/Y") . "&nbsp as " . date_format($datah, "H:i:s") . "
							</div>
								<label>Respondeu:</label> " . $resulth['resposta_atendente'] . "";

							echo "</div>";
						}

						if ($resulth['resposta_solicitante'] > '0') {
							echo "
							<div class='grade-ch-resposta-so'>
							";
							echo "<div>
						<label>Solicitante:</label> " . $resultuser['nomeuser'] . "  &nbsp <label>Data:</label> " . date_format($datah, "d/m/Y") . "&nbsp as " . date_format($datah, "H:i:s") . "
						</div>
								<label>Respondeu:</label> " . $resulth['resposta_solicitante'] . "";

							echo "</div>";
						}
					}
				}
				echo "
					
				</div>
			</div>";
			}
		}
		?>
	</div>
</div>