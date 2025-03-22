<?
//Verifica access
checkAccessPage('editaocorrencia','s','denied');

if(isset($_GET['id'])){
	$id = (int)$_GET['id'];

	$ocorrencia = select::seleciona('chamados_ocorrencia','seq_pla_ocorrencia = ?',array($id));
	$consulta = select::ligaocorrencia($id);
	$subcategorias_disponiveis = select::selected('chamados_subcategoria sc', 'sc.status_subcategoria = "A"');

	if($consulta){
		while($row = $consulta->fetch(PDO::FETCH_ASSOC)){
			$subcategorias_vinculadas[] = $row['seq_pla_subcategoria'];
		}
	}

	if ($subcategorias_disponiveis) {
		foreach ($subcategorias_disponiveis as $resultsubcategoria) {
			$id_subcategoria = $resultsubcategoria['seq_pla_subcategoria'];
			$nome_subcategoria = $resultsubcategoria['nome_subcategoria'];

			$subcategorias[] = array(
				'seq_pla_subcategoria' => $id_subcategoria,
				'nome_subcategoria' => $nome_subcategoria
			);
		}
	}

}else{
	painel::alert('erro','Você precisa passar o parametro ID.');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa fa-pen"></i> Editar Ocorrência</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if (isset($_POST['acao'])) {
			if (isset($_POST['subcategoria']) && is_array($_POST['subcategoria'])) {
				$nomeocorrencia = $_POST['nomeocorrencia'];
				$descocorrencia = $_POST['descocorrencia'];
				$statusocorrencia = $_POST['statusocorrencia'];
				$subcategorias_selecionadas = array_map('intval', $_POST['subcategoria']);

				if ($nomeocorrencia == '') {
					painel::alert('erro', 'Nome da ocorrência não pode retornar vazio!');
				} else
				if ($descocorrencia == '') {
					painel::alert('erro', 'A descrição não pode retornar vazia!');
				} else {
					$insert = update::ocorrencia($nomeocorrencia, $descocorrencia, $statusocorrencia, $id);
					
					if (!empty($subcategorias_selecionadas)) {
						delete::ligaocorrencia('ch_liga_ocor_cat_subcat', 'seq_pla_ocorrencia', $id);
						$insertliga = insert::ligaOcorCatSubcat($id, $subcategorias_selecionadas);
					}

					if ($insertliga == count($subcategorias_selecionadas)) {
						$insertliga = true;
					}

					$ocorrencia = select::seleciona('chamados_ocorrencia', 'seq_pla_ocorrencia = ?', array($id));

					if ($insert == true && $insertliga == true) {
						alert::alertaeditaocorrencia($nomeocorrencia);
					}
				}
			} else {
				painel::alert('aviso', 'Você não pode deixar de escolher uma subcategoria');
			}
		}
		?>
		<div class="form_group">
			<label>Nome da Ocorrência:</label>
			<input type="text" name="nomeocorrencia" value="<? echo $ocorrencia['nome_ocorrencia']; ?>">
		</div>
        <div class="form_group">
			<label>Observação:</label>
			<textarea name="descocorrencia"><? echo $ocorrencia['desc_ocorrencia']; ?></textarea>
		</div>
        <div class="form_group">
			<span>Status:</span>
			<input type="radio" name="statusocorrencia" value="A" <? echo ($ocorrencia['status_ocorrencia'] == 'A')?"checked":""; ?>/> Ativo
			<input type="radio" name="statusocorrencia" value="I" <?  echo ($ocorrencia['status_ocorrencia'] == 'I')?"checked":"";  ?>  /> Inativo
		</div>
		<br />
		<h2><i class="fa-solid fa-arrow-right-arrow-left"></i> Associar ocorrência</h2>
		<div class="form_group">
			<label>Sistemas</label>
			<ul class="form_group_checkbox">
				<?
				foreach ($subcategorias as $resultsub){
					$subcategorias_vinculadas = isset($subcategorias_vinculadas) ? $subcategorias_vinculadas : array();
					$checked = is_array($subcategorias_vinculadas) && in_array($resultsub['seq_pla_subcategoria'], $subcategorias_vinculadas) ? "checked" : "";

					echo '<li><input type="checkbox" name="subcategoria[]" value="' . $resultsub['seq_pla_subcategoria'] . '" ' . $checked . '> ' . $resultsub['nome_subcategoria'] . '</li>';
				}
				?>
			</ul>
		</div>
		
		<div class="form_group">
            <input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="history.go(-1)">
		</div>
	</form>
</div>