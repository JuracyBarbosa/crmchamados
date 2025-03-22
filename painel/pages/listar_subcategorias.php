<?
//Verifica access
checkAccessPage('listasubcategoria','s','denied');

if(isset($_GET['excluir'])){
	$idExcluir = intval($_GET['excluir']);
	$selectname = select::seleciona('chamados_subcategoria','seq_pla_subcategoria = ?', array($idExcluir));
	$nomecategoria = $selectname['nome_subcategoria'];
	
	if($selectname > 0){
		delete::categoria('chamados_subcategoria','seq_pla_subcategoria',$idExcluir,$nomecategoria);
	}

}

$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 5;

$categoria = select::orderby('chamados_subcategoria','seq_pla_subcategoria',($paginaAtual - 1) * $porPagina,$porPagina);
	
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
			$data = date_create($value['data_cadastro']);
		?>
			<tr>
				<td><? echo $value['nome_subcategoria']; ?></td>
				<td><? echo date_format($data,"d/m/Y H:i:s"); ?></td>
				<td><a class="btn edit" href="<? echo INCLUDE_PATH_PAINEL ?>editar_subcategoria?id=<? echo $value['seq_pla_subcategoria']; ?>"><i class="fas fa-pencil-alt"></i></a></td>
				<td><a actionBtn="delete" class="btn delete" href="<? INCLUDE_PATH_PAINEL ?>listar_subcategorias?excluir=<? echo $value['seq_pla_subcategoria']; ?>"><i class="fas fa-trash"></i></a></td>
			</tr>
		<?}?>
		</table>
	</div>
	<div class="paginacao">
		<?
		$totalPaginas = ceil(count(select::All('chamados_subcategoria')) / $porPagina);
		
		for($pag = 1; $pag <= $totalPaginas; $pag++){
			if($pag == $paginaAtual)
			echo '<a class="page_selected" href="'.INCLUDE_PATH_PAINEL.'listar_subcategorias?pagina='.$pag.'">'.$pag.'</a>';
		else
			echo '<a href="'.INCLUDE_PATH_PAINEL.'listar_subcategorias?pagina='.$pag.'">'.$pag.'</a>';
		}
		?>
	</div>
</div>