<?
//Verifica access
checkAccessPage('editauser', 'S', 'denied');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $operador = select::operador($id);
    //$checkcargo = select::selected('cargo','idcargo = '.$user['id_cargo'].'');

    //Consultas para retorno das tags Select<option>
    //$cargos = select::All('cargo');
    $access = select::All('access');

} else {
    painel::alert('erro', 'Você precisa passar o parametro ID.');
    die();
}
?>

<div class="box_content">
    <h2><i class="fa-solid fa-user-pen"></i> Editar Usuário</h2>
    <form method="post" enctype="multipart/form-data">
        <?
        if (isset($_POST['acao'])) {
            $nomeoperador = $_POST['surname'];
            $nivelaccess = $_POST['access'];
            $prestaservico = $_POST['prestadorservico'];
            $datealter = date('Y-m-d H:i:s');
            
            if ($nivelaccess == '') {
                painel::alert('erro', 'O nivel de acesso não pode ser vazio!');
            } else {
                update::operador($nomeoperador, $nivelaccess, $prestaservico, $id);
            }
        }

        ?>
        <div>
            <div class="form_group">
                <label>Operador:</label>
                <input type="text" name="surname" value="<? echo $operador['surname']; ?>">
            </div>
            <div class="form_group">
			<label>Nivel de Acesso:</label>
			<select name="access">
				<option value=""></option>
				<?
				foreach($access as $key => $value){
					echo '<option value="'.$value['idaccess'].'"'.(($operador['access'] == $value['nomeaccess']) ? 'selected' : '').'>'.$value['nomeaccess'].'</option>';
				}
				?>
			</select>
		    </div>
            <h2><i class="fa-solid fa-handshake"></i> Prestador de Serviço</h2>
            <div class="form_group_radio">
                <span>Terceirizado:</span>
                <input type="radio" name="prestadorservico" value="S" <? echo ($operador['prestador_servico'] == 'S') ? "checked" : ""; ?> /> Sim
                <input type="radio" name="prestadorservico" value="N" <? echo ($operador['prestador_servico'] == 'N') ? "checked" : ""; ?> /> Não
            </div>
            <div class="clear"></div>
        </div>
        <div class="form_group">
            <input type="submit" name="acao" value="Atualizar" />
            <input class="btn-voltar" type="button" value="Voltar" onClick="history.back()">
        </div>
    </form>
</div>