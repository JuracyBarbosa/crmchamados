<?

include ('../classes/PHPMailer/PHPMailerAutoload.php');

$mail = new PHPMailer();
$mail->isSMTP();

$message_id = '<'.md5('HELLO'.(idate("U")-1000000000).uniqid()).'@CRM.HelpDesk>';

$mail->Host = 'smtp.sementestropical.com.br';
$mail->SMTPAuth = true;
$mail->Username = 'envio.crm@sementestropical.com.br';
$mail->Password = '3nvi0crm@st';
$mail->SMTPSecure = 'STARTTLS';
$mail->Port = 587;
$mail->SMTPOptions = array( 'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true ) );
$mail->MessageID = MessageID;
//Abaixo código debug para echoa o erro
$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->Debugoutput = 'HTML';
$mail->setLanguage('pt');

//Remetente, localhost não permite envio sem esses está setado.
$mail->setFrom('envio.crm@sementestropical.com.br', 'HelpDesk CRM System');
//$mail->addCustomHeader('In-Reply-To', 'id_message');
$mail->addAddress('juracy.junior@sementestropical.com.br','Juracy B');
//$mail->addCC('juracybarbosadosantosjr@gmail.com','juracy');
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->Subject = 'Espaço para assunto do E-mail';
$mail->Body = 'Aqui fica o conteudo do e-mail!';
//$mail->addAttachment(PATH_ANEXO.'chamado-1REG[649f246b8d163].png');
$enviado = $mail->send();

if($enviado){
    echo 'Seu e-mail foi enviado!!';
}else{
    echo 'Deu B.O: '.$mail->ErrorInfo;
}



?>