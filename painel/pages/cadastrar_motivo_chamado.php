<?
//Verifica access
checkAccessPage('cadastramotivo','s');
?>

<div class="box_content">
	<h2><i class="fas fa-edit"></i> Cadastrar Motivos de Chamados</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
			
			$nomemotivo = $_POST['nomemotivo'];
			$obsmotivo = $_POST['obsmotivo'];
			$datacadmotivo = $_POST['datacadmotivo'];
			$statusmotivo = 'A';

			if($nomemotivo == ''){
				painel::alert('erro','Preencha o campo nome do motivo!');
			}else
			if($obsmotivo == ''){
				painel::alert('erro','Preencha com uma observação do objetivo desse motivo!');
			} else {
				$verificar = MySql::conectar()->prepare("SELECT * FROM motivo_chamado WHERE nomemotivo = ?");
				$verificar->execute(array($_POST['nomemotivo']));
				if($verificar->rowCount() == 0){
					$Motivo = new insert();
					$Motivo->cadMotivoch($nomemotivo,$obsmotivo,$datacadmotivo,$statusmotivo);
				} else {
					painel::alert('erro','Motivo <b>'.$nomemotivo.'</b> já existe!');
				}
			}
		}
		?>
		<div class="form_group">
			<label>Nome do Motivo:</label>
			<input type="text" name="nomemotivo" />
		</div>
		<div class="form_group">
			<label>Observação:</label>
			<textarea name="obsmotivo"></textarea>
		</div>
		<div class="form_group">
			<? $data = date('Y-m-d H:i:s') ?>
			<input type="hidden" name="datacadmotivo" value="<? echo $data ?>" />
		</div>
		
		<div class="form_group">
			<input type="submit" name="acao" value="Cadastrar" />
		</div>
	</form>
</div>