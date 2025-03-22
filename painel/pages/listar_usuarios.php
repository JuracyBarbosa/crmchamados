<?
//verifica access
checkAccessPage('listauser','s','denied');

//Defino variavel do campo ID
$namecampoid = 'iduser';
$namecampoidaccess = 'id_user';

if(isset($_GET['excluir'])){
	$idExcluir = intval($_GET['excluir']);
	$selectname = select::seleciona('usuarios','iduser = ?', array($idExcluir));
	$sqlavatar = select::selected('usuarios','iduser = '.$idExcluir.'' );
	foreach ($sqlavatar as $key => $resultavatar) {
		
		if($selectname > 0){
		delete::usuario('usuarios','iduser',$idExcluir,$resultavatar['nomeuser']);
		delete::avatar($resultavatar['avataruser']);
		delete::excluir('permissoes', $namecampoidaccess, $idExcluir, null, null);
		}
	}

}

$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 10;

$usuarios = select::listuseraccess(($paginaAtual - 1) * $porPagina,$porPagina);
	
?>

<div class="box_content">
	<h2><i class="fa-solid fa-users-line"></i> Usuários Cadastrados</h2>
	<div class="wraper_table">
		<table>
			<tr>
				<td><i class="fa-solid fa-id-badge"></i> Login</td>
				<td><i class="fa-solid fa-user"></i> Nome</td>
				<td><i class="fas fa-calendar-alt"></i> Data cadastro</td>
				<td><i class="fa-solid fa-user-check"></i> Abre Chamado</td>
				<td><i class="fa-solid fa-user-shield"></i> Key User</td>
				<td><i class="fa-solid fa-user-gear"></i> Função</td>
				<td><i class="fas fa-edit"></i> Edit</td>
				<td><i class="fas fa-trash-alt"></i> Del</td>
			</tr>
		<?
		foreach($usuarios as $key => $value){
			$data = date_create($value['datacadastro']);

		?>
			<tr>
				<td><? echo $value['login']; ?></td>
				<td><? echo $value['nome']; ?></td>
				<td><? echo date_format($data,"d/m/Y H:i:s"); ?></td>
				<td><? echo $value['abre_chamado']; ?></td>
				<td><? echo $value['keyuser']; ?></td>
				<td><? echo $value['operador']; ?></td>
				<td><a class="btn edit" href="<? echo INCLUDE_PATH_PAINEL ?>editar_usuario?id=<? echo $value['iduser']; ?>"><i class="fas fa-pencil-alt"></i></a></td>
				<td><a actionBtn="delete" class="btn delete" href="<? INCLUDE_PATH_PAINEL ?>listar_usuarios?excluir=<? echo $value['iduser']; ?>"><i class="fas fa-trash"></i></a></td>
			</tr>
		<?}?>
		</table>
	</div>
	<div class="paginacao">
		<?
		$totalPaginas = ceil(count(select::All('usuarios')) / $porPagina);
		
		for($pag = 1; $pag <= $totalPaginas; $pag++){
			if($pag == $paginaAtual)
			echo '<a class="page_selected" href="'.INCLUDE_PATH_PAINEL.'listar_usuarios?pagina='.$pag.'">'.$pag.'</a>';
		else
			echo '<a href="'.INCLUDE_PATH_PAINEL.'listar_usuarios?pagina='.$pag.'">'.$pag.'</a>';
		}
		?>
	</div>
</div>