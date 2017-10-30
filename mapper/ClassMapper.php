<?php

namespace mapper;


class ClassMapper{
    /**
     * @var string
     */
    private $className;

    /**
     * @var \mapper\PropertyMapper[]
     */
    private $properties = [];

    /**
     * @var \mapper\AccessorMapper[]
     */
    private $accessors = [];

    public function __construct($name){
        $this->className = $name;
    }

    public function __toString(){
        return $this->mapClass($this->mapProperties(), $this->mapAccessors());
    }

    /**
     * @param string $properties
     * @param string $methods
     * @return string
     */
    private function mapClass($properties, $methods){
        $class = "export class {$this->className} {\n";
        $class .= $properties . ';';
        $class .= "\n\n";
        $class .= $methods;
        $class .= "\n}";
        return $class;
    }

    /**
     * @return string
     */
    private function mapProperties(){
        return implode(";\n", array_map(function ($p){
            return "  " . (string)$p;
        }, $this->properties));
    }

    /**
     * @return string
     */
    private function mapAccessors(){
        return implode(" \n", array_map(function ($m){
            return "  " . (string)$m;
        }, $this->accessors));
    }


    /**
     * @return string
     */
    public function getClassName(): string{
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className){
        $this->className = $className;
    }

    /**
     * @return \mapper\PropertyMapper[]
     */
    public function getProperties(): array{
        return $this->properties;
    }

    /**
     * @param \mapper\PropertyMapper[] $properties
     */
    public function setProperties(array $properties){
        $this->properties = $properties;
    }

    /**
     * @return \mapper\AccessorMapper[]
     */
    public function getAccessors(): array{
        return $this->accessors;
    }

    /**
     * @param \mapper\AccessorMapper[] $accessors
     */
    public function setAccessors(array $accessors){
        $this->accessors = $accessors;
    }

    /**
     * @param \mapper\PropertyMapper $property
     */
    public function appendProperty(PropertyMapper $property){
        $this->properties[] = $property;
    }

    /**
     * @param \mapper\AccessorMapper $accessor
     */
    public function appendAccessor(AccessorMapper $accessor){
        $this->accessors[] = $accessor;
    }

}


