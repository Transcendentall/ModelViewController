<?php

namespace Controller;

use Model\Model;
use Twig\Environment;

class Controller
{
    private Environment $twig;
    private $url = 'http://localhost:63342';


    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function showUserInfo($login)
    {
        $model = new Model();
        $result = $model->getUserInfo($login);
        $users = $model->getAllUsers();
        foreach ($result as $curUser)
        {
            echo $this->twig->render('userprofile.twig', ['curUser' => $curUser, 'users' => $users]);
        }
    }

    public function unauthorized()
    {
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '/reg?') === 0)
        {
            echo $this->twig->render('registration.twig');
        }
        else if (strpos($uri, '/run_reg?') === 0)
        {
            $this->reg();
        }
        else if (strpos($uri, '/login?') === 0)
        {
            $this->authorisation();
            echo $this->twig->render('mainpage.twig');
        }
        else
        {
            echo $this->twig->render('mainpage.twig');
        }

    }

    public function authorized()
    {
        $model = new Model();
        if ($_COOKIE['lHash'] == $model->getLoginHash($_COOKIE['login']))
        {
            $this->showUserInfo($_COOKIE['login']);
        }
        else
        {
            echo '<h2>Не стоит подделывать куки, ♂fucking slave♂!</h2>';
            setcookie('login', '');
            setcookie('lHash', '');
        }

        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '/logout?') === 0)
        {
            setcookie('login', '');
            setcookie('lHash', '');
            header('Location: ' . $this->url);
        }
    }

    public function authorisation()
    {
        $model = new Model();
        $model->setLP($_GET['login'], $_GET['password']);
        $exists = $model->searchByLoginPassword();
        if ($exists)
        {
            setcookie('login', $model->getLogin(), time()+120);
            setcookie('lHash', $model->getLoginHash($model->getLogin()), time()+120);
            header('Location: ' . $this->url);
        }
        else
        {
            echo '<br><br><br><h2 style="text-align: center; font-family: Arial;">ОШИБКА! Логин и/или пароль не верны.</h2>';
        }
    }

    public function reg()
    {
        $model = new Model();
        $model->setLP($_GET['login'], $_GET['password']);
        $notExisted = $model->newUser();
        if ($notExisted == true)
        {
            echo '<br><br><h2 style="text-align: center; font-family: Arial;">Welcome to the club, buddy!</h2>';
            echo $this->twig->render('mainpage.twig');
        }
        else
        {
            echo '<br><br><h2 style="text-align: center; font-family: Arial;">Oh shit, I am sorry :(</h2>';
            echo $this->twig->render('registration.twig');
        }
    }
}

