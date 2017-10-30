<?php

require_once('parser/Parser.php');

$parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
$traverser = new PhpParser\NodeTraverser;
$visitor = new parser\Visitor();
$traverser->addVisitor($visitor);

try {
    //$code = file_get_contents($fileName);


    $code = <<<'EOD'
<?php
namespace spec\hs\fire\protection\api\model\property;

/**
 */
class PropertyProduct {

    /**
     * @var \DateTime
     */
    private $creationDate;

    /**
     * @var string
     */
    private $id;

    /**
     * @var \spec\hs\fire\protection\api\model\product\Product
     */
    private $product;

    /**
     * @var \spec\hs\fire\protection\api\model\property\Property
     */
    private $property;

    /**
     * @return \DateTime
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return \spec\hs\fire\protection\api\model\product\Product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * @return \spec\hs\fire\protection\api\model\property\Property
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * @param \DateTime $creationDate
     * @return self
     */
    public function setCreationDate(\DateTime $creationDate) {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * @param \spec\hs\fire\protection\api\model\product\Product $product
     * @return self
     */
    public function setProduct(\spec\hs\fire\protection\api\model\product\Product $product = null) {
        $this->product = $product;
        return $this;
    }

    /**
     * @param \spec\hs\fire\protection\api\model\property\Property $property
     * @return self
     */
    public function setProperty(\spec\hs\fire\protection\api\model\property\Property $property = null) {
        $this->property = $property;
        return $this;
    }
}
EOD;

    // parse
    $stmts = $parser->parse($code);

    // traverse
    $stmts = $traverser->traverse($stmts);

//    print_r($stmts);

    echo "<pre><code>" . $visitor->getTypescriptClass() . "</code></pre>";

} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}
