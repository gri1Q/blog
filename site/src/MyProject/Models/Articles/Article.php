<?php

namespace MyProject\Models\Articles;

use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Users\User;

class Article extends ActiveRecordEntity
{

    protected $name;
    protected $text;
    protected $authorId;
    protected $createdAt;



    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
    public function getAuthor(): User
    {
        return User::getById($this->authorId);
    }
    public function getAuthorId()
    {
        return $this->authorId;
    }
    public function setText(string $text)
    {
        $this->text = $text;
    }
    public function setName(string $name)
    {
        $this->name = $name;
    }
    public function setAuthor(User $author): void
    {
        $this->authorId = $author->getId();
    }
    protected static function getTableName(): string
    {
        return 'articles';
    }
}
