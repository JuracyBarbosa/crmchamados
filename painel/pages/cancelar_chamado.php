<?
//Verifica access
checkAccessPage('abre_chamado', 's','denied');

if (isset($_GET['id'])) {
	$id = (int)$_GET['id'];
	$chamado = select::seleciona('chamados', 'idchamado = ?', array($id));
} else {
	painel::alert('erro', 'Você precisa passar o parametro ID.');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa-sharp fa-solid fa-pen-to-square"></i> Cancelar Chamado</h2>
	<div class="form_group edit-chamado">
		<div class="form-chamado">
			<label>ID chamado:</label>
			<span><?
					$sql = select::selected('chamados', 'idchamado = ' . $id . '');
					foreach ($sql as $key => $query) {
						echo $query['idchamado'];
					} ?></span>
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
			<label>Descrição:</label>
			<span><? echo $chamado['descricao']; ?></span>
		</div>
	</div>
	<form method="post" enctype="multipart/form-data">
		<?
		if (isset($_POST['acao'])) {
			$status = 6;
			$motivo = $_POST['motivo'];
			$acao = 'cancelar';
			$cancelado = 'S';

			if ($_POST['motivo'] == '') {
				painel::alert('erro', 'Não pode ficar vazio a resposta.!');
			}else{
				$update = update::cancelachamado($status, $cancelado, $motivo, $id);
				alert::alertaRespondeChamado($id, $acao, @$funcao);
			}
		}
		?>
		<div><h3>Motivo do cancelamento</h3>
			<textarea name="motivo"></textarea>
		</div>
		<div class="form_group btn-atualiza">
			<? $data = date('Y-m-d H:i:s') ?>
			<input type="hidden" name="datah" value="<? echo $data ?>" />
			<input type="submit" name="acao" value="Cancelar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="GoBackWithRefresh();return false;">
		</div>
	</form>
</div>