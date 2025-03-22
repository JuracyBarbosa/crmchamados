<?
//Verifica access
checkAccessPage('cadastracategoria', 's', 'denied');
?>

<div class="box_content">
	<h2><i class="fa-solid fa-layer-group"></i> Cadastrar Categorias</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if (isset($_POST['acao'])) {

			try {
				$nomecategoria = $_POST['nomecategoria'];
				$userinsert = $_SESSION['iduser'];
				$desccategoria = $_POST['desccategoria'];
				@$statuscategoria = $_POST['statuscategoria'];

				if ($nomecategoria == '') {
					$msg = '' . Painel::alert('erro', 'Nome da categoria deve ser preenchido!') . '';
					throw new Exception($msg);
				}
				if ($desccategoria == '') {
					$msg = '' . Painel::alert('erro', 'Deve inserir uma breve descrição dessa categoria que deseja cadastrar!') . '';
					throw new Exception($msg);
				}
				if (strlen($desccategoria) > 14 && preg_match('/^(.)\1*$/', $desccategoria)) {
					$msg = '' . Painel::alert('erro', 'Por favor, a descrição é importante para entendimento do cadastro!') . '';
					throw new Exception($msg);
				}
				if ($statuscategoria == '') {
					$msg = '' . Painel::alert('erro', 'O status deve ser informado!') . '';
					throw new Exception($msg);
				}

				$verificarcadastrocategoria = consulta::cadastrocategoria($nomecategoria);

				if ($verificarcadastrocategoria > 0) {
					$msg = '' . Painel::alert('erro', 'A categoria <b>'.$nomecategoria.'</b> já existe no cadastro!') . '';
					throw new Exception($msg);
				}

				$insertcat = insert::cadcategoria($nomecategoria, $userinsert, $desccategoria, $statuscategoria);

				if ($insertcat == true) {
					alert::Cadsubcategoria($nomecategoria);
				}				

			} catch (Exception $e) {
				$e->getMessage();
			}
		}
		?>
		<div class="form_group">
			<label>Nome da Categoria:</label>
			<input type="text" name="nomecategoria" />
		</div>
		<div class="form_group">
			<label>Descrição:</label>
			<textarea minlength="15" name="desccategoria"></textarea>
		</div>
		<div class="form_group">
			<span>Status:</span>
			<input type="radio" name="statuscategoria" value="A" /> Ativo
			<input type="radio" name="statuscategoria" value="I" /> Inativo
		</div>
		<br />

		<div class="form_group_button">
			<input type="submit" name="acao" value="Cadastrar" />
		</div>
	</form>
</div>