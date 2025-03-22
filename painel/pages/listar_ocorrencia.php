<?
//Verifica access
checkAccessPage('listaocorrencia','s','denied');

$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 10;

if(isset($_GET['excluir'])){
	$idExcluir = intval($_GET['excluir']);
	$selectname = select::seleciona('chamados_ocorrencia','seq_pla_ocorrencia = ?', array($idExcluir));
	@$nomeocorrencia = $selectname['nomeocorrencia'];
	
	if($selectname > 0){
		delete::categoria('chamados_ocorrencia','seq_pla_ocorrencia',$idExcluir,$nomeocorrencia);
		$url = "http://localhost/painel/listar_ocorrencia";
		header("Location: $url");
		exit();
	}

}

$ocorrencia = select::orderocorrencia(($paginaAtual - 1) * $porPagina,$porPagina);

?>

<div class="box_content">
	<h2><i class="fa-solid fa-users-gear"></i> Ocorrências Cadastradas</h2>
	<div class="wraper_table">
		<table>
			<tr>
				<td><i class="fas fa-briefcase"></i> Ocorrência</td>
				<td><i class="fas fa-calendar-alt"></i> Data cadastro</td>
				<td><i class="fas fa-edit"></i> Editar</td>
				<td><i class="fas fa-trash-alt"></i> Deletar</td>
			</tr>
		<?
		foreach($ocorrencia as $key => $value){
			$data = date_create($value['data_cadastro']);
		?>
			<tr>
				<td><? echo $value['nome_ocorrencia']; ?></td>
				<td><? echo date_format($data,"d/m/Y H:i:s"); ?></td>
				<td><a class="btn edit" href="<? echo INCLUDE_PATH_PAINEL ?>editar_ocorrencia?id=<? echo $value['seq_pla_ocorrencia']; ?>"><i class="fas fa-pencil-alt"></i></a></td>
				<td><a actionBtn="delete" class="btn delete" href="<? INCLUDE_PATH_PAINEL ?>listar_ocorrencia?excluir=<? echo $value['seq_pla_ocorrencia']; ?>"><i class="fas fa-trash"></i></a></td>
			</tr>
		<?}?>
		</table>
	</div>
	<div class="paginacao">
		<?
		$totalPaginas = ceil(count(select::orderocorrencia()) / $porPagina);
		
		for($pag = 1; $pag <= $totalPaginas; $pag++){
			if($pag == $paginaAtual)
			echo '<a class="page_selected" href="'.INCLUDE_PATH_PAINEL.'listar_ocorrencia?pagina='.$pag.'">'.$pag.'</a>';
		else
			echo '<a href="'.INCLUDE_PATH_PAINEL.'listar_ocorrencia?pagina='.$pag.'">'.$pag.'</a>';
		}
		?>
	</div>
</div>