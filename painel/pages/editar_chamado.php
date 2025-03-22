<?
//Verifica access
checkAccessPage('keyuser','s','denied');

if (isset($_GET['id'])) {
	$id = (int)$_GET['id'];
	$chamado = select::seleciona('chamados', 'idchamado = ?', array($id));
} else {
	painel::alert('erro', 'Você precisa passar o parametro ID.');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa-sharp fa-solid fa-pen-to-square"></i> Responder Chamado</h2>
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
			<span><? $atendente = $chamado['id_atendente'];
			$atendente = select::selected('usuarios','iduser = '.$atendente.'');
			foreach($atendente as $key => $atendente){
			echo $atendente['nomeuser'];
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
			
			if ($_POST['resposta'] == '') {
				painel::alert('erro', 'Não pode ficar vazio a resposta.!');
			} else {
				//Verificação de quem responde
				if($_SESSION['id_access'] == 1){
					$acao = 'resposta_atendente';
					$resposta1 = $_POST['resposta'];
					$resposta2 = '';
				}elseif ($_SESSION['id_access'] == 2) {
					$acao = 'resposta_solicitante';
					$resposta1 = '';
					$resposta2 = $_POST['resposta'];
			}
				insert::movimentacaochamado($id, $resposta1, $resposta2, null);
				alert::alertaRespondeChamado($id, $acao, null);
			}
		}
		?>
		<div>
			<textarea name="resposta"></textarea>
		</div>
		<div class="form_group btn-atualiza">
			<? $data = date('Y-m-d H:i:s') ?>
			<input type="hidden" name="datah" value="<? echo $data ?>" />
			<input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="GoBackWithRefresh();return false;">
		</div>
	</form>
</div>