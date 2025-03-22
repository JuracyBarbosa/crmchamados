<?
//Verifica access
checkAccessPage('editasubcategoria', 's', 'denied');

if (isset($_GET['id'])) {
	$id = (int)$_GET['id'];
	$subcategoria = select::seleciona('chamados_subcategoria', 'seq_pla_subcategoria = ?', array($id));
} else {
	painel::alert('erro', 'Você precisa passar o parametro ID.');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa fa-pen"></i> Editar Subcategoria</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if (isset($_POST['acao'])) {
			$nomesubcategoria = $_POST['nomesubcategoria'];
			$descsubcategoria = $_POST['descsubcategoria'];
			$status = $_POST['statussubcategoria'];
			$departamento = new update();

			if ($nomesubcategoria == '') {
				painel::alert('erro', 'Nome da subcategoria não pode retornar vazio!');
			} else
            if ($descsubcategoria == '') {
				painel::alert('erro', 'A descrição não pode retornar vazia!');
			}

			$verificarcadastrosubcategoria = consulta::cadastrosubcategoria($nomesubcategoria, $id);

			if ($verificarcadastrosubcategoria > 0) {
				Painel::alert('erro', 'A subcategoria <b>' . $nomesubcategoria . '</b> já existe no cadastro!') . '';

			} else {
				update::subcategoria($nomesubcategoria, $descsubcategoria, $status, $id);
			}
		}
		?>
		<div class="form_group">
			<label>Nome:</label>
			<input type="text" name="nomesubcategoria" value="<? echo $subcategoria['nome_subcategoria']; ?>">
		</div>
		<div class="form_group">
			<label>Descrição da Subcategoria:</label>
			<textarea name="descsubcategoria"><? echo $subcategoria['desc_subcategoria']; ?></textarea>
		</div>
		<div class="form_group_radio">
			<span>Status:</span>
			<input type="radio" name="statussubcategoria" value="A" <? echo ($subcategoria['status_subcategoria'] == 'A') ? "checked" : ""; ?> /> Sim
			<input type="radio" name="statussubcategoria" value="I" <? echo ($subcategoria['status_subcategoria'] == 'I') ? "checked" : ""; ?> /> Não
		</div>

		<div class="form_group">
			<input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="history.go(-1)">
		</div>
	</form>
</div>