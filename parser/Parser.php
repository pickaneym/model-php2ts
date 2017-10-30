<?php

namespace parser;

ini_set('display_errors', 1);
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../mapper/ClassMapper.php');
require_once(__DIR__ . '/../mapper/PropertyMapper.php');
require_once(__DIR__ . '/../mapper/AccessorMapper.php');
require_once(__DIR__ . '/../mapper/ParameterMapper.php');

use PhpParser;
use PhpParser\Node;
use mapper;

class Visitor extends PhpParser\NodeVisitorAbstract{

    /**
     * @var \mapper\ClassMapper[]
     */
    private $output = [];

    /**
     * @var \mapper\ClassMapper
     */
    private $classMapper;

    /**
     * @param \PhpParser\Node $node
     * @return void
     */
    public function enterNode(Node $node){
        if ($node instanceof PhpParser\Node\Stmt\Class_) {
            $this->output[] = $this->classMapper = new mapper\ClassMapper($node->name);
        }

        if ($node instanceof PhpParser\Node\Stmt\Property) {
            $property = $this->generateProperty($node);
            $getter = $this->generateGetter($node);
            $setter = $this->generateSetter($node);

            $this->classMapper->appendProperty($property);

            if ($node->isPrivate()) {
                $this->classMapper->appendAccessor($getter);
                $this->classMapper->appendAccessor($setter);
            }
        }

    }

    /**
     * @return string
     */
    public function getTypescriptClass(){
        return implode("\n\n", array_map(function ($i){
            return (string)$i;
        }, $this->output));
    }

    /**
     * @param \PhpParser\Comment|null $phpDoc
     * @return string
     */
    private function parsePhpDocForProperty($phpDoc){
        $result = 'any';

        if ($phpDoc !== null) {
            if (preg_match('/@var[ \t]+([a-z0-9]+)/i', $phpDoc->getText(), $matches)) {
                $t = trim(strtolower($matches[1]));

                switch ($t) {
                    case 'integer':
                    case 'int':
                    case 'float':
                        $result = 'number';
                        break;
                    case 'string':
                        $result = 'string';
                        break;
                    case '\datetime';
                        $result = 'Date';
                        break;
                }
            }
        }

        return $result;
    }

    /**
     * @var PhpParser\Node\Stmt\Property $property
     * @return \mapper\PropertyMapper
     */
    private function generateProperty($property){
        $propertyName = $property->props[0]->name;
        $typeInference = $this->parsePhpDocForProperty($property->getDocComment());

        $propertyMapper = new mapper\PropertyMapper();
        $propertyMapper->setName($propertyName);
        $propertyMapper->setType($typeInference);

        if ($property->isPublic()) {
            $propertyMapper->setAccessModifier('public');
        } elseif ($property->isPrivate()) {
            $propertyMapper->setName('_' . $propertyName);
            $propertyMapper->setAccessModifier('private');
        } elseif ($property->isProtected()) {
            $propertyMapper->setAccessModifier('protected');
        }

        return $propertyMapper;
    }

    /**
     * @var PhpParser\Node\Stmt\Property $property
     * @return \mapper\AccessorMapper
     */
    private function generateGetter($property){
        $getter = new mapper\AccessorMapper('get');
        $getter->setName($property->props[0]->name);
        $getter->setReturnType($this->parsePhpDocForProperty($property->getDocComment()));
        return $getter;
    }

    /**
     * @var PhpParser\Node\Stmt\Property $property
     * @return \mapper\AccessorMapper
     */
    private function generateSetter($property){
        $propertyName = $property->props[0]->name;

        $parameter = new mapper\ParameterMapper();
        $parameter->setName($propertyName);
        $parameter->setType($this->parsePhpDocForProperty($property->getDocComment()));

        $setter = new mapper\AccessorMapper('set');
        $setter->setName($property->props[0]->name);
        $setter->setParameter($parameter);

        return $setter;
    }
}