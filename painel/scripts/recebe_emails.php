<?php

// Configuração IMAP
$hostname = '{imap.sementestropical.com.br:993/imap/ssl}INBOX';
$username = 'envio.crm@sementestropical.com.br';
$password = '3nvi0crm@st';

// Conexão IMAP
$inbox = imap_open($hostname, $username, $password) or die('Erro IMAP: ' . imap_last_error());
$emails = imap_search($inbox, 'UNSEEN');

if ($emails) {
    rsort($emails);

    foreach ($emails as $email_number) {
        $overview = imap_fetch_overview($inbox, $email_number, 0)[0];
        $message = imap_fetchbody($inbox, $email_number, 1.1);
        if (empty($message)) {
            $message = imap_fetchbody($inbox, $email_number, 1);
        }

        $remetente = $overview->from;
        $assunto   = mb_decode_mimeheader($overview->subject);
        $descricao = quoted_printable_decode($message);
        $data      = date('Y-m-d H:i:s');

        // Buscar cliente pelo e-mail (caso exista)
        preg_match('/<(.+?)>/', $remetente, $matches);
        $emailLimpo = $matches[1] ?? $remetente;

        $stmtCliente = $conn->prepare("SELECT id_cliente FROM cliente WHERE email = ?");
        $stmtCliente->execute([$emailLimpo]);
        $cliente = $stmtCliente->fetch();

        if ($cliente) {
            $idCliente = $cliente['id_cliente'];

            // Inserir chamado
            $stmtInsert = $conn->prepare("
                INSERT INTO chamado (assunto, descricao, status, id_cliente, data_abertura) 
                VALUES (?, ?, 'Aberto', ?, ?)
            ");
            $stmtInsert->execute([$assunto, $descricao, $idCliente, $data]);
        }

        // Marcar como lido
        imap_setflag_full($inbox, $email_number, "\\Seen");
    }
}

imap_close($inbox);
?>
