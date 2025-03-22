<?
//Verifica access
checkAccessPage('editasite','s','denied');

if(isset($_GET['excluir'])){
	$idExcluir = intval($_GET['excluir']);
	$selectimg = select::seleciona('images','idimg = ?', array($idExcluir));
	
	//declaro variaveis
	$namecampoid = 'idimg';
	$slide = $selectimg['slide'];
	$nameslide = $selectimg['nameimg'];
	
	if($selectimg > 0){
		painel::deleteSlide($slide);
		delete::slide('images',$namecampoid,$idExcluir,$nameslide);
	}

}else if(isset($_GET['order']) && isset($_GET['id'])){
	$namecampoid = 'idimg';
	painel::orderItem('images',$_GET['order'],$_GET['id'],$namecampoid);
}

$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 5;

$slides = select::orderid('images',($paginaAtual - 1) * $porPagina,$porPagina);
	
?>

<div class="box_content">
	<h2><i class="far fa-images"></i> Slides Cadastrados</h2>
	<div class="wraper_table">
		<table>
			<tr>
				<td><i class="far fa-file-image"></i> Nome</td>
				<td><i class="far fa-image"></i> Slide</td>
				<td><i class="fas fa-calendar-alt"></i> Data</td>
				<td><i class="fas fa-edit"></i></td>
				<td><i class="fas fa-trash-alt"></i></td>
				<td></td>
				<td></td>
			</tr>
		<?
		foreach($slides as $key => $value){
			$data = date_create($value['datacadastro']);
		?>
			<tr>
				<td><? echo $value['nameimg']; ?></td>
				<td><img class="prev_img" src="<? echo INCLUDE_PATH_PAINEL ?>uploads/slides/<? echo $value['slide']; ?>" /></td>
				<td><? echo date_format($data,"d/m/Y H:i:s"); ?></td>
				<td><a class="btn edit" href="<? echo INCLUDE_PATH_PAINEL ?>editar_slide?id=<? echo $value['idimg']; ?>"><i class="fas fa-pencil-alt"></i> Editar</a></td>
				<td><a actionBtn="delete" class="btn delete" href="<? INCLUDE_PATH_PAINEL ?>listar_slides?excluir=<? echo $value['idimg']; ?>"><i class="fas fa-trash"></i> Excluir</a></td>
				<td><a class="btn order" href="<? echo INCLUDE_PATH_PAINEL ?>listar_slides?order=up&id=<? echo $value['idimg']; ?>"><i class="fas fa-angle-up"></i></td>
				<td><a class="btn order" href="<? echo INCLUDE_PATH_PAINEL ?>listar_slides?order=down&id=<? echo $value['idimg']; ?>"><i class="fas fa-angle-down"></i></td>
			</tr>
		<?}?>
		</table>
	</div>
	<div class="paginacao">
		<?
		$totalPaginas = ceil(count(select::All('images')) / $porPagina);
		
		for($pag = 1; $pag <= $totalPaginas; $pag++){
			if($pag == $paginaAtual)
			echo '<a class="page_selected" href="'.INCLUDE_PATH_PAINEL.'listar_slides?pagina='.$pag.'">'.$pag.'</a>';
		else
			echo '<a href="'.INCLUDE_PATH_PAINEL.'listar_slides?pagina='.$pag.'">'.$pag.'</a>';
		}
		?>
	</div>
</div>