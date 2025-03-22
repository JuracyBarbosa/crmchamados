<?
//Verifica access
checkAccessPage('editasite','s','denied');

$site = select::seleciona('tbl_site_config',false);
?>

<div class="box_content">
	<h2><i class="fa fa-pen"></i> Editar Configurações do Site</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
			if(update::site($_POST,true)){
				painel::alert('sucesso','Configurações editadas com sucesso!');
				$site = select::seleciona('tbl_site_config',false);
			}else{
				painel::alert('erro','Campos vazios não são permitidos!');
			}
		}
		?>
		<div class="form_group">
			<label>Titulo do site:</label>
			<input type="text" name="titulo" value="<? echo $site['titulo'] ?>" />
		</div>
		<div class="form_group">
			<label>Nome autor:</label>
			<input type="text" name="nome_autor" value="<? echo $site['nome_autor'] ?>" />
		</div>
		<div class="form_group">
			<label>Descrição do autor:</label>
			<textarea name="descricao_autor"><? echo $site['descricao_autor'] ?></textarea>
		</div>
		<?
		for($i = 1; $i <= 3; $i++){
		?>
		<div class="form_group">
			<label>Icone <? echo $i?>º:</label>
			<input type="text" name="icone<? echo $i; ?>" value="<? echo $site['icone'.$i] ?>" />
		</div>
		<div class="form_group">
			<label>Titulo <? echo $i?>º:</label>
			<input type="text" name="titulo<? echo $i; ?>" value="<? echo $site['titulo'.$i] ?>" />
		</div>
		<div class="form_group">
			<label>Descrição <? echo $i?>º:</label>
			<textarea name="descricao<? echo $i; ?>"><? echo $site['descricao'.$i] ?></textarea>
		</div>

		<?}?>
		<div class="form_group">
			<input type="hidden" name="nome_tabela" value="tbl_site_config" />
			<input type="submit" name="acao" value="Atualizar" />
		</div>
	</form>
</div>