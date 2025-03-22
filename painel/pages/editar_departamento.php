<?
//Verifica access
checkAccessPage('editadept','s','denied');

if(isset($_GET['id'])){
	$id = (int)$_GET['id'];
	$dept = select::seleciona('departamento','iddept = ?',array($id));
}else{
	painel::alert('erro','Você precisa passar o parametro ID.');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa fa-pen"></i> Editar Departamento</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
            $nomedept = $_POST['nomedept'];
            $descdept = $_POST['descdept'];
            $departamento = new update();

            if($nomedept == ''){
                painel::alert('erro','Nome do departamento não pode retornar vazio!');
            }else
            if($descdept == ''){
                painel::alert('erro','A descrição não pode retornar vazia!');
            }else{
				$departamento = new update();
				$departamento->updateDept($nomedept, $descdept, $id);
			}

		}
		?>
		<div class="form_group">
			<label>Nome:</label>
			<input type="text" name="nomedept" value="<? echo $dept['nomedept']; ?>">
		</div>
        <div class="form_group">
			<label>Descrição:</label>
			<textarea name="descdept"><? echo $dept['descricaodept']; ?></textarea>
		</div>
		
		<div class="form_group">
            <input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="history.go(-1)">
		</div>
	</form>
</div>