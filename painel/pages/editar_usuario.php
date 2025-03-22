<?
//Verifica access
checkAccessPage('editauser','S','denied');

if(isset($_GET['id'])){
	$id = (int)$_GET['id'];
	$user = select::seleciona('usuarios','iduser = ?',array($id));
	$access = select::seleciona('permissoes','id_user = ?',array($id));
	//$checkcargo = select::selected('cargo','idcargo = '.$user['id_cargo'].'');

	//Consultas para retorno das tags Select<option>
	$cargos = select::All('cargo');
	$dep = select::All('departamento');

}else{
	painel::alert('erro','Você precisa passar o parametro ID.');
	die();
}
?>

<div class="box_content">
	<h2><i class="fa-solid fa-user-pen"></i> Editar Usuário</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
			@$nivelaccess = $_POST['access'];
			if($nivelaccess == ''){
				$nivelaccess = $access['id_access'];
			}
            $userlogin = $_POST['login'];
			$usersenha = $_POST['senha'];
			$username = $_POST['username'];
			$email = $_POST['email'];
			@$cargo = $_POST['cargo'];
			$departamento = $_POST['departamento'];
			$avataruser = $_FILES['avatar'];
			$avatar_atual = $_POST['avatar_atual'];
            $usuario = new update();

			$idpermission = $access['idpermissao'];
			$datealter = date('Y-m-d H:i:s');
			@$keyuser = $_POST['keyuser'];
			@$abrechamado = $_POST['abre_chamado'];
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

            if($username == ''){
                painel::alert('erro','Nome do usuário não pode retornar vazio!');
            }else
			if($userlogin == ''){
                painel::alert('erro','O login não pode ser vazio!');
            }else
			if($usersenha == ''){
					painel::alert('erro','A senha não pode ser vazia!');
			}else{
				if($avataruser['name'] != ''){
					if(painel::avatarValida($avataruser)){
						delete::avatar($avatar_atual);
						$avataruser = upload::avatar($avataruser, null);
						if($usuario->updateUsuario($userlogin, $usersenha, $username, $email, $nivelaccess, $avataruser, $id)){
							$array = ['idpermissao'=> $idpermission, 'datelastalter' => $datealter, 'id_access' => $nivelaccess, 'abre_chamado' => $abrechamado, 'keyuser' => $keyuser, 'cadastracategoria' => $cadastracategoria, 'listacategoria' => $listacategoria, 'editacategoria' => $editacategoria, 'cadastrasubcategoria' => $cadastrasubcategoria, 'listasubcategoria' => $listasubcategoria, 'editasubcategoria' => $editasubcategoria, 'cadastradept' => $cadastradept, 'listadept' => $listadept, 'editadept' => $editadept, 'cadastracargo' => $cadastracargo, 'listacargo' => $listacargo, 'editacargo' => $editacargo, 'cadastrauser' => $cadastrauser, 'listauser' => $listauser, 'editauser' => $editauser, 'cadastraoperador' => $cadastraoperador, 'listaoperador' => $listaoperador, 'editaoperador' => $editaoperador, 'cadastraocorrencia' => $cadastraocorrencia, 'listaocorrencia' => $listaocorrencia, 'editaocorrencia' => $editaocorrencia, 'editasite' => $editasite];
							update::access($array);
							
							alert::EditarUsuario($username, $avataruser);
						}else{
							alert::Error('Algo de errado no processo de Edição do Usuario com avatar!');
						}
					}else{
						painel::alert('erro','O formato ou tamanho não são validos!');
					}
				}else{
					$avataruser = $avatar_atual;
					$acao = '';
					if($usuario->updateUsuario($userlogin, $usersenha, $username, $email, $nivelaccess, $avataruser, $id)){
						$array = ['idpermissao'=> $idpermission, 'datelastalter' => $datealter, 'id_access' => $nivelaccess, 'abre_chamado' => $abrechamado, 'keyuser' => $keyuser, 'cadastracategoria' => $cadastracategoria, 'listacategoria' => $listacategoria, 'editacategoria' => $editacategoria, 'cadastrasubcategoria' => $cadastrasubcategoria, 'listasubcategoria' => $listasubcategoria, 'editasubcategoria' => $editasubcategoria, 'cadastradept' => $cadastradept, 'listadept' => $listadept, 'editadept' => $editadept, 'cadastracargo' => $cadastracargo, 'listacargo' => $listacargo, 'editacargo' => $editacargo, 'cadastrauser' => $cadastrauser, 'listauser' => $listauser, 'editauser' => $editauser, 'cadastraoperador' => $cadastraoperador, 'listaoperador' => $listaoperador, 'editaoperador' => $editaoperador, 'cadastraocorrencia' => $cadastraocorrencia, 'listaocorrencia' => $listaocorrencia, 'editaocorrencia' => $editaocorrencia, 'editasite' => $editasite];
						update::access($array);
						
						alert::EditarUsuario($username, $acao);
					}else{
						alert::Error('Algo de errado no processo de Edição do Usuario!');
					}
				}
			}
		}
		if($_SESSION['id_access'] == 1){

		?>
		<div class="form_group">
			<label>Administrador:</label>
			<input type="radio" name="access" value="1" <? echo ($access['id_access'] == '1')?"checked":""; ?> /> Sim
			<input type="radio" name="access" value="2" <? echo ($access['id_access'] == '2')?"checked":""; ?> /> Não
		</div>
		<?}?>
		<div class="form_group">
			<label>Nome:</label>
			<input type="text" name="username" value="<? echo $user['nomeuser']; ?>">
		</div>
		<div class="form_group">
			<label>Email:</label>
			<input type="text" name="email" value="<? echo $user['email']; ?>">
		</div>
		<div class="form_group">
			<label>Login:</label>
			<input type="text" name="login" value="<? echo $user['loginuser']; ?>">
		</div>
		<div class="form_group">
			<label>Senha:</label>
			<input type="password" name="senha" value="<? echo $user['senhauser']; ?>">
		</div>
		<!--DESATIVADO <div class="form_group">
			<label>Cargo:</label>
			<select name="cargo">
				<option value=""></option>
				<?
				/* foreach($cargos as $key => $resultcargo){
					echo '<option value="'.$resultcargo['idcargo'].'"'.(($user['id_cargo'] == $resultcargo['idcargo']) ? 'selected' : '').'>'.$resultcargo['nomecargo'].'</option>';
				} */
				?>
			</select>
		</div> -->
		<div class="form_group">
			<label>Departamento:</label>
			<select name="departamento">
				<option value=""></option>
				<?
				foreach($dep as $key => $value){
					echo '<option value="'.$value['iddept'].'"'.(($user['id_dept'] == $value['iddept']) ? 'selected' : '').'>'.$value['nomedept'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="form_group">
			<label>Avatar:</label>
			<input type="file" name="avatar" />
			<input type="hidden" name="avatar_atual" value="<? echo $user['avataruser']; ?>" />
		</div>
		
		<div <? checkAccess('id_access','2') ?>class="w100">
		<h2><i class="fa-solid fa-user-shield"></i> Liberação de acessos</h2>
			<div class="form_group_radio">
				<span>Abre chamado:</span>
				<input type="radio" name="abre_chamado" value="S" <? echo ($access['abre_chamado'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="abre_chamado" value="N" <? echo ($access['abre_chamado'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Key user:</span>
				<input type="radio" name="keyuser" value="S" <? echo ($access['keyuser'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="keyuser" value="N" <? echo ($access['keyuser'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Categoria:</span>
				<input type="radio" name="cadastracategoria" value="S" <? echo ($access['cadastracategoria'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="cadastracategoria" value="N" <? echo ($access['cadastracategoria'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Lista Categoria:</span>
				<input type="radio" name="listacategoria" value="S" <? echo ($access['listacategoria'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="listacategoria" value="N" <? echo ($access['listacategoria'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Categoria:</span>
				<input type="radio" name="editacategoria" value="S" <? echo ($access['editacategoria'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="editacategoria" value="N" <? echo ($access['editacategoria'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Subcategoria:</span>
				<input type="radio" name="cadastrasubcategoria" value="S" <? echo ($access['cadastrasubcategoria'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="cadastrasubcategoria" value="N" <? echo ($access['cadastrasubcategoria'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Lista Subcategoria:</span>
				<input type="radio" name="listasubcategoria" value="S" <? echo ($access['listasubcategoria'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="listasubcategoria" value="N" <? echo ($access['listasubcategoria'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Subcategoria:</span>
				<input type="radio" name="editasubcategoria" value="S" <? echo ($access['editasubcategoria'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="editasubcategoria" value="N" <? echo ($access['editasubcategoria'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Departamento:</span>
				<input type="radio" name="cadastradept" value="S" <? echo ($access['cadastradept'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="cadastradept" value="N" <? echo ($access['cadastradept'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Lista Departamento:</span>
				<input type="radio" name="listadept" value="S" <? echo ($access['listadept'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="listadept" value="N" <? echo ($access['listadept'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Departamento:</span>
				<input type="radio" name="editadept" value="S" <? echo ($access['editadept'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="editadept" value="N" <? echo ($access['editadept'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Cargo:</span>
				<input type="radio" name="cadastracargo" value="S" <? echo ($access['cadastracargo'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="cadastracargo" value="N" <? echo ($access['cadastracargo'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Lista Cargo:</span>
				<input type="radio" name="listacargo" value="S" <? echo ($access['listacargo'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="listacargo" value="N" <? echo ($access['listacargo'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Cargo:</span>
				<input type="radio" name="editacargo" value="S" <? echo ($access['editacargo'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="editacargo" value="N" <? echo ($access['editacargo'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Usuário:</span>
				<input type="radio" name="cadastrauser" value="S" <? echo ($access['cadastrauser'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="cadastrauser" value="N" <? echo ($access['cadastrauser'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>lista Usuário:</span>
				<input type="radio" name="listauser" value="S" <? echo ($access['listauser'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="listauser" value="N" <? echo ($access['listauser'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Usuário:</span>
				<input type="radio" name="editauser" value="S" <? echo ($access['editauser'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="editauser" value="N" <? echo ($access['editauser'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Operador:</span>
				<input type="radio" name="cadastraoperador" value="S" <? echo ($access['cadastraoperador'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="cadastraoperador" value="N" <? echo ($access['cadastraoperador'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>lista Operador:</span>
				<input type="radio" name="listaoperador" value="S" <? echo ($access['listaoperador'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="listaoperador" value="N" <? echo ($access['listaoperador'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Operador:</span>
				<input type="radio" name="editaoperador" value="S" <? echo ($access['editaoperador'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="editaoperador" value="N" <? echo ($access['editaoperador'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Cadastra Ocorrencia:</span>
				<input type="radio" name="cadastraocorrencia" value="S" <? echo ($access['cadastraocorrencia'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="cadastraocorrencia" value="N" <? echo ($access['cadastraocorrencia'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>lista Ocorrencia:</span>
				<input type="radio" name="listaocorrencia" value="S" <? echo ($access['listaocorrencia'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="listaocorrencia" value="N" <? echo ($access['listaocorrencia'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Ocorrencia:</span>
				<input type="radio" name="editaocorrencia" value="S" <? echo ($access['editaocorrencia'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="editaocorrencia" value="N" <? echo ($access['editaocorrencia'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="form_group_radio">
				<span>Edita Site:</span>
				<input type="radio" name="editasite" value="S" <? echo ($access['editasite'] == 'S')?"checked":""; ?> /> Sim
				<input type="radio" name="editasite" value="N" <? echo ($access['editasite'] == 'N')?"checked":""; ?> /> Não
			</div>
			<div class="clear"></div>
		</div>
        		
		<div class="form_group">
            <input type="submit" name="acao" value="Atualizar" />
			<input class="btn-voltar" type="button" value="Voltar" onClick="history.back()">
		</div>
	</form>
</div>