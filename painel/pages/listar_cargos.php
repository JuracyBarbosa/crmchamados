<?
//Verifica access
checkAccessPage('listacargo','s','denied');

if(isset($_GET['excluir'])){
	$idExcluir = intval($_GET['excluir']);
	$selectname = select::seleciona('cargo','idcargo = ?', array($idExcluir));
	$nomecargo = $selectname['nomecargo'];
	
	if($selectname > 0){
		delete::excluir('cargo','idcargo',$idExcluir,$nomecargo,'cargo');
	}

}else if(isset($_GET['order']) && isset($_GET['id'])){
	$namecampoid = 'idcargo';
	painel::orderItem('cargo',$_GET['order'],$_GET['idcargo'],$namecampoid);
}

$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 5;

$cargos = select::orderby('cargo','idcargo',($paginaAtual - 1) * $porPagina,$porPagina);
	
?>

<div class="box_content">
	<h2><i class="fa-solid fa-users-gear"></i> Cargos Cadastrados</h2>
	<div class="wraper_table">
		<table>
			<tr>
				<td><i class="fas fa-briefcase"></i> Cargo</td>
				<td><i class="fas fa-calendar-alt"></i> Data cadastro</td>
				<td><i class="fas fa-edit"></i></td>
				<td><i class="fas fa-trash-alt"></i></td>
			</tr>
		<?
		foreach($cargos as $key => $value){
			$data = date_create($value['datacadastro']);
		?>
			<tr>
				<td><? echo $value['nomecargo']; ?></td>
				<td><? echo date_format($data,"d/m/Y H:i:s"); ?></td>
				<td><a class="btn edit" href="<? echo INCLUDE_PATH_PAINEL ?>editar_cargo?id=<? echo $value['idcargo']; ?>"><i class="fas fa-pencil-alt"></i></a></td>
				<td><a actionBtn="delete" class="btn delete" href="<? INCLUDE_PATH_PAINEL ?>listar_cargos?excluir=<? echo $value['idcargo']; ?>"><i class="fas fa-trash"></i></a></td>
			</tr>
		<?}?>
		</table>
	</div>
	<div class="paginacao">
		<?
		$totalPaginas = ceil(count(select::All('cargo')) / $porPagina);
		
		for($pag = 1; $pag <= $totalPaginas; $pag++){
			if($pag == $paginaAtual)
			echo '<a class="page_selected" href="'.INCLUDE_PATH_PAINEL.'listar_cargos?pagina='.$pag.'">'.$pag.'</a>';
		else
			echo '<a href="'.INCLUDE_PATH_PAINEL.'listar_cargos?pagina='.$pag.'">'.$pag.'</a>';
		}
		?>
	</div>
</div>