<?

if(isset($_GET['denied'])){
	$get = $_GET['denied'];

	if($get == 'denied'){
		$msg = 'Você não tem permissão para acessar ao conteudo desta pagina, veja suas autorizações com seu superior!';
		alert::Warning($msg);
	}else

	if($get == 'chamado'){
		$msg = 'Você possui chamados <b>Concluídos</b> sem validar!<p> Por favor verifique seu chamado concluído e valide se está de acordo e finalize-o.';
		alert::Warning($msg);
	}
}

?>
