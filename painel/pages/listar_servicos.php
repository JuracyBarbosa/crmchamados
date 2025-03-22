<?
//Verifica access
checkAccessPage('editasite','s','denied');

//Defino variavel do campo ID
$namecampoid = 'id';

if(isset($_GET['excluir'])){
	$idExcluir = intval($_GET['excluir']);
	$selectname = select::seleciona('tbl_site_servicos',''.$namecampoid.' = ?', array($idExcluir));

	//declarando variaveis
	$nameservice = $selectname['servico'];
	$destino = 'servico';

	if($selectname > 0){
		delete::excluir('tbl_site_servicos', $namecampoid, $idExcluir, $nameservice, $destino);
	}

}else if(isset($_GET['order']) && isset($_GET['id'])){
	painel::orderItem('tbl_site_servicos',$_GET['order'],$_GET['id'],$namecampoid);
}

$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 5;

$servicos = select::orderid('tbl_site_servicos',($paginaAtual - 1) * $porPagina,$porPagina);
	
?>

<div class="box_content">
	<h2><i class="fas fa-briefcase"></i> Servicos Cadastrados</h2>
	<div class="wraper_table">
		<table>
			<tr>
				<td><i class="fas fa-briefcase"></i> Serviço</td>
				<td><i class="fas fa-calendar-alt"></i> Data</td>
				<td><i class="fas fa-edit"></i></td>
				<td><i class="fas fa-trash-alt"></i></td>
				<td></td>
				<td></td>
			</tr>
		<?
		foreach($servicos as $key => $value){
			$data = date_create($value['data']);
		?>
			<tr>
				<td><? echo $value['servico']; ?></td>
				<td><? echo date_format($data,"d/m/Y H:i:s"); ?></td>
				<td><a class="btn edit" href="<? echo INCLUDE_PATH_PAINEL ?>editar_servico?id=<? echo $value['id']; ?>"><i class="fas fa-pencil-alt"></i></a></td>
				<td><a actionBtn="delete" class="btn delete" href="<? INCLUDE_PATH_PAINEL ?>listar_servicos?excluir=<? echo $value['id']; ?>"><i class="fas fa-trash"></i></a></td>
				<td><a class="btn order" href="<? echo INCLUDE_PATH_PAINEL ?>listar_servicos?order=up&id=<? echo $value['id']; ?>"><i class="fas fa-angle-up"></i></td>
				<td><a class="btn order" href="<? echo INCLUDE_PATH_PAINEL ?>listar_servicos?order=down&id=<? echo $value['id']; ?>"><i class="fas fa-angle-down"></i></td>
			</tr>
		<?}?>
		</table>
	</div>
	<div class="paginacao">
		<?
		$totalPaginas = ceil(count(select::All('tbl_site_servicos')) / $porPagina);
		
		for($pag = 1; $pag <= $totalPaginas; $pag++){
			if($pag == $paginaAtual)
			echo '<a class="page_selected" href="'.INCLUDE_PATH_PAINEL.'listar_servicos?pagina='.$pag.'">'.$pag.'</a>';
		else
			echo '<a href="'.INCLUDE_PATH_PAINEL.'listar_servicos?pagina='.$pag.'">'.$pag.'</a>';
		}
		?>
	</div>
</div>