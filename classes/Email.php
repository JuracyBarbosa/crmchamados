<?

class Email
{
	private $mailer;
	public function __construct(
		$host = 'smtp.sementestropical.com.br',
		$username = 'envio.crm@sementestropical.com.br',
		$senha = '3nvi0crm@st',
		$name = 'HelpDesk CRM System'
		)
	{
		$this->mailer = new PHPMailer;
		
		$this->mailer->isSMTP();
		$this->mailer->Host = $host;
		$this->mailer->SMTPAuth = true;
		$this->mailer->Username = $username;
		$this->mailer->Password = $senha;
		$this->mailer->SMTPSecure = 'STARTTLS';
		$this->mailer->Port = 587;
		$this->mailer->SMTPOptions = array( 'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true ) );
		
		$this->mailer->setFrom($username, $name);
		$this->mailer->isHTML(true);
		$this->mailer->CharSet = 'UTF-8';

	}

	public function message_ID($IDmessage){
		if($this->mailer->MessageID = $IDmessage){
			return $IDmessage;
		}
	}

	public function addReplyTo($email, $nome){
		$this->mailer->addReplyTo($email, $nome);
	}
	
	public function addAdress($email,$nome) {
		$this->mailer->addAddress($email,$nome);
	}

	public function addAdressCC($email, $nome){
		$this->mailer->addCC($email, $nome);
	}

	public function addAttachment($arquivo){
		$this->mailer->addAttachment(PATH_ANEXO.$arquivo);
	}

	public function addCustomHeader($parametro, $IDmsg){
		$this->mailer->addCustomHeader($parametro, $IDmsg);
	}

	public function addEmbeddedImage($img, $ref){
		$this->mailer->addEmbeddedImage($img, $ref);
	}
	
	public function formatarEmail($info){
		$this->mailer->Subject = $info['assunto'];
		$this->mailer->Body = $info['corpo'];
		$this->mailer->AltBody = strip_tags($info['corpo']);
	}
	
	public function enviarEmail(){
		if($this->mailer->send()){
			return true;
		} else {
			return false;
		}
	}

	public function ClearAllRecipients(){
		$this->mailer->ClearAllRecipients();
	}
}
?>