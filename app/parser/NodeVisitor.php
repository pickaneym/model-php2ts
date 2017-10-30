<?php

namespace parser;

ini_set('display_errors', 1);
ini_set('xdebug.max_nesting_level', 3000);

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../mapper/ImportMapper.php');
require_once(__DIR__ . '/../mapper/ClassMapper.php');
require_once(__DIR__ . '/../mapper/PropertyMapper.php');
require_once(__DIR__ . '/../mapper/AccessorMapper.php');
require_once(__DIR__ . '/../mapper/ParameterMapper.php');

use PhpParser;
use PhpParser\Node;
use mapper;

/**
 * Class NodeVisitor
 */
class NodeVisitor extends PhpParser\NodeVisitorAbstract{

    /**
     * @var \mapper\ClassMapper[]
     */
    private $classMap = [];

    /**
     * @var \mapper\ImportMapper[]
     */
    private $imports = [];

    /**
     * @var \mapper\ClassMapper
     */
    private $classMapper;

    /**
     * @var PhpParser\Node\Stmt\Namespace_
     */
    private $currentNodeNamespace;

    /**
     * @var PhpParser\Node\Stmt\Class_
     */
    private $currentNodeClass;

    /**
     * @var PhpParser\Node\Stmt\Property
     */
    private $currentNodeProperty;

    /**
     * @param \PhpParser\Node $node
     * @return void
     */
    public function enterNode(Node $node){
        if ($node instanceof PhpParser\Node\Stmt\Namespace_) {
            $this->currentNodeNamespace = $node;
        }

        if ($node instanceof PhpParser\Node\Stmt\Class_) {
            $this->currentNodeClass = $node;
            $this->classMap[] = $this->classMapper = new mapper\ClassMapper($this->currentNodeClass->name);
        }

        if ($node instanceof PhpParser\Node\Stmt\Property) {
            $this->currentNodeProperty = $node;
            $property = $this->generateProperty($this->currentNodeProperty);

            $this->classMapper->appendProperty($property);

            if ($this->currentNodeProperty->isPrivate()) {
                $getter = $this->generateGetter($this->currentNodeProperty);
                $setter = $this->generateSetter($this->currentNodeProperty);

                $this->classMapper->appendAccessor($getter);
                $this->classMapper->appendAccessor($setter);
            }
        }
    }

    /**
     * @param array $nodes
     * @return void
     */
    public function afterTraverse(array $nodes){
        $this->classMapper->setImports($this->imports);
    }

    /**
     * @return string
     */
    public function getTypescriptClass(){
        return implode("\n\n", array_map(function ($i){
            return (string)$i;
        }, $this->classMap));
    }

    /**
     * @param \PhpParser\Comment|null $phpDoc
     * @return string
     */
    private function parsePhpDocForPropertyTypeInference($phpDoc){
        $type = 'any';

        if ($phpDoc !== null) {
            $docComment = $phpDoc->getText();
            $isArray = $this->isPropertyAnArray($docComment);

            try {
                // RawTypes doesn't care whether the var type is declared as a single object or an array
                $rawPhpType = $this->getDocCommentVarType($docComment);

                /**
                 * If doc comment contains a local namespace get that model
                 * else just check for basic types
                 */
                if (substr_count($rawPhpType, "\\") > 0) {
                    $namespace = explode("\\", $rawPhpType);
                    $propertyClassName = $namespace[count($namespace) - 1];
                    $type = $propertyClassName;
                    $type = $isArray ? $type . "[]" : $type;
                } else {
                    $t = trim(strtolower($rawPhpType));

                    switch ($t) {
                        case 'integer':
                        case 'int':
                        case 'float':
                            $type = $isArray ? 'number[]' : 'number';
                            break;
                        case 'string':
                        case 'datetime':
                            $type = $isArray ? 'string[]' : 'string';
                            break;
                    }
                }

            } catch (\InvalidDocCommentException $e) {
                return $type;
            }
        }

        return $type;
    }

    /**
     * @var PhpParser\Node\Stmt\Property $property
     * @return \mapper\PropertyMapper
     */
    private function generateProperty($property){
        $propertyName = $property->props[0]->name;
        $typeInference = $this->parsePhpDocForPropertyTypeInference($property->getDocComment());

        // RawTypes doesn't care whether the var type is declared as a single object or an array
        $rawTSType = str_replace(['[', ']'], '', $typeInference);

        if (!$this->belongsToTSBasicType($rawTSType) && $this->currentNodeNamespace !== null) {
            $importPath = $this->getTSImportPath($property, $this->currentNodeNamespace);
            $this->prepareImportStmt($rawTSType, $importPath);
        }

        $propertyMapper = new mapper\PropertyMapper();
        $propertyMapper->setName($property->isPrivate() ? '_' . $propertyName : $propertyName);
        $propertyMapper->setTypeInference($typeInference);

        if ($property->isPublic()) {
            $propertyMapper->setAccessModifier('public');
        } elseif ($property->isPrivate()) {
            $propertyMapper->setAccessModifier('private');
        } elseif ($property->isProtected()) {
            $propertyMapper->setAccessModifier('protected');
        }

        return $propertyMapper;
    }

