<?
//Verifica access
checkAccessPage('editacargo','s','denied');

if(isset($_GET['id'])){
	$id = (int)$_GET['id'];
	$cargo = select::seleciona('cargo','idcargo = ?',array($id));
}else{
	painel::alert('erro','Você precisa passar o parametro ID.');
	die();
}

$setcargo = select::seleciona('cargo','idcargo = ?',array($id));
$dep = select::All('departamento');
?>

<div class="box_content">
	<h2><i class="fa fa-pen"></i> Editar Cargo</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
            $nomecargo = $_POST['nomecargo'];
            $departamento = $_POST['departamento'];
            $status = $_POST['status'];

            if($nomecargo == ''){
                painel::alert('erro','Nome do cargo não pode retornar vazio!');
            }else
            if($departamento == ''){
                painel::alert('erro','Departamento não pode ser vazio!');
            }else
            if($status == ''){
                painel::alert('erro','O status não pode retornar vazia!');
            }else{
				update::updatecargo($nomecargo, $departamento, $status, $id);
			}

		}
		?>
		<div class="form_group">
			<label>Nome:</label>
			<input type="text" name="nomecargo" value="<? echo $cargo['nomecargo']; ?>">
		</div>
        <div class="form_group">
			<label>Departamento:</label>
			<select name="departamento">
				<option value=""></option>
				<?
				foreach($dep as $key => $value){
					echo '<option value="'.$value['iddept'].'"'.(($setcargo['id_dept'] == $value['iddept']) ? 'selected' : '').'>'.$value['nomedept'].'</option>';
				}
				?>
			</select>
		</div>
        <div class="form_group">
			<label>Status:</label>
			<input type="text" name="status" value="<? echo $cargo['statuscargo']; ?>">
		</div>
		
		<div class="form_group">
            <input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="history.go(-1)">
		</div>
	</form>
</div>