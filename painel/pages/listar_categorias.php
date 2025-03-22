<?
//Verifica access
checkAccessPage('listacategoria','s','denied');

if(isset($_GET['excluir'])){
	$idExcluir = intval($_GET['excluir']);
	$selectname = select::seleciona('chamados_categoria','idcategoria = ?', array($idExcluir));
	$nomecategoria = $selectname['nomecategoria'];
	
	if($selectname > 0){
		delete::categoria('chamados_categoria','idcategoria',$idExcluir,$nomecategoria);
	}

}

$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 5;

$categoria = select::orderby('chamados_categoria','idcategoria',($paginaAtual - 1) * $porPagina,$porPagina);
	
?>

<div class="box_content">
	<h2><i class="fa-solid fa-users-gear"></i> Categorias Cadastradas</h2>
	<div class="wraper_table">
		<table>
			<tr>
				<td><i class="fas fa-briefcase"></i> Categorias</td>
				<td><i class="fas fa-calendar-alt"></i> Data cadastro</td>
				<td><i class="fas fa-edit"></i></td>
				<td><i class="fas fa-trash-alt"></i></td>
			</tr>
		<?
		foreach($categoria as $key => $value){
			$data = date_create($value['datacadastro']);
		?>
			<tr>
				<td><? echo $value['nomecategoria']; ?></td>
				<td><? echo date_format($data,"d/m/Y H:i:s"); ?></td>
				<td><a class="btn edit" href="<? echo INCLUDE_PATH_PAINEL ?>editar_categoria?id=<? echo $value['idcategoria']; ?>"><i class="fas fa-pencil-alt"></i></a></td>
				<td><a actionBtn="delete" class="btn delete" href="<? INCLUDE_PATH_PAINEL ?>listar_categorias?excluir=<? echo $value['idcategoria']; ?>"><i class="fas fa-trash"></i></a></td>
			</tr>
		<?}?>
		</table>
	</div>
	<div class="paginacao">
		<?
		$totalPaginas = ceil(count(select::All('chamados_categoria')) / $porPagina);
		
		for($pag = 1; $pag <= $totalPaginas; $pag++){
			if($pag == $paginaAtual)
			echo '<a class="page_selected" href="'.INCLUDE_PATH_PAINEL.'listar_categorias?pagina='.$pag.'">'.$pag.'</a>';
		else
			echo '<a href="'.INCLUDE_PATH_PAINEL.'listar_categorias?pagina='.$pag.'">'.$pag.'</a>';
		}
		?>
	</div>
</div>