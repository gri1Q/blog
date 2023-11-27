<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\Users\User;
use MyProject\View\View;


class UsersController
{
    private $view;

    public function __construct()
    {
        $this->view = new View(__DIR__ . '/../../../templates');
    }
    public function signUp()
    {
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST);
            } catch (InvalidArgumentException $e) {

                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }
        }

        $this->view->renderHtml('users/signUp.php');
    }
}
