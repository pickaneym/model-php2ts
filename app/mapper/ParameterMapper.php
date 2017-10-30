<?php

namespace mapper;


class ParameterMapper{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    public function __toString(){
        return "{$this->name}: {$this->type}";
    }

    /**
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name){
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string{
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type){
        $this->type = $type;
    }

}