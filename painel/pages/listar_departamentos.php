<?
//Verifica access
checkAccessPage('listadept','s','denied');

if(isset($_GET['excluir'])){
	$idExcluir = intval($_GET['excluir']);
	$selectname = select::seleciona('departamento','iddept = ?', array($idExcluir));
	$nomedept = $selectname['nomedept'];
	
	if($selectname > 0){
		delete::departamento('departamento','iddept',$idExcluir,$nomedept);
	}

}else if(isset($_GET['order']) && isset($_GET['id'])){
	$namecampoid = 'iddept';
	painel::orderItem('departamento',$_GET['order'],$_GET['iddept'],$namecampoid);
}

$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 5;

$departamentos = select::orderby('departamento','iddept',($paginaAtual - 1) * $porPagina,$porPagina);
	
?>

<div class="box_content">
	<h2><i class="fa-solid fa-users-gear"></i> Departamentos cadastrados</h2>
	<div class="wraper_table">
		<table>
			<tr>
				<td><i class="fas fa-briefcase"></i> Departamento</td>
				<td><i class="fas fa-calendar-alt"></i> Data cadastro</td>
				<td><i class="fas fa-edit"></i></td>
				<td><i class="fas fa-trash-alt"></i></td>
			</tr>
		<?
		foreach($departamentos as $key => $value){
			$data = date_create($value['datacadastro']);
		?>
			<tr>
				<td><? echo $value['nomedept']; ?></td>
				<td><? echo date_format($data,"d/m/Y H:i:s"); ?></td>
				<td><a class="btn edit" href="<? echo INCLUDE_PATH_PAINEL ?>editar_departamento?id=<? echo $value['iddept']; ?>"><i class="fas fa-pencil-alt"></i></a></td>
				<td><a actionBtn="delete" class="btn delete" href="<? INCLUDE_PATH_PAINEL ?>listar_departamentos?excluir=<? echo $value['iddept']; ?>"><i class="fas fa-trash"></i></a></td>
			</tr>
		<?}?>
		</table>
	</div>
	<div class="paginacao">
		<?
		$totalPaginas = ceil(count(select::All('departamento')) / $porPagina);
		
		for($pag = 1; $pag <= $totalPaginas; $pag++){
			if($pag == $paginaAtual)
			echo '<a class="page_selected" href="'.INCLUDE_PATH_PAINEL.'listar_departamentos?pagina='.$pag.'">'.$pag.'</a>';
		else
			echo '<a href="'.INCLUDE_PATH_PAINEL.'listar_departamentos?pagina='.$pag.'">'.$pag.'</a>';
		}
		?>
	</div>
</div>