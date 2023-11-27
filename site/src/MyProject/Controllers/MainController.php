<?php

namespace MyProject\Controllers;

use MyProject\Models\Articles\Article;
use MyProject\View\View;

class MainController
{
    private $view;

    public function __construct()
    {
        // var_dump(__DIR__);
        $this->view = new View(__DIR__ . '/../../../templates');

    }
    public function main()
    {
        $articles = Article::findAll();
        // echo "<pre>";
        // var_dump($articles);
        $this->view->renderHtml('main/main.php', ['articles' => $articles]);
    }
}
