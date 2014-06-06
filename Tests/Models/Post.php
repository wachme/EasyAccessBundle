<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests\Models;

class Post {
    private $id;
    private $title;
    private $content;
    
    public function __construct($id=null, $title=null, $content=null) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    public function getContent() {
        return $this->content;
    }
}