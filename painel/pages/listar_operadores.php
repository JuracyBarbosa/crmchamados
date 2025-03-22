<?
//verifica access
checkAccessPage('listaoperador','s','denied');

if(isset($_GET['excluir'])){
	$idExcluir = intval($_GET['excluir']);
	$selectoperador = select::seleciona('operadores','idoperator = ?', array($idExcluir));
    //declaro nome do operador
    $nameoperador = $selectoperador['surname'];
    if($selectoperador > 0){
        delete::excluir('operadores', 'idoperator', $idExcluir, $nameoperador, 'operador');
    }
}

$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 5;

$operadores = select::listOperadoresOrderID(($paginaAtual - 1) * $porPagina,$porPagina);
	
?>

<div class="box_content">
	<h2><i class="fa-solid fa-users-line"></i> Usuários Cadastrados</h2>
	<div class="wraper_table">
		<table>
			<tr>
				<td><i class="fa-solid fa-id-badge"></i> Apelido</td>
				<td><i class="fa-solid fa-user-tie"></i> Função</td>
				<td><i class="fas fa-calendar-alt"></i> Data cadastro</td>
				<td><i class="fas fa-edit"></i>Edit</td>
				<td><i class="fas fa-trash-alt"></i> Del</td>
			</tr>
		<?
		foreach($operadores as $key => $value){
			$data = date_create($value['datecadastro']);
		?>
			<tr>
				<td><? echo $value['surname']; ?></td>
				<td><? echo $value['funcao']; ?></td>
				<td><? echo date_format($data,"d/m/Y H:i:s"); ?></td>
				<td><a class="btn edit" href="<? echo INCLUDE_PATH_PAINEL ?>editar_operador?id=<? echo $value['idoperator']; ?>"><i class="fas fa-pencil-alt"></i></a></td>
				<td><a actionBtn="delete" class="btn delete" href="<? INCLUDE_PATH_PAINEL ?>listar_operadores?excluir=<? echo $value['idoperator']; ?>"><i class="fas fa-trash"></i></a></td>
			</tr>
		<?}?>
		</table>
	</div>
	<div class="paginacao">
		<?
		$totalPaginas = ceil(count(select::All('operadores')) / $porPagina);
		
		for($pag = 1; $pag <= $totalPaginas; $pag++){
			if($pag == $paginaAtual)
			echo '<a class="page_selected" href="'.INCLUDE_PATH_PAINEL.'listar_operadores?pagina='.$pag.'">'.$pag.'</a>';
		else
			echo '<a href="'.INCLUDE_PATH_PAINEL.'listar_operadores?pagina='.$pag.'">'.$pag.'</a>';
		}
		?>
	</div>
</div>