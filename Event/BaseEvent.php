<?php

namespace Clamidity\BaseBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Clamidity\BaseBundle\Model\EntityInterface;

/**
 * Base event class that can be sent directly or extended.
 * 
 * @author Michael Shattuck <ms2474@gmail.com>
 */
class BaseEvent extends Event
{
    protected $entity;

    /**
     *
     * @param EntityInterface $entity 
     */
    public function __construct(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return  Clamidity\BaseBundle\Model\EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }
}