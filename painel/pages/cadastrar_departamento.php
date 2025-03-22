<?
//Verifica access
checkAccessPage('cadastradept','s','denied');
?>

<div class="box_content">
	<h2><i class="fas fa-edit"></i> Cadastrar Departamento</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){

			$nomedept = $_POST['nomedept'];
			$descdept = $_POST['descdept'];
			$datacad = $_POST['datacad'];
			$statusdept = 'A';

			if($nomedept == ''){
				painel::alert('erro','Preencha o campo nome do Departamento!');
			}elseif($descdept == ''){
				painel::alert('erro','Preencha com uma observação do que se trata o Departamento!');
			} else {
			$verificar = MySql::conectar()->prepare("SELECT * FROM departamento WHERE nomedept = ?");
			$verificar->execute(array($_POST['nomedept']));

			if($verificar->rowCount() == 0) {
				$departamento = new insert();
				$departamento->cadDepartamento($nomedept,$descdept,$datacad,$statusdept);
			} else {
				painel::alert('erro','Departamento <b>'.$nomedept.'</b> já existe!');
			}
		}
		}
		?>
		<div class="form_group">
			<label>Nome do Departamento:</label>
			<input type="text" name="nomedept" />
		</div>
		<div class="form_group">
			<label>Observação:</label>
			<textarea name="descdept"></textarea>
		</div>
		<div class="form_group">
			<? $data = date('Y-m-d H:i:s') ?>
			<input type="hidden" name="datacad" value="<? echo $data ?>" />
		</div>
		
		<div class="form_group">
			<input type="submit" name="acao" value="Cadastrar" />
		</div>
	</form>
</div>