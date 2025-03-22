<?
//Verifica access
checkAccessPage('access','s','denied');

?>

<div class="box_content">
	<h2><i class="fa-solid fa-user-shield"></i> Liberação de acessos</h2>
	<form method="post" enctype="multipart/form-data">
		<div class="form_group">
			<label>Key user:
			<input type="radio" name="keyuser" value="s" /> Sim
			<input type="radio" name="keyuser" value="n" /> Não
			</label>
		</div>
		<div class="form_group">
			<label>Cadastra Departamento:</label>
			<input type="radio" name="cadastradept" value="s" /> Sim
			<input type="radio" name="cadastradept" value="n" /> Não
		</div>
		<div class="form_group">
			<label>Lista Departamento:</label>
			<input type="radio" name="listadept" value="s" /> Sim
			<input type="radio" name="listadept" value="n" /> Não
		</div>
		<div class="form_group">
			<label>Edita Departamento:</label>
			<input type="radio" name="editadept" value="s" /> Sim
			<input type="radio" name="editadept" value="n" /> Não
		</div>
		<div class="form_group">
			<label>Cadastra Usuário:</label>
			<input type="radio" name="cadastrauser" value="s" /> Sim
			<input type="radio" name="cadastrauser" value="n" /> Não
		</div>
		<div class="form_group">
			<label>Edita Usuário:</label>
			<input type="radio" name="editauser" value="s" /> Sim
			<input type="radio" name="editauser" value="n" /> Não
		</div>
		<div class="form_group">
			<label>lista Usuário:</label>
			<input type="radio" name="listauser" value="s" /> Sim
			<input type="radio" name="listauser" value="n" /> Não
		</div>
		<div class="form_group">
			<label>Edita Site:</label>
			<input type="radio" name="editasite" value="s" /> Sim
			<input type="radio" name="editasite" value="n" /> Não
		</div>
		
		<div class="form_group">
			<input type="submit" name="acao" value="Cadastrar" />
		</div>
	</form>
</div>