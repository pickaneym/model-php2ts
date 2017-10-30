<?php

namespace mapper;


class PropertyMapper{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $accessModifier;


    public function __toString(){
        return "{$this->accessModifier} {$this->name}: {$this->type}";
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

    /**
     * @return string
     */
    public function getAccessModifier(): string{
        return $this->accessModifier;
    }

    /**
     * @param string $accessModifier
     */
    public function setAccessModifier(string $accessModifier){
        $this->accessModifier = $accessModifier;
    }

}