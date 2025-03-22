<?
//Verifica access
checkAccessPage('listamotivo','s');

if(isset($_GET['excluir'])){
	$idExcluir = intval($_GET['excluir']);
	$selectname = select::seleciona('motivo_chamado','idmotivo = ?', array($idExcluir));
	$nomemotivo = $selectname['nomemotivo'];
	
	if($selectname > 0){
		delete::motivos('motivo_chamado','idmotivo',$idExcluir,$nomemotivo);
	}

}

$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 5;

$motivo = select::orderby('motivo_chamado','idmotivo',($paginaAtual - 1) * $porPagina,$porPagina);
	
?>

<div class="box_content">
	<h2><i class="fa-solid fa-users-gear"></i> Motivos cadastrados</h2>
	<div class="wraper_table">
		<table>
			<tr>
				<td><i class="fas fa-briefcase"></i> Motivos</td>
				<td><i class="fas fa-calendar-alt"></i> Data cadastro</td>
				<td><i class="fas fa-edit"></i></td>
				<td><i class="fas fa-trash-alt"></i></td>
			</tr>
		<?
		foreach($motivo as $key => $value){
			$data = date_create($value['datacadastro']);
		?>
			<tr>
				<td><? echo $value['nomemotivo']; ?></td>
				<td><? echo date_format($data,"d/m/Y H:i:s"); ?></td>
				<td><a class="btn edit" href="<? echo INCLUDE_PATH_PAINEL ?>editar_motivo?id=<? echo $value['idmotivo']; ?>"><i class="fas fa-pencil-alt"></i></a></td>
				<td><a actionBtn="delete" class="btn delete" href="<? INCLUDE_PATH_PAINEL ?>listar_motivos?excluir=<? echo $value['idmotivo']; ?>"><i class="fas fa-trash"></i></a></td>
			</tr>
		<?}?>
		</table>
	</div>
	<div class="paginacao">
		<?
		$totalPaginas = ceil(count(select::All('motivo_chamado')) / $porPagina);
		
		for($pag = 1; $pag <= $totalPaginas; $pag++){
			if($pag == $paginaAtual)
			echo '<a class="page_selected" href="'.INCLUDE_PATH_PAINEL.'listar_motivos?pagina='.$pag.'">'.$pag.'</a>';
		else
			echo '<a href="'.INCLUDE_PATH_PAINEL.'listar_motivos?pagina='.$pag.'">'.$pag.'</a>';
		}
		?>
	</div>
</div>