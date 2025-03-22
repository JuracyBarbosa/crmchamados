<?
//Verifica access
checkAccessPage('editamotivo','s','denied');

if(isset($_GET['id'])){
	$id = (int)$_GET['id'];
	$dept = select::seleciona('motivo_chamado','idmotivo = ?',array($id));
}else{
	painel::alert('erro','Você precisa passar o parametro ID.');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa fa-pen"></i> Editar Motivo</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
            $nomemotivo = $_POST['nomemotivo'];
            $obsmotivo = $_POST['obsmotivo'];
            $departamento = new update();

            if($nomemotivo == ''){
                painel::alert('erro','Nome do departamento não pode retornar vazio!');
            }else
            if($obsmotivo == ''){
                painel::alert('erro','A descrição não pode retornar vazia!');
            }else{
				$departamento = new update();
				$departamento->updateMotivo($nomemotivo, $obsmotivo, $id);
			}

		}
		?>
		<div class="form_group">
			<label>Nome:</label>
			<input type="text" name="nomemotivo" value="<? echo $dept['nomemotivo']; ?>">
		</div>
        <div class="form_group">
			<label>Observação:</label>
			<textarea name="obsmotivo"><? echo $dept['obsmotivo']; ?></textarea>
		</div>
		
		<div class="form_group">
            <input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="history.go(-1)">
		</div>
	</form>
</div>