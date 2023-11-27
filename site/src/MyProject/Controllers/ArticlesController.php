<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\NotFoundException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Users\User;
use MyProject\View\View;

class ArticlesController
{
    private $view;

    public function __construct()
    {
        $this->view = new View(__DIR__ . '/../../../templates');
    }

    public function view(int $articleId)
    {
        $result = Article::getById($articleId);

        if ($result === null) {
            throw new NotFoundException();
        }

        // var_dump($result);
        // $articleAuthor = User::getById($result->getAuthorId());
        $this->view->renderHtml('articles/view.php', ['article' => $result]);
    }
    public function edit(int $articleId)
    {
        $article = Article::getById($articleId);
        if ($article === null) {
            throw new NotFoundException();
        }
        $article->setName('name');
        $article->setText('text');
        $article->save();
    }

    public function add(): void
    {
        $author = User::getById(1);

        $article = new Article();
        $article->setAuthor($author);
        $article->setName('Новое название статьи');
        $article->setText('Новый текст статьи');

        $article->save();

        var_dump($article);
    }
    public function delete($articleId)
    {
        $article = Article::getById($articleId);
        if ($article === null) {
            $this->view->renderHtml('errors/NotFound.php', [], 404);
            return;
        }
        $article->delete();
        var_dump($article);
    }
}
