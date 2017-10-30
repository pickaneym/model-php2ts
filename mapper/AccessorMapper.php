<?php

namespace mapper;


/**
 * Class AccessorMapper
 * @package mapper
 */
class AccessorMapper{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $accessType;

    /**
     * @var \mapper\ParameterMapper
     */
    private $parameter;

    /**
     * @var string
     */
    private $returnType;

    private $isPrivate;


    public function __construct($accessType){
        $this->accessType = $accessType;
    }

    public function __toString(){
        if ($this->accessType === 'get') {
            return $this->getter();
        } else {
            return $this->setter();
        }
    }

    /**
     * @return string
     */
    private function getter(){
        $method = "{$this->accessType} {$this->name}(): {$this->returnType}  {\n";
        $method .= "    return this._{$this->name};";
        $method .= "\n  }\n";
        return $method;
    }

    /**
     * @return string
     */
    private function setter(){
        $parameterToString = (string)$this->parameter;
        $method = "{$this->accessType} {$this->name}({$parameterToString}) {\n";
        $method .= "    this._{$this->name} = {$this->parameter->getName()};";
        $method .= "\n  }\n";
        return $method;
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
    public function getAccessType(): string{
        return $this->accessType;
    }

    /**
     * @param string $accessType
     */
    public function setAccessType(string $accessType){
        $this->accessType = $accessType;
    }

    /**
     * @return \mapper\ParameterMapper
     */
    public function getParameter(): ParameterMapper{
        return $this->parameter;
    }

    /**
     * @param \mapper\ParameterMapper $parameter
     */
    public function setParameter(ParameterMapper $parameter){
        $this->parameter = $parameter;
    }

    /**
     * @return string
     */
    public function getReturnType(): string{
        return $this->returnType;
    }

    /**
     * @param string $returnType
     */
    public function setReturnType(string $returnType){
        $this->returnType = $returnType;
    }
}