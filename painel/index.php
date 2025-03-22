<?
require_once '../config.php';
require_once '../classes/painel.php';

if(painel::logado() == false) {
	include('login.php');
}else{
	include('main.php');
}

?>