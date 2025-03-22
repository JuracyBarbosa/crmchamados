<?
//Verifica access
// if(isset($_GET['atendente'])){
//     $operador = $_GET['atendente'];
//     ('id_access',$operador);
// }

$sts = @$_POST['status'];
$iduser = $_SESSION['iduser'];
$consult = consulta::operador($iduser, '4');
foreach($consult as $key => $result)
$operador = $result['idoperator'];

$chamados = chamado::selectchamados('chamados', $sts, 0);
$row = count($chamados);

//Painel chamados
//seleciona chamados abertos
$chamadosAbertos = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '1'");
$chamadosAbertos->execute();
$chamadosAbertos = $chamadosAbertos->rowCount();

//seleciona chamados encaminhados
$chamadosEncaminhados = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '2'");
$chamadosEncaminhados->execute();
$chamadosEncaminhados = $chamadosEncaminhados->rowCount();

//seleciona chamados sendo atendidos
$chamadosEmatendimento = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_operator = $operador AND id_status = '3'");
$chamadosEmatendimento->execute();
$chamadosEmatendimento = $chamadosEmatendimento->rowCount();

//seleciona chamados concluidos
$chamadosConcluidos = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '4'");
$chamadosConcluidos->execute();
$chamadosConcluidos = $chamadosConcluidos->rowCount();

//seleciona chamados finalizados
$chamadosFinalizados = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '5'");
$chamadosFinalizados->execute();
$chamadosFinalizados = $chamadosFinalizados->rowCount();

//seleciona chamados cancelados
$chamadosCancelados = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_status = '6'");
$chamadosCancelados->execute();
$chamadosCancelados = $chamadosCancelados->rowCount();

//seleciona chamados retornados
$chamadosRetornados = MySql::conectar()->prepare("SELECT * FROM chamados_retornados WHERE seq_pla_operador = $operador");
$chamadosRetornados->execute();
$chamadosRetornados = $chamadosRetornados->rowCount();


//realiza contagem total de chamados
$chamadostotal = MySql::conectar()->prepare("SELECT * FROM chamados WHERE id_operator = $operador");
$chamadostotal->execute();
$chamadostotal = $chamadostotal->rowCount();
//FIM

?>

<div class="box_content">
	<h2><i class="fa-solid fa-clipboard-list"></i>&nbsp; Painel do Atendente</h2>
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
			<button value="1" name="status">Abertos</button>
			<button value="2" name="status">Encaminhados</button>
			<button value="3" name="status">Em Atendimento</button>
			<button value="atendendo" name="status">Atendendo</button>
			<button value="4" name="status">Concluidos</button>
			<button value="7" name="status">Retornados</button>
		</form>

		<?
		if($sts == 'atendendo'){
			$sts = 3;
		}
		if (!empty($sts == '')) {
			painel::alert('sucesso', 'Selecione o status!');
		} else if ($row == 0) {
			$stts = select::selected('status', 'idstatus = '.$sts.'');
			foreach($stts as $key => $stats)
			painel::alert('sucesso', 'Você não possui chamados '.$stats['nomestatus'].'');
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
					$sqldept = select::selected('departamento','iddept = '.$resultuser['id_dept'].'');
				}
				//result query user
				foreach ($sqldept as $key => $resultdep) {
				}
				//query atendente
				if(!empty($atendente == '')){

				}else{
					$queryatendente = select::selected('operadores','idoperator = '.$atendente.'');
					foreach ($queryatendente as $key => $resultat) {
					}
				}

				$files =select::useranexo($idchamado);
				$rowfiles = count($files);

				$categoria = select::selected('chamados_categoria','idcategoria = '.$category.'');
				$prioridade = select::selected('prioridade', 'idprioridade = ' . $prioridade . '');
				$status = select::selected('status', 'idstatus = ' . $sts . '');

				foreach ($categoria as $key => $resultcat){
				}
				foreach ($prioridade as $key => $resultp){
				}
				foreach ($status as $key => $resultst){
				}
				//Botões do chamado
				$atender = "<input type='submit' name='atender' value='Atender' />";
				$responder = "<span><a href='" . INCLUDE_PATH_PAINEL . "responder_solicitante?id=" . @$value['idchamado'] . "'>Responder</a></span>";
				$concluir = "<span><a href='" . INCLUDE_PATH_PAINEL . "concluir_chamado?id=" . @$value['idchamado'] . "'>Concluir</a></span>";
				$repassar = "<span><a href='" . INCLUDE_PATH_PAINEL . "trocar_atendente?id=" . @$value['idchamado'] . "'>Repassar</a></span>";

				if (@$_POST['status']) {
					$st = $_POST['status'];
					if($_POST['status'] == '1' or '7'){
						if($_POST['status'] == '1'){
							$btnchamado = $atender;
						}
						if($_POST['status'] == '7'){
							$btnchamado = $atender;
						}
					}
					if($_POST['status'] == '3' && $atendente == $operador){
						$btnchamado = $responder;
						$btnchamado1 = $concluir;
					}
				}
				// FIM
				
				$data = date_create($value['data_abertura']);
				echo
				"<div>
				<div class='grade_chamado form_group'>
					<div>
					<label>Solicitante: </label>" . $resultuser['nomeuser'] . " <label> &nbsp Departamento: </label>".$resultdep['nomedept']." <label> &nbsp Data: </label>" . date_format($data, "d/m/Y H:i") . " <label>&nbsp Nº Solicitação: </label>" . $value['idchamado'] . "
					</div>
					<div>
					<label>Status: </label>" . $resultst['nomestatus'] . " <label> &nbsp Prioridade: </label>" . $resultp['nomeprioridade'] . "";
				if ($value['id_status'] == 3 && 4) {
					echo "<label> &nbsp Atendente: </label>" . $resultat['surname'] . "";
				}
				echo " <label> &nbsp Categoria: </label>" . $resultcat['nomecategoria'] . "
					";
				
				echo
				"
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
					foreach($files as $key => $resultanexo){
						$file = $resultanexo['nomeanexo'];
						$requester = $resultanexo['solicitante'];
						$operator = $resultanexo['operador'];

						if($requester !== null){
							echo "<a class='' target='_blank' href=" . ANEXO_CHAMADO . "" . $file . ">".$requester."</a> &emsp; ";
						}
						if($operator !== null){
							echo "<a class='' target='_blank' href=" . ANEXO_CHAMADO . "" . $file . ">".$operator."</a> &emsp; ";
						}
						
					}
					echo "</div>";	
				}
				echo "
					<form action='".INCLUDE_PATH_PAINEL."requisicao' method='post' enctype='multipart/form-data' />
					<input type='hidden' name='idchamado' value=" . $value['idchamado'] . " />
					<input type='hidden' name='status' value='3' />
					<input type='hidden' name='funcao' value='atendente' />
					<input type='hidden' name='atendente' value='".$operador."' />
					<input type='hidden' name='nome_tabela' value='chamados' />
					" . @$btnchamado . "&emsp;" .@$btnchamado1. "".@$btnconcluir."".@$btnrepassar."
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