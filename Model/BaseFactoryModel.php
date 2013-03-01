<?php

namespace Clamidity\BaseBundle\Model;

use Clamidity\BaseBundle\Model\BaseFactoryInterface;
use Clamidity\BaseBundle\Model\BaseNamespaceParser;

/**
 * Description of BaseFactoryModel
 *
 * @author Michael Shattuck <ms2474@gmail.com>
 */
abstract class BaseFactoryModel implements BaseFactoryInterface
{
    /* @var $parser \Clamidity\BaseBundle\Model\BaseNamespaceParser */
    protected $parser;


    public function __construct()
    {
        $this->parser = new BaseNamespaceParser();
    }

//    /**
//     * {@inheritDoc}
//     */
//    abstract function get($class);
}