<?php
// models/UserModel.php

class UserModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findUser($login, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE loginuser = ? AND senhauser = ?");
        $stmt->execute([$login, $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setSession($userInfo)
    {
        $_SESSION['login'] = true;
        $_SESSION['iduser'] = $userInfo['iduser'];
        $_SESSION['loginuser'] = $userInfo['loginuser'];
        $_SESSION['senhauser'] = $userInfo['senhauser'];
        $_SESSION['nomeuser'] = $userInfo['nomeuser'];
        $_SESSION['email'] = $userInfo['email'];
        $_SESSION['id_access'] = $userInfo['id_access'];
        $_SESSION['id_cargo'] = $userInfo['id_cargo'];
        $_SESSION['id_dept'] = $userInfo['id_dept'];
        $_SESSION['avataruser'] = $userInfo['avataruser'];
    }
}
?>
