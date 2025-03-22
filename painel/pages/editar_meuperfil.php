<div class="box_content">
	<h2><i class="fa-solid fa-user-tie"></i> Meu Perfil</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
			
			$iduser = $_SESSION['iduser'];
			$loginuser = $_POST['login'];
			$senhauser = $_POST['senha'];
			$nomeuser = $_POST['nome'];
			$email = $_POST['email'];
			$idaccess = $_SESSION['id_access'];
			$avataruser = $_FILES['avatar'];
			$avatar_atual = $_POST['avatar_atual'];
			$usuario = new update();
			
			if($nomeuser == ''){
                painel::alert('erro','Nome do usuário não pode retornar vazio!');
            }else
			if($loginuser == ''){
                painel::alert('erro','O login não pode ser vazio!');
            }else
			if($senhauser == ''){
					painel::alert('erro','A senha não pode ser vazia!');
			}else{
				if($avataruser['name'] != ''){
					if(painel::avatarValida($avataruser)){
						delete::avatar($avatar_atual);
						$avataruser = upload::avatar($avataruser, $loginuser);
						if($usuario->updateUsuario($loginuser, $senhauser, $nomeuser, $email, $idaccess, $avataruser, $iduser)){
							//Atualiza a sessão que sofrer update
							$_SESSION['loginuser'] = $loginuser;
							$_SESSION['senhauser'] = $senhauser;
							$_SESSION['nomeuser'] = $nomeuser;
							$_SESSION['email'] = $email;
							$_SESSION['avataruser'] = $avataruser;

							painel::alert('sucesso','Atualizado com sucesso junto com ao seu avatar!');
						}else{
							painel::alert('erro','Algo deu errado ao atualizar com a avatar!');
						}
					}else{
						painel::alert('erro','O formato ou tamanho não são validos!');
					}
				}else{
					$avataruser = $avatar_atual;
					if($usuario->updateUsuario($loginuser, $senhauser, $nomeuser, $email, $idaccess, $avataruser, $iduser)){
						//Atualiza a sessão que sofrer update
						$_SESSION['loginuser'] = $loginuser;
						$_SESSION['senhauser'] = $senhauser;
						$_SESSION['nomeuser'] = $nomeuser;
						$_SESSION['email'] = $email;
						$_SESSION['avataruser'] = $avataruser;
						
						painel::alert('sucesso','Atualizado com sucesso!');
					}else{
						painel::alert('erro','Ocorreu um erro na atualização!');
					}
				}
			}

		}
		?>
        <div class="form_group">
			<label>Login:</label>
			<input type="text" name="login" value="<? echo $_SESSION['loginuser']; ?>">
		</div>
		<div class="form_group">
			<label>Senha:</label>
			<input type="password" name="senha" value="<? echo $_SESSION['senhauser']; ?>" required />
		</div>
		<div class="form_group">
			<label>Nome:</label>
			<input type="text" name="nome" value="<? echo $_SESSION['nomeuser']; ?>" required />
		</div>
		<div class="form_group">
			<label>Email:</label>
			<input type="text" name="email" value="<? echo $_SESSION['email']; ?>">
		</div>
		<div class="form_group">
			<label>Avatar</label>
			<input type="file" name="avatar" />
			<input type="hidden" name="avatar_atual" value="<? echo $_SESSION['avataruser']; ?>" />
		</div>
		<div class="form_group">
			<input type="submit" name="acao" value="Atualizar" />
		</div>
	</form>
</div>