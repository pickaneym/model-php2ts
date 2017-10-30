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
    private $typeInference;

    public function __toString(){
        return "{$this->name}: {$this->typeInference}";
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

}