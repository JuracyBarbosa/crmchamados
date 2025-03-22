<?php
require_once '../config.php';
require_once 'models/UserModel.php';
require_once 'controllers/AuthController.php';

$db = MySql::conectar();
$userModel = new UserModel($db);
$authController = new AuthController($userModel);

if (isset($_POST['acao'])) {
    $login = $_POST['loginuser'];
    $password = $_POST['senhauser'];
    $remember = isset($_POST['lembrar']);
    $errorMessage = $authController->login($login, $password, $remember);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Painel de Controle</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>css/fontawesome_v5.13.css">
    <link href="<?php echo INCLUDE_PATH_PAINEL ?>css/style.css" rel="stylesheet" />
    <link rel="icon" href="<?php echo INCLUDE_PATH; ?>favicon.ico" type="image/x-icon" />
</head>
<body>
    <div class="box_login">
        <?php if (isset($errorMessage)) echo '<div class="erro_box"><i class="fa fa-times"></i> ' . $errorMessage . '</div>'; ?>
        <h2>Efetue o Login:</h2>
        <form method="post">
            <input type="text" name="loginuser" placeholder="Login.." required />
            <input type="password" name="senhauser" placeholder="Senha.." required />
            <div class="form_group_login left">
                <input type="submit" name="acao" value="Login" />
                <a class="login-home" href="<?php echo INCLUDE_PATH; ?>">Home</a>
            </div>
            <div class="form_group_login right">
                <label>Lembrar-me</label>
                <input type="checkbox" name="lembrar" />
            </div>
            <div class="clear"></div>
        </form>
    </div>
</body>
</html>
