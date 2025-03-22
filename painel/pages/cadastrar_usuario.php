<?
//Verifica access
checkAccessPage('cadastrauser','s', 'denied');
?>

<div class="box_content">
	<h2><i class="fa-solid fa-user-plus"></i> Cadastrar Usuário</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
			$access = $_POST['access'];
			$login = $_POST['login'];
			$senha = $_POST['password'];
			$nome = $_POST['nome'];
			$email = $_POST['email'];
			$idaccess = $_POST['access'];
			$cargo = 0; //$_POST['cargo'];
			$departamento = $_POST['departamento'];
			$avatar = $_FILES['avatar'];
			$dtinserssao = date('Y-m-d H:i:s');
			@$keyuser = $_POST['keyuser'];
			@$abrechamado = $_POST['abrechamado'];
			@$cadastracategoria = $_POST['cadastracategoria'];
			@$listacategoria = $_POST['listacategoria'];
			@$editacategoria = $_POST['editacategoria'];
			@$cadastrasubcategoria = $_POST['cadastrasubcategoria'];
			@$listasubcategoria = $_POST['listasubcategoria'];
			@$editasubcategoria = $_POST['editasubcategoria'];
			@$cadastradept = $_POST['cadastradept'];
			@$listadept = $_POST['listadept'];
			@$editadept = $_POST['editadept'];
			@$cadastracargo = $_POST['cadastracargo'];
			@$listacargo = $_POST['listacargo'];
			@$editacargo = $_POST['editacargo'];
			@$cadastrauser = $_POST['cadastrauser'];
			@$listauser = $_POST['listauser'];
			@$editauser = $_POST['editauser'];

			@$cadastraoperador = $_POST['cadastraoperador'];
			@$listaoperador = $_POST['listaoperador'];
			@$editaoperador = $_POST['editaoperador'];
			@$cadastraocorrencia = $_POST['cadastraocorrencia'];
			@$listaocorrencia = $_POST['listaocorrencia'];
			@$editaocorrencia = $_POST['editaocorrencia'];
			@$editasite = $_POST['editasite'];
			
			if($login == ''){
				painel::alert('erro','O login está vázio!');
			}else if($senha == ''){
				painel::alert('erro','A senha não pode ser vázio!');
			}else if($nome == ''){
				painel::alert('erro','O nome está vázio!');
			}else if($avatar['name'] = ''){
				painel::alert('erro','A avatar não pode ser vázio!');
			}else if($departamento == ''){
				painel::alert('erro','Escolha o Departamento!');
			}else{
				if(painel::avatarValida($avatar) == false){
					painel::alert('erro','A avatar não é valida! verifique se a avatar é .PNG, .JPG, .JPEG e menor que 300kb');
				}else if(verificar::userExists($login)){
					painel::alert('erro','O login já existe!');
				}else{
					$usuario = new insert();
					$avatar = upload::avatar($avatar, $login);
					$usuario->cadUsuario($login,$senha,$nome,$email,$idaccess,$cargo,$departamento,$avatar);

					$lastID = MySql::conectar()->lastInsertId();
					$array = ['id_user' => $lastID, 'datacadastro' => $dtinserssao, 'datelastalter' => '1989-01-01 23:59:59', 'id_access' => $access, 'abre_chamado' => $abrechamado, 'keyuser' => $keyuser, 'cadastracategoria' => $cadastracategoria, 'listacategoria' => $listacategoria, 'editacategoria' => $editacategoria, 'cadastrasubcategoria' => $cadastrasubcategoria, 'listasubcategoria' => $listasubcategoria, 'editasubcategoria' => $editasubcategoria, 'cadastradept' => $cadastradept, 'listadept' => $listadept, 'editadept' => $editadept, 'cadastracargo' => $cadastracargo, 'listacargo' => $listacargo, 'editacargo' => $editacargo, 'cadastrauser' => $cadastrauser, 'listauser' => $listauser, 'editauser' => $editauser, 'cadastraoperador' => $cadastraoperador, 'listaoperador' => $listaoperador, 'editaoperador' => $editaoperador, 'cadastraocorrencia' => $cadastraocorrencia, 'listaocorrencia' => $listaocorrencia, 'editaocorrencia' => $editaocorrencia, 'editasite' => $editasite];
					insert::includaccess($array);

					alert::alertaCadUsuario($nome);
				}
			}
		}
		?>
		<div class="form_group">
			<label>Nivel de Acesso:</label>
			<input type="radio" name="access" value="1" /> Administrador
			<input type="radio" name="access" value="2" checked/> Normal
		</div>
		<div class="form_group">
			<label>Login:</label>
			<input type="text" name="login" />
		</div>
		<div class="form_group">
			<label>Senha:</label>
			<input type="password" name="password" />
		</div>
		<div class="form_group">
			<label>Nome:</label>
			<input type="text" name="nome" />
		</div>
		<div class="form_group">
			<label>E-mail:</label>
			<input type="text" name="email" />
		</div>
		<div class="form_group">
			<label>Cargo:</label>
			<select name="cargo">
				<option value=""></option>
				<?
				$cargos = select::All('cargo');
				foreach($cargos as $key => $resultcargo){
					echo '<option value="'.$resultcargo['idcargo'].'">'.$resultcargo['nomecargo'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="form_group">
			<label>Departamento:</label>
			<select name="departamento">
				<option value=""></option>
				<?
				$dep = select::All('departamento');
				foreach($dep as $key => $value){
					echo '<option value="'.$value['iddept'].'">'.$value['nomedept'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="form_group">
			<label>Avatar:</label>
			<input type="file" name="avatar" />
		</div>
		<h2><i class="fa-solid fa-user-shield"></i> Liberação de acessos</h2>
		<div class="w100">
			<div class="form_group_radio">
				<span>Abre Chamado:</span>
				<input type="radio" name="abrechamado" value="S" /> Sim
				<input type="radio" name="abrechamado" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Key user:</span>
				<input type="radio" name="keyuser" value="S" /> Sim
				<input type="radio" name="keyuser" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Categoria:</span>
				<input type="radio" name="cadastracategoria" value="S" /> Sim
				<input type="radio" name="cadastracategoria" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Lista Categoria:</span>
				<input type="radio" name="listacategoria" value="S" /> Sim
				<input type="radio" name="listacategoria" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Categoria:</span>
				<input type="radio" name="editacategoria" value="S" /> Sim
				<input type="radio" name="editacategoria" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Subcategoria:</span>
				<input type="radio" name="cadastrasubcategoria" value="S" /> Sim
				<input type="radio" name="cadastrasubcategoria" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Lista Subcategoria:</span>
				<input type="radio" name="listasubcategoria" value="S" /> Sim
				<input type="radio" name="listasubcategoria" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Subcategoria:</span>
				<input type="radio" name="editasubcategoria" value="S" /> Sim
				<input type="radio" name="editasubcategoria" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Departamento:</span>
				<input type="radio" name="cadastradept" value="S" /> Sim
				<input type="radio" name="cadastradept" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Lista Departamento:</span>
				<input type="radio" name="listadept" value="S" /> Sim
				<input type="radio" name="listadept" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Departamento:</span>
				<input type="radio" name="editadept" value="S" /> Sim
				<input type="radio" name="editadept" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Cargo:</span>
				<input type="radio" name="cadastracargo" value="S" /> Sim
				<input type="radio" name="cadastracargo" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Lista Cargo:</span>
				<input type="radio" name="listacargo" value="S" /> Sim
				<input type="radio" name="listacargo" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Cargo:</span>
				<input type="radio" name="editacargo" value="S" /> Sim
				<input type="radio" name="editacargo" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Usuário:</span>
				<input type="radio" name="cadastrauser" value="S" /> Sim
				<input type="radio" name="cadastrauser" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>lista Usuário:</span>
				<input type="radio" name="listauser" value="S" /> Sim
				<input type="radio" name="listauser" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Usuário:</span>
				<input type="radio" name="editauser" value="S" /> Sim
				<input type="radio" name="editauser" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Operador:</span>
				<input type="radio" name="cadastraoperador" value="S" /> Sim
				<input type="radio" name="cadastraoperador" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>lista Operador:</span>
				<input type="radio" name="listaoperador" value="S" /> Sim
				<input type="radio" name="listaoperador" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Operador:</span>
				<input type="radio" name="editaoperador" value="S" /> Sim
				<input type="radio" name="editaoperador" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Ocorrencia:</span>
				<input type="radio" name="cadastraocorrencia" value="S" /> Sim
				<input type="radio" name="cadastraocorrencia" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>lista Ocorrencia:</span>
				<input type="radio" name="listaocorrencia" value="S" /> Sim
				<input type="radio" name="listaocorrencia" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Ocorrencia:</span>
				<input type="radio" name="editaocorrencia" value="S" /> Sim
				<input type="radio" name="editaocorrencia" value="N" checked /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Site:</span>
				<input type="radio" name="editasite" value="S" /> Sim
				<input type="radio" name="editasite" value="N" checked /> Não
			</div>
			<div class="clear"></div>
		</div>
		<div class="form_group">
			<input type="submit" name="acao" value="Cadastrar" />
		</div>
	</form>
</div>