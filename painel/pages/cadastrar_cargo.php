<?
//Verifica access
checkAccessPage('cadastracargo','s','denied');
?>

<div class="box_content">
	<h2><i class="fas fa-edit"></i> Cadastrar Cargo</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
			
			$nomecargo = $_POST['nomecargo'];
			$departamento = $_POST['departamento'];
			$statuscargo = 'A';

			if($nomecargo == ''){
				painel::alert('erro','Preencha o campo nome do cargo!');
			}else
			if($departamento == ''){
				painel::alert('erro','Preencha com uma observação do objetivo desse cargo!');
			} else {
				$verificar = MySql::conectar()->prepare("SELECT * FROM cargo WHERE nomecargo = ?");
				$verificar->execute(array($_POST['nomecargo']));
				if($verificar->rowCount() == 0){
					$cargo = new insert();
					$cargo->cadastracargo($nomecargo,$departamento,$statuscargo);
				} else {
					painel::alert('erro','cargo <b>'.$nomecargo.'</b> já existe!');
				}
			}
		}
		?>
		<div class="form_group">
			<label>Nome do cargo:</label>
			<input type="text" name="nomecargo" />
		</div>
		<div class="form_group">
			<label>Departamento:</label>
			<select name="departamento">
				<option value=""></option>
				<?
				$cargos = select::All('departamento');
				foreach($cargos as $key => $resultcargo){
					echo '<option value="'.$resultcargo['iddept'].'">'.$resultcargo['nomedept'].'</option>';
				}
				?>
			</select>
		</div>
		
		<div class="form_group">
			<input type="submit" name="acao" value="Cadastrar" />
		</div>
	</form>
</div>