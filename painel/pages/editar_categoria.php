<?
//Verifica access
checkAccessPage('editacategoria', 's', 'denied');

if (isset($_GET['id'])) {
	$id = (int)$_GET['id'];
	$categoria = select::seleciona('chamados_categoria', 'idcategoria = ?', array($id));
} else {
	painel::alert('erro', 'Você precisa passar o parametro ID.');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa fa-pen"></i> Editar Categoria</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if (isset($_POST['acao'])) {
			$nomecategoria = $_POST['nomecategoria'];
			$desccategoria = $_POST['desccategoria'];
			$status = $_POST['statuscategoria'];
			$departamento = new update();

			if ($nomecategoria == '') {
				painel::alert('erro', 'Nome da categoria não pode retornar vazio!');
			} else
            if ($desccategoria == '') {
				painel::alert('erro', 'A descrição não pode retornar vazia!');
			}

			$verificarcadastrocategoria = consulta::cadastrocategoria($nomecategoria, $id);

			if ($verificarcadastrocategoria > 0) {
				Painel::alert('erro', 'A categoria <b>' . $nomecategoria . '</b> já existe no cadastro!') . '';

			} else {
				$departamento = new update();
				$departamento->categoria($nomecategoria, $desccategoria, $status, $id);
			}
		}
		?>
		<div class="form_group">
			<label>Nome:</label>
			<input type="text" name="nomecategoria" value="<? echo $categoria['nomecategoria']; ?>">
		</div>
		<div class="form_group">
			<label>Descrição da Categoria:</label>
			<textarea name="desccategoria"><? echo $categoria['desccategoria']; ?></textarea>
		</div>
		<div class="form_group_radio">
			<span>Status:</span>
			<input type="radio" name="statuscategoria" value="A" <? echo ($categoria['status'] == 'A') ? "checked" : ""; ?> /> Sim
			<input type="radio" name="statuscategoria" value="I" <? echo ($categoria['status'] == 'I') ? "checked" : ""; ?> /> Não
		</div>

		<div class="form_group">
			<input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="history.go(-1)">
		</div>
	</form>
</div>