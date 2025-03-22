<?
//Requer metodo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    function validarInput($campo) {
        return isset($_POST[$campo]) ? $_POST[$campo] : null;
    }
    
    if(isset($_POST['atender'])){

        $idchamado = validarInput('idchamado');
        $status = validarInput('status');
        $idsolicitante = validarInput('solicitante');
        $atendente = validarInput('atendente');
        $acao = 'atender';
        $funcao = validarInput('funcao');
        $data = 'iniciando';
        $status_registro = 'A';
            
        if(insert::atendechamado($idchamado, $atendente) || update::registrachamado($status, $atendente, $idchamado)){
            painel::alert('erro','Algo deu errado na inserção!');
        }else if(insert::andamentochamado($idchamado, $atendente, $idsolicitante, $data, $status, $status_registro) == true) {
            //Enviar e-mail
            $mail = new Email();

            $dadosEnvio = select::dadosparaenvio($idchamado);
            foreach ($dadosEnvio as $key => $resultEmail) {
                $mailSolicitante = $resultEmail['email'];
                $mailNome = $resultEmail['solicitante'];
                $atendente = $resultEmail['atendente'];
                $IDmessage = $resultEmail['idmessage'];
                $assunto = $resultEmail['descbreve'];
                $descricao = $resultEmail['descricao'];
                
                if($mailSolicitante == '') {
                    alert::alertaRespondeChamado($idchamado, $acao, $funcao);
                } else {

                    $mail->addReplyTo('ti@sementestropical.com.br', 'Departamento de TI');
                    $mail->addCustomHeader('In-Reply-To', $IDmessage);
                    $mail->addAdress($mailSolicitante, $mailNome);
                    $mail->addAdressCC(EMAIL_TI, 'TI');

                    $img = INCLUDE_PATH_PAINEL.'images/logo.png';
                    $mail->addEmbeddedImage($img, 'logo_ref');

                    $info = array('assunto' => $assunto, 'corpo' => parametros::assinaturach('atender', NULL, NULL, NULL, NULL, $atendente, NULL));
                    $mail->formatarEmail($info);

                    if ($mail->enviarEmail()) {
                        alert::alertaRespondeChamado($idchamado, $acao, $funcao);
                    }
                }
            }
        }
    } else

    //quando o chamado for retornado, e clicar em pegar chamado ele passa por aqui
    if(isset($_POST['pegachamado'])){

        $idchamado = $_POST['idchamado'];
        $status = $_POST['status'];
        $idsolicitante = $_POST['solicitante'];
        $atendente = $_POST['atendente'];
        $acao = 'atender';
        $funcao = $_POST['funcao'];
        $data = 'iniciando';
        $status_registro = 'A';
            
        if(update::registrachamado($status, $atendente, $idchamado)){
            painel::alert('erro','Algo deu errado na inserção!');
        }else if(insert::andamentochamado($idchamado, $atendente, $idsolicitante, $data, $status, $status_registro) == true) {
            //Enviar e-mail
            $mail = new Email();

            $dadosEnvio = select::dadosparaenvio($idchamado);
            foreach ($dadosEnvio as $key => $resultEmail)
                $mailSolicitante = $resultEmail['email'];
                $mailNome = $resultEmail['solicitante'];
                $atendente = $resultEmail['atendente'];
                $IDmessage = $resultEmail['idmessage'];
                $assunto = $resultEmail['descbreve'];
                $descricao = $resultEmail['descricao'];

            
            $mail->addReplyTo('ti@sementestropical.com.br', 'Departamento de TI');
            $mail->addCustomHeader('In-Reply-To', $IDmessage);
            $mail->addAdress($mailSolicitante, $mailNome);
            $mail->addAdressCC(EMAIL_TI, 'TI');

            $img = INCLUDE_PATH_PAINEL.'images/logo.png';
            $mail->addEmbeddedImage($img, 'logo_ref');

            $info = array('assunto' => $assunto, 'corpo' => parametros::assinaturach('atender', NULL, NULL, NULL, NULL, $atendente, NULL));
            $mail->formatarEmail($info);

            if ($mail->enviarEmail()) {
                alert::alertaRespondeChamado($idchamado, $acao, $funcao);
            }            
        }
    }

    //quando o chamado for para revalidação da nota, cai aqui.
    if (isset($_POST['revalidarch'])) {

        $idchamado = $_POST['idchamado'];
        $status = 4;
        $idsolicitante = $_POST['solicitante'];
        $atendente = $_POST['atendente'];
        $acao = 'concluir';
        $funcao = $_POST['funcao'];
        $data = 'iniciando';
        $status_registro = 'A';

        if ($_POST['revalidarch'] == 'Revalidar') {
            try {
                $reavaliar = update::reavaliarchamado($status, NULL, $idchamado);
            } catch (Exception $e) {
                painel::alert('erro', 'Algo deu errado na inserção!<br />' . $e->getMessage() . '');
            }
        }

        //Enviar e-mail
        $mail = new Email();

        $dadosEnvio = select::dadosparaenvio($idchamado);
        foreach ($dadosEnvio as $key => $resultEmail) {
            $mailSolicitante = $resultEmail['email'];
            $mailNome = $resultEmail['solicitante'];
            $atendente = $resultEmail['atendente'];
            $IDmessage = $resultEmail['idmessage'];
            $assunto = $resultEmail['descbreve'];
            $descricao = $resultEmail['descricao'];

            if ($mailSolicitante == '') {
                alert::alertaRespondeChamado($idchamado, $acao, $funcao);
            }

            $mail->addReplyTo('ti@sementestropical.com.br', 'Departamento de TI');
            $mail->addCustomHeader('In-Reply-To', $IDmessage);
            $mail->addAdress($mailSolicitante, $mailNome);
            $mail->addAdressCC(EMAIL_TI, 'TI');

            $img = INCLUDE_PATH_PAINEL.'images/logo.png';
            $mail->addEmbeddedImage($img, 'logo_ref');

            $info = array('assunto' => $assunto, 'corpo' => parametros::assinaturach('concluirchamado', NULL, $idchamado, NULL, NULL, $atendente, NULL));
            $mail->formatarEmail($info);

            if ($mail->enviarEmail()) {
                alert::alertaRespondeChamado($idchamado, $acao, $funcao);
            }
        }
    }
}

//ABAIXO TEREMOS REQUISIÇÕES FORA DO ENVIO POST
//ABAIXO REQUISIÇÕES AJAX!

if (isset($_GET['seq_pla_categoria'])) {
    $idcategoria = $_GET['seq_pla_categoria'];
    $returncategoria = select::obterSubcategorias($idcategoria);
    echo $returncategoria;
}else

if (isset($_GET['seq_pla_ocorrencia'])) {
    $seq_pla_ocorrencia = $_GET['seq_pla_ocorrencia'];
    $returnOcorrencia = select::obterOcorrencia($seq_pla_ocorrencia);
    echo $returnOcorrencia;
}

?>