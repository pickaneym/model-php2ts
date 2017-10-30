<?php

namespace mapper;


class ImportMapper{

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $path;

    public function __toString(){
        return "import { {$this->class} } from '{$this->path}'";
    }

    /**
     * @return string
     */
    public function getClass(): string{
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class){
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getPath(): string{
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path){
        $this->path = $path;
    }

}