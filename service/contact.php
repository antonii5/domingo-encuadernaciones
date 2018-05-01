<?php

if($_POST)
{
    require('constant.php');

    $user_name      = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
    $user_email     = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $user_phone     = filter_var($_POST["tel"], FILTER_SANITIZE_STRING);
    $business     = filter_var($_POST["business"], FILTER_SANITIZE_STRING);
    $content   = filter_var($_POST["mensaje"], FILTER_SANITIZE_STRING);
    $attachments = $_FILES['file'];




  if(empty($user_name)) {
		$empty[] = "<b>Nombre</b>";
	}
	if(empty($user_email)) {
		$empty[] = "<b>E-mail</b>";
	}
	if(empty($user_phone)) {
		$empty[] = "<b>Telf.</b>";
	}
  if(empty($business)) {
		$empty[] = "<b>Empresa</b>";
	}

	if(!empty($empty)) {
		$output = json_encode(array('type'=>'error', 'text' => implode(", ",$empty) . ' requeridos'));
        die($output);
	}

	if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)){ //email validation
	    $output = json_encode(array('type'=>'error', 'text' => '<b>'.$user_email.'</b> no es válido'));
		die($output);
	}

	//reCAPTCHA validation
	if (isset($_POST['g-recaptcha-response'])) {

		require('component/recaptcha/src/autoload.php');

		$recaptcha = new \ReCaptcha\ReCaptcha(SECRET_KEY, new \ReCaptcha\RequestMethod\SocketPost());

		$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

		  if (!$resp->isSuccess()) {
				$output = json_encode(array('type'=>'error', 'text' => '<b>Captcha</b> obligatorio'));
				die($output);
		  }
	}






	$toEmail = "apontwork@gmail.com";
	$mailHeaders = "From: " . $user_name . "<" . $user_email . ">\r\n";
	if (mail($toEmail, "[WEB] FORMULARIO CONTACTO", $content, $mailHeaders)) {
	    $output = json_encode(array('type'=>'message', 'text' => 'Hola '.$user_name .', gracias por contactar. Pronto un agente se pondrá en contacto!'));
	    die($output);

	} else {
	    $output = json_encode(array('type'=>'error', 'text' => 'Error el enviar el correo, contacta con'.SENDER_EMAIL));
	    die($output);
	}
}
