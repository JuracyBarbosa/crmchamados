<?
//Verifica access
checkAccessPage('cadastraoperador','s','denied');
?>

<div class="box_content">
	<h2><i class="fa-solid fa-user-plus"></i> Cadastrar Operador</h2>
	<form method="post" enctype="multipart/form-data">
		<?
		if(isset($_POST['acao'])){
			
			$operador = $_POST['operador'];
			$apelido = $_POST['apelido'];
			$designacao = $_POST['designacao'];
			$prestaservico = $_POST['prestadorservico'];
			
			if($operador == ''){
				painel::alert('erro','O operador não foi selecionado, está vázio!');
				return;
			}if($apelido == ''){
				painel::alert('erro','O apelido não pode ser vazio!!');
				return;
			}else{
				if(verificar::operadorExists($apelido, $operador)){
					painel::alert('erro','Esse operador já está cadastrado!');
				}else{
					if($apelido == ''){
						$peganome = select::selected('usuarios', 'iduser = '.$operador.'');
						foreach($peganome as $key => $peganame)
						$peganame = $peganame['nomeuser'];

						$pegasurname = explode(' ', $peganame);
						$name = array_shift($pegasurname);
						$lastname = array_pop($pegasurname);
						$apelido = $name.'.'.$lastname;
					}
					
					$insert = insert::cadastraoperador($operador, $apelido, $designacao, $prestaservico);
					if($insert == true){
						alert::cadOperador($apelido);
					}else{
						painel::alert('erro', 'Erro ao cadastrar operador!');
					}
					
				}
			}
		}
		?>
        <div class="form_group">
			<label>Usuário:</label>
			<select name="operador">
				<option value=""></option>
				<?
				$cargos = select::All('usuarios');
				foreach($cargos as $key => $resultcargo){
					echo '<option value="'.$resultcargo['iduser'].'">'.$resultcargo['nomeuser'].'</option>';
				}
				?>
			</select>
		</div>
		<div class="form_group">
			<label>Apelido:</label>
			<input type="text" name="apelido" />
		</div>

		<h2><i class="fa-solid fa-user-shield"></i> Designação</h2>
		<div class="w100">
			<div class="form_group">
				<span>Gestor:</span>
				<input type="radio" name="designacao" value="3" />
			</div>
			<div class="form_group">
				<span>Atendente:</span>
				<input type="radio" name="designacao" checked value="4" />
			</div>
			<h2><i class="fa-solid fa-handshake"></i> Prestador de Serviço</h2>
            <div class="form_group_radio">
                <span>Terceirizado:</span>
                <input type="radio" name="prestadorservico" value="S" /> Sim
                <input type="radio" name="prestadorservico" checked value="N" /> Não
            </div>

			<div class="clear"></div>
		</div>
		<div class="form_group">
			<input type="submit" name="acao" value="Cadastrar" />
		</div>
	</form>
</div>