    /**
     * @param string $type
     * @return bool
     */
    private function belongsToTSBasicType($type){
        $tsBasicTypes = ['string', 'number', 'boolean', 'never', 'null', 'undefined', 'void', 'any'];
        return in_array($type, $tsBasicTypes);
    }

    /**
     * @throws \InvalidDocCommentException
     * @param string $string
     * @return string
     */
    private function getDocCommentVarType($string){
        if (preg_match('/@var[ \t]+([a-z0-9\\\\]+)/i', $string, $matches)) {
            return ltrim($matches[1], "\\"); // Remove leading backslash
        } else {
            throw new \InvalidDocCommentException('Invalid doc comment!');
        }
    }

    /**
     * @param \PhpParser\Node\Stmt\Property $property
     * @param \PhpParser\Node\Stmt\Namespace_ $currentNodeNamespace
     * @return string
     */
    private function getTSImportPath($property, $currentNodeNamespace){
        $importPath = '';

        $currentClass_NamespaceArray = $currentNodeNamespace->name->parts;
        $currentClass_NamespaceArray[] = $this->currentNodeClass->name;

        $rawPhpTypeOfProperty = $this->getDocCommentVarType($property->getDocComment());
        $propertyType_NamespaceArray = explode("\\", $rawPhpTypeOfProperty);

        // NAL == `namespace array length`
        $currentClassNAL = count($currentClass_NamespaceArray) - 1;

        /**
         * Get the last matched path of the current class namespace and the property type namespace for
         * us to be able to measure the relative distance of the current file to the target file.
         */
        $lastMatchedPathArray = array_intersect($currentClass_NamespaceArray, $propertyType_NamespaceArray);

        /**
         * Get index of last matched dirname to be used to get the the root path of the target path
         * and calculate the directory level of the property type class from the current namespace
         */
        $lastMatchedDirNameIndex = count($lastMatchedPathArray) - 1;

        $propertyRootPathArray = array_slice($propertyType_NamespaceArray, $lastMatchedDirNameIndex + 1);
        $propertyClassNameIndex = count($propertyRootPathArray) - 1;

        $propertyRootPath = implode("/", array_map(function ($dir, $k) use ($propertyClassNameIndex){
            return $k == $propertyClassNameIndex ? camel2dashed($dir) . '.model' : $dir;
        }, $propertyRootPathArray, array_keys($propertyRootPathArray)));

        $propertyClassDirectoryLevel = $currentClassNAL - $lastMatchedDirNameIndex;

        switch ($propertyClassDirectoryLevel) {
            case 1 :
                $importPath = './' . $propertyRootPath;
                break;
            case 2:
                $importPath = '../' . $propertyRootPath;
                break;
            case 3:
                $importPath = '../../' . $propertyRootPath;
                break;
            case 4:
                $importPath = '../../../' . $propertyRootPath;
                break;
            case 5:
                $importPath = '../../../../' . $propertyRootPath;
        }

        return $importPath;
    }

    private function getPropertyRootPath(){

    }

    /**
     * @param $docComment
     * @return bool
     */
    private function isPropertyAnArray($docComment){
        return substr_count($docComment, "[") > 0;
    }

    /**
     * @param string $type
     * @param string $path
     */
    private function prepareImportStmt($type, $path){
        $import = new mapper\ImportMapper();
        $import->setClass($type);
        $import->setPath($path);
        $this->imports[] = $import;
    }

    /**
     * @var PhpParser\Node\Stmt\Property $property
     * @return \mapper\AccessorMapper
     */
    private function generateGetter($property){
        $getter = new mapper\AccessorMapper('get');
        $getter->setName($property->props[0]->name);
        $getter->setReturnType($this->parsePhpDocForPropertyTypeInference($property->getDocComment()));
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
        $parameter->setTypeInference($this->parsePhpDocForPropertyTypeInference($property->getDocComment()));

        $setter = new mapper\AccessorMapper('set');
        $setter->setName($property->props[0]->name);
        $setter->setParameter($parameter);

        return $setter;
    }

}