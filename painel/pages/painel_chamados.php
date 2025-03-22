<?
//verifica access
/* if(isset($_GET['atendente'])){
    $operador = $_GET['atendente'];
    ('id_access',$operador);
} */

$sts = @$_POST['status'];
$idoperador = $_SESSION['iduser'];

$pegaoperador = consulta::operador($idoperador, '3');
foreach($pegaoperador as $key => $resultop)
$operador = $resultop['idoperator'];
@$accesoperador = $resultop['id_access'];

$accesoperador == 1 ? $accesoperador = 3 : '';

$sts == 'atendendo' ? $chamados = chamado::selectchamados('chamados', 3, @$operador) : $chamados = chamado::selectchamados('chamados', $sts, null);
$row = count($chamados);

if($accesoperador == 3 or 1){
	$funcao = 'gestor';
}else{
	$funcao = 'atendente';
}

?>

<div class="box_content">
	<h2><i class="fas fa-list"></i>&nbsp; Painel gerencial de Atendimentos</h2>
	
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
			$selectstatus = select::selected('status', 'idstatus = '.$sts.'');
			foreach($selectstatus as $key => $resultstatus)
			painel::alert('sucesso', 'Sem chamados '.$resultstatus['nomestatus'].' no momento!');
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

				//Botão atender, responder chamado
				$atender = "<input type='submit' name='atender' value='Atender' />";
				$responder = "<span><a href='" . INCLUDE_PATH_PAINEL . "responder_solicitante?id=" . @$value['idchamado'] . "'>Responder</a></span>";
				$concluir = "<span><a href='" . INCLUDE_PATH_PAINEL . "concluir_chamado?id=" . @$value['idchamado'] . "'>Concluir</a></span>";
				$atribuir = "<span><a href='" . INCLUDE_PATH_PAINEL . "atribuir_chamado?id=" . @$value['idchamado'] . "'>Atribuir</a></span>";

				//Mostrar quantidade de tempo corrido
				/* $dataInicio = date_create($value['data_abertura']);
				$dataAtual = new DateTime(date('Y-m-d H:i:s'));
				
				$diff = $dataInicio->diff($dataAtual); */
				
				//designação dos botões atender, responder, concluir
				if ($sts == '1') {
					$btnchamado = $atender;
					$btnatribuir = $atribuir;
				}
				if ($sts == '2') {
					$btnatribuir = $atribuir;
				}
				if ($sts == '2' && $atendente == $operador){
					$btnchamado = $atender;
				}
				if ($sts == '3' && $atendente == $operador) {
					$btnchamado = $responder;
					$btnconcluir = $concluir;
				}
				

				$data = date_create($value['data_abertura']);
				
				echo
				"
				<div>
				<div class='grade_chamado form_group'>
					<div class='contagem-horas-chamado'>
					";
					//echo $diff->format('%a Dias %H:%I');
					echo "
					</div>
					<div>
					<label>Solicitante: </label>" . $resultuser['nomeuser'] . " <label> &nbsp Departamento: </label>".$resultdep['nomedept']." <label> &nbsp Data: </label>" . date_format($data, "d/m/Y H:i") . " <label>&nbsp Nº Solicitação: </label>" . $value['idchamado'] . "
					</div>
					<div>
					<label>Status: </label>" . $resultst['nomestatus'] . " <label> &nbsp Prioridade: </label>" . $resultp['nomeprioridade'] . "";
				if (!empty($value['id_operator'])) {
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
					<form style='margin-top: 36px;' action='" . INCLUDE_PATH_PAINEL . "requisicao' method='post' enctype='multipart/form-data' />
					<input type='hidden' name='idchamado' value=" . $value['idchamado'] . " />
					<input type='hidden' name='solicitante' value=".$resultuser['iduser']." />
					<input type='hidden' name='descricao' value=".$value['descricao']." />
					<input type='hidden' name='status' value='3' />
					<input type='hidden' name='atendente' value=".$operador." />
					<input type='hidden' name='funcao' value=".$funcao." />
					" . @$btnchamado . "&emsp;".@$btnconcluir."&emsp;".@$btnatribuir."
					</form>
					";
					
					//query historico
					$historicoh = select::selected('historico_chamados', 'id_chamado =' . $idchamado . ' ORDER BY data_movimentacao DESC');
				foreach ($historicoh as $key => $resulth) {
					$datah = date_create($resulth['data_movimentacao']);

					if ($value['id_status'] == 3 || 4) {

						if ($resulth['resposta_atendente'] > '0') {

							echo "
								<div class='grade-ch-resposta-at'>
								";
							echo "<div>
								<label>Atendente:</label> " . $resultat['surname'] . " &nbsp <label>Data:</label> " . date_format($datah, "d/m/Y") . "&nbsp as " . date_format($datah, "H:i:s") . "
								</div>
								<label>Respondeu:</label> " . $resulth['resposta_atendente'] . "";

							echo "</div>";
						}


						if ($resulth['resposta_solicitante'] > '0') {
							echo "
							
							<div class='grade-ch-resposta-so'>
							";
							echo "<div>
							<label>Solicitante:</label> " . $resultuser['nomeuser'] . " &nbsp <label>Data:</label> " . date_format($datah, "d/m/Y") . "&nbsp as " . date_format($datah, "H:i:s") . "
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