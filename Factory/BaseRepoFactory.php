<?php

namespace Clamidity\BaseBundle\Factory;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\MappingException;
use Clamidity\BaseBundle\Repository\BaseRepository;
use Clamidity\BaseBundle\Model\BaseFactoryModel;

/**
 * Class for generating repositories using given class names. 
 * 
 * @author Michael Shattuck <ms2474@gmail.com> 
 */
class BaseRepoFactory extends BaseFactoryModel
{
    protected $em;
    protected $dispatcher;
    protected $repositories;

    /**
     *
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManager $em
     * @param Container $container 
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em)
    {
        parent::__construct();
        $this->em           = $em;
        $this->dispatcher   = $dispatcher;
        $this->repositories = array();
    }

    /**
     * Creates and returns a BaseRepository.
     *
     * @example $this->get('AcmeTestBundle:TestEntity');
     * @param string $class The short namespace of 
     * repository class requested.
     * @param boolean $custom Set to true if the repository
     * is an extension of the BaseRepository
     * 
     * @return BaseRepository|repoClass
     * @throws InvalidArgumentException 
     */
    public function get($class)
    {
        $shortClass = $this->getShortClass($class);

        if (array_key_exists($shortClass, $this->repositories)) {
            return $this->repositories[$class];
        }

        $path = $this->getPath($class);

        if ($path) {
            $this->repositories[$shortClass] = new $path($this->dispatcher, $this->em, $class);
        }
        else {
            $this->repositories[$shortClass] = $this->generateRepo($class);
        }

        return $this->repositories[$shortClass];
    }

    /**
     * Function for determining repository
     * class type.
     *
     * @param string $class
     * @return boolean 
     */
    protected function getPath($class)
    {
        if (!class_exists($class)) {
            return $this->parser->getPath($class, 'Repository', 'Repository', false);
        }

        return false;
    }

    /**
     * Function for getting the shorthand
     * namespace of a class.
     *
     * @param string $class
     * @return string 
     */
    protected function getShortClass($class)
    {
        if (class_exists($class)) {
            return $this->parseLongClass($class);
        }
        else {
            return $class;
        }
    }

    /**
     * Function for parsing a longhand
     * namespace of the given class
     * and returning the shorthand
     * namespace.
     *
     * @param string $class
     * @return string 
     */
    protected function parseLongClass($class)
    {
        $classData = explode('\\', $class);
        $count = count($classData) - 1;

        return $classData[0].$classData[1].':'.$classData[$count];
    }

    /**
     * Function for generating a base
     * repository.
     *
     * @param string $class
     * @return \Clamidity\BaseBundle\Repository\BaseRepository 
     */
    protected function generateRepo($class)
    {
        try {
            $metadata  = $this->em->getClassMetadata($class);
        }
        catch (\ErrorException $e) {
            $this->throwClassException($class);
        }

        $repoClass = $metadata->rootEntityName;

        if (!class_exists($repoClass)) {
            $this->throwClassException($class);
        }

        return new BaseRepository($this->dispatcher, $this->em, $repoClass); 
    }

    /**
     *
     * @param string $class
     * @throws InvalidArgumentException 
     */
    protected function throwClassException($class)
    {
        throw new InvalidArgumentException('Invalid Class: The class "'.$class.'" could not be found!');
    }
}