<?php
// controllers/AuthController.php

class AuthController
{
    private $userModel;

    public function __construct($userModel)
    {
        $this->userModel = $userModel;
    }

    public function login($login, $password, $remember = false)
    {
        $userInfo = $this->userModel->findUser($login, $password);
        if ($userInfo) {
            $this->userModel->setSession($userInfo);
            if ($remember) {
                setcookie('loginuser', $login, time() + (60 * 60 * 24), '/');
                setcookie('senhauser', $password, time() + (60 * 60 * 24), '/');
            }
            header('Location: ' . INCLUDE_PATH_PAINEL);
            exit();
        } else {
            return 'Usuário ou senha incorretos!';
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . INCLUDE_PATH . 'login.php');
        exit();
    }
}
?>
