<?
//Verifica access
checkAccessPage('cadastrasubcategoria', 's', 'denied');
?>

<div class="box_content">
	<h2><i class="fa-solid fa-square-plus"></i> Cadastrar Subcategoria</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if (isset($_POST['acao'])) {

			try {
				$nomesubcategoria = $_POST['nomesubcategoria'];
				@$descsubcategoria = $_POST['descsubcategoria'];
				@$statussubcategoria = $_POST['statussubcategoria'];
				@$categoria = $_POST['categoria'];


				if ($nomesubcategoria == '') {
					$msg = Painel::alert('aviso', 'Escolha um nome para a categoria!');
					throw new Exception('$msg');
				}
				if ($descsubcategoria == '') {
					$msg = Painel::alert('aviso', 'A descrição da sub categoria deve ser descrita!');
					throw new Exception('$msg');
				}
				if (strlen($descsubcategoria) > 14 && preg_match('/^(.)\1*$/', $descsubcategoria)) {
					$msg = Painel::alert('aviso', 'Por favor, a descrição é importante para entendimento do cadastro!');
					throw new Exception('$msg');
				}
				if ($statussubcategoria == '') {
					$msg = Painel::alert('aviso', 'O status da sub categoria deve ser informado!');
					throw new Exception('$msg');
				}
				if ($categoria == '') {
					$msg = Painel::alert('aviso', 'Selecione uma categoria para associação!');
					throw new Exception('$msg');
				}


				$verificacadastrosubcategoria = consulta::cadastrosubcategoria($nomesubcategoria);

				if ($verificacadastrosubcategoria > 0) {
					$msg = Painel::alert('erro', 'A sub categoria <b>' . $nomesubcategoria . '</b> já existe no cadastro!');
					throw new Exception('$msg');
				} else {
					$insertsubcat = insert::cadsubcategoria($nomesubcategoria, $categoria, $descsubcategoria, $statussubcategoria);

					if ($insertsubcat == true) {
						alert::Cadsubcategoria($nomesubcategoria);
					}
				}
			} catch (Exception $e) {
				$e->getMessage();
			}
		}
		?>

		<div class="form_group">
			<label>Nome da Subcategoria:</label>
			<input type="text" name="nomesubcategoria" />
		</div>
		<div class="form_group">
			<label>Descrição:</label>
			<textarea name="descsubcategoria"></textarea>
		</div>
		<div class="form_group">
			<span>Status:</span>
			<input type="radio" name="statussubcategoria" value="A" /> Ativo
			<input type="radio" name="statussubcategoria" value="I" /> Inativo
		</div>
		<div class="form_group">
			<label>Categoria:</label>
			<select name="categoria">
				<option value=""></option>
				<?
				$categoria = select::All('chamados_categoria');
				foreach($categoria as $key => $value){
					echo '<option value="'.$value['idcategoria'].'">'.$value['nomecategoria'].'</option>';
				}
				?>
			</select>
		</div>
		<br />

		<div class="form_group_button">
			<input type="submit" name="acao" value="Cadastrar" />
		</div>
	</form>
</div>