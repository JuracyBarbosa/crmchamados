<?
//Verifica access
checkAccessPage('editasite','s','denied');

if(isset($_GET['id'])){
	$id = (int)$_GET['id'];
	$servico = select::seleciona('tbl_site_servicos','id = ?',array($id));
}else{
	painel::alert('erro','Você precisa passar o parametro ID.');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa fa-pen"></i> Editar Serviços</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
			if(update::array($_POST)){
				alert::UpdateServico($_POST['id']);
				$servico = select::seleciona('tbl_site_servicos','id = ?',array($id));
			}else{
				painel::alert('erro','Campos vazios não são permitidos!');
			}
		}
		?>
		<div class="form_group">
			<label>servico:</label>
			<textarea name="servico"><? echo $servico['servico']; ?></textarea>
		</div>
		<div class="form_group">
			<? $data = date('Y-m-d H:i:s') ?>
			<input type="hidden" name="data" value="<? echo $data ?>" />
		</div>
		
		<div class="form_group">
			<input type="hidden" name="id" value="<? echo $servico['id']; ?>" />
			<input type="hidden" name="namecampoid" value="id" />
			<input type="hidden" name="nome_tabela" value="tbl_site_servicos" />
			<input type="submit" name="acao" value="Atualizar" />
		</div>
	</form>
</div>