<?php

namespace Model;

use PDO;

require_once('../vendor/autoload.php');
$dotenv = \Dotenv\Dotenv::createImmutable('../');

$dotenv->load();

class Model
{
    private string $login;
    private string $password;
    private string $salt = '1BC29B36F623BA82AAF6724FD3B16718';
    private string $saltLogin = 'C93D3BF7A7C4AFE94B64E30C2CE39F4F';
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(
            "mysql:host=" . $_ENV['host'] .
            ";dbname=" . $_ENV['namedatabase'] .
            ";port=" . $_ENV['port'] . ";",
            $_ENV['username'],
            $_ENV['password']);
    }

    public function getUserInfo($login)
    {
        $sql = "SELECT * 
        FROM MVCUsersDB.tableusers 
        WHERE login='$login';";
        $result = $this->pdo->prepare($sql);
        $result->execute();
        return $result;
    }

    public function getAllUsers()
    {
        $sql = "SELECT * 
        FROM MVCUsersDB.tableusers;";
        $result = $this->pdo->prepare($sql);
        $result->execute();
        return $result;
    }

    public function searchByLoginPassword()
    {
        $stmt = $this->pdo->prepare("SELECT * 
        FROM MVCUsersDB.tableusers 
        WHERE login='$this->login' 
          AND password='$this->password';");
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (count($result) != 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function setLP(string $login, string $password)
    {
        $this->login = $login;
        $this->password = md5($password . $this->salt);
    }

    public function getLoginHash($login)
    {
        return(md5($login.$this->saltLogin));
    }

    public function newUser()
    {
        if (count($this->getUserInfo($this->login)->fetchAll()) != 0) {
            return false;
        } else {
            $stmt = $this->pdo->prepare("insert into MVCUsersDB.tableusers values ('$this->login', '$this->password');");
            $stmt->execute();
            return true;
        }
    }

    public function getLogin()
    {
        return $this->login;
    }

}