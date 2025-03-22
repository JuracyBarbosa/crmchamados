<?
include('../config.php');

$data = array();

$assunto = 'Nova Mensagem do Site!';
$corpo = '';
foreach ($_POST as $key => $value) {
	$corpo.=ucfirst($key).": ".$value;
	$corpo.="<hr>";
}
$info = array('assunto'=> $assunto, 'corpo'=> $corpo);
$mail = new Email();
$mail->addAdress('juracybarbosa@outlook.com.br','Juracy Barbosa');
$mail->formatarEmail($info);
if($mail->enviarEmail()){
	$data['sucesso'] = true;
} else {
	$data['erro'] = true;
}

//$data['retorno'] = 'sucessoo!!';

die(json_encode($data));

?>