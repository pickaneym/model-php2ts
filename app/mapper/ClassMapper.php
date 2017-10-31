<?php

namespace mapper;


class ClassMapper{
    /**
     * @var \mapper\ImportMapper[];
     */
    private $imports;

    /**
     * @var string
     */
    private $className;

    /**
     * @var \mapper\PropertyMapper[]
     */
    private $properties;

    /**
     * @var \mapper\AccessorMapper[]
     */
    private $accessors;

    public function __construct($name){
        $this->imports = [];
        $this->className = $name;
        $this->properties = [];
        $this->accessors = [];
    }

    public function __toString(){
        return $this->mapClass($this->mapImports(), $this->mapProperties(), $this->mapAccessors());
    }

    /**
     * @return string
     */
    private function mapImports(){
        $imports = implode(";\n", array_map(function ($i){
            return (string)$i;
        }, $this->imports));

        return count($this->imports) > 0 ? $imports . ";\n\n" : '';
    }

    /**
     * @param string $imports
     * @param string $properties
     * @param string $methods
     * @return string
     */
    private function mapClass($imports, $properties, $methods){
        $class = $imports;
        $class .= "export class {$this->className} {\n";
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

    /**
     * @return \mapper\ImportMapper[]
     */
    public function getImports(): array{
        return $this->imports;
    }

    /**
     * @param \mapper\ImportMapper[] $imports
     */
    public function setImports(array $imports){
        $this->imports = $imports;
    }


}


