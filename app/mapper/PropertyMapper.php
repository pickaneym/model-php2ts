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
    private $typeInference;

    /**
     * @var string
     */
    private $accessModifier;


    public function __toString(){
        return "{$this->accessModifier} {$this->name}: {$this->typeInference}";
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
    public function getTypeInference(): string{
        return $this->typeInference;
    }

    /**
     * @param string $typeInference
     */
    public function setTypeInference(string $typeInference){
        $this->typeInference = $typeInference;
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