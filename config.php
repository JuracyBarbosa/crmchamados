<?
session_start();
ob_start();
date_default_timezone_set('America/Cuiaba');

// $autoload = function($class){
// 	if($class == 'Email'){
// 		require_once('classes/phpmailer/PHPMailerAutoLoad.php');
// 	}
// 	include('classes/'.$class.'.php');
// };
// spl_autoload_register($autoload);

$autoload = function($class) {
    if ($class == 'Email') {
        require_once(__DIR__ . '/classes/phpmailer/PHPMailerAutoLoad.php');
    }

    // Força o carregamento do arquivo painel.php para classes específicas
    if (in_array($class, ['chamado','verificar','select','insert','upload','update','delete','alert','consulta','parametros'])) {
        require_once(__DIR__ . '/classes/painel.php');
        return;
    }

    $file = __DIR__ . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        throw new Exception("Arquivo da classe '{$class}' não encontrado em {$file}");
    }
};

spl_autoload_register($autoload);

//Parametros de caminhos
define('INCLUDE_PATH', 'http://localhost/crmintranet/');
define('INCLUDE_PATH_PAINEL', INCLUDE_PATH.'painel/');
define('INCLUDE_PATH_PAINEL_PAGES', INCLUDE_PATH.'painel/pages/');
define('INCLUDE_PATH_AVATAR', INCLUDE_PATH_PAINEL.'uploads/usuarios/avatar/');
define('DELET_AVATAR', 'uploads/usuarios/avatar/');
define('ANEXO_CHAMADO', INCLUDE_PATH_PAINEL.'uploads/chamados/');
define('UPLOAD_SLIDE', INCLUDE_PATH_PAINEL.'uploads/slides/');
define('UPLOAD_SAVE_SLIDE','/uploads/slides/');

define('BASE_DIR_PAINEL',__DIR__.'/painel');

//Parametros de envio de e-mail
define('MessageID','<'.md5('HELLO'.(idate("U")-1000000000).uniqid()).'@CRM.HelpDesk>');
define('PATH_ANEXO', './uploads/chamados/');

//Parametros de envio de e-mail automatico do CRM
define('EMAIL_MAXLOGIC','');
define('EMAIL_TI','');
define('EMAIL_COORDENADOR','barbosajuracy@outlook.com.br');

//Conexão BD
define('HOST','localhost');
define('USER','root');
define('PASSWORD','Projeto@');
define('DATABASE','crmchamado');

//Constante para o painel de controle
define('NOME_EMPRESA','Sementes Tropical');


/*Funções Menu*/
function selecionadoMenu($par){
	$url = explode('/',@$_GET['url'])[0];
	if($url == $par){
		echo 'class="menu_active"';
	}
}

function checkAccess($modulo,$check){
	$iduser = $_SESSION['iduser'];
	$idaccess = $_SESSION['id_access'];
	$sql = consulta::permissao('id_user', $iduser, $modulo, $check);
	
	if($sql > 0){
		return;
	}else if($idaccess == 1){
		return;
	}else{
		echo 'style="display:none;"';
	}
}

function checkAccessPage($modulo, $check, $tipo){
	$iduser = $_SESSION['iduser'];
	$idaccess = $_SESSION['id_access'];
	$sql = consulta::permissao('id_user', $iduser, $modulo, $check);
	
	if($sql > 0){
		return;
	}else if($idaccess == 1){
		return;
	}else{
		header('location: '.INCLUDE_PATH_PAINEL.'permissao_negada?denied='.urldecode($tipo));
	}
}

function checkaccesspageoperator($tipo){
	$iduser = $_SESSION['iduser'];
	$check = consulta::operator($iduser);

	if ($check > 0) {
		return;
	} else {
		header('location: '.INCLUDE_PATH_PAINEL.'permissao_negada?denied='.urldecode($tipo));
	}
}

function checkAccessMenu($modulo = '',$check){
	$iduser = $_SESSION['iduser'];
	$idaccess = $_SESSION['id_access'];
	$sql = consulta::permissaoMenu('id_user', $iduser, $modulo, $check);

	if($sql > 0){
		return;
	}else if($idaccess == 1){
		return;
	}else{
		echo 'style="display:none;"';
	}
	
}

function checkOperador($access = ''){
	$idoperador = $_SESSION['iduser'];
	$idaccess = $_SESSION['id_access'];
	$sql = consulta::operator($idoperador, $access);
	
	if($sql > 0){
		return;
	}else if($idaccess == 1){
		return;
	}else{
		echo 'style="display:none;"';
	}
}

function checkAccessMenuOperador($coluna){
	$idoperador = $_SESSION['iduser'];
	$idaccess = $_SESSION['id_access'];
	
	$sql = consulta::operator($idoperador);

	if($sql > 0){
		return;
	}else if($idaccess == 1){
		return;
	}else{
		echo 'style="display:none;"';
	}
	
}

function verificachamadoconcluido($tipo){
	$idsolicitante = $_SESSION['iduser'];

	$consultachmados = select::selected('chamados', 'id_user = '.$idsolicitante.' AND id_status = 4');
	foreach($consultachmados as $key => $resultch){
		$solicitante = $resultch['id_user'];
		if($solicitante > 0){
			header('location: '.INCLUDE_PATH_PAINEL.'permissao_negada?denied='.urldecode($tipo));
		}
	}
}

?>