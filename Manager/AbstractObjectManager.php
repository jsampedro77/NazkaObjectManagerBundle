<?php

namespace Nazka\ObjectManagerBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Nazka\ObjectManagerBundle\Exception;

/**
 * AbstractManager
 *
 * @author javier
 */
abstract class AbstractObjectManager
{

    protected $om;
    protected $validator;
    protected $repository;
    protected $eventDispatcher;

    public function __construct(ObjectManager $om, Validator $validator, EventDispatcherInterface $eventDispatcher)
    {
        $this->om = $om;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find($id)
    {
        $object = $this->getRepository()->find($id);
        $class = $this->getClass();
        if (!$object instanceof $class) {
            throw new Exception\ObjectNotFoundException(sprintf(
                    'Object  with id %s could not be found', $id));
        }

        return $object;
    }

    /**
     * Find All with optional sorting options
     * i.e array('field' => 'ASC')
     * 
     * @param array $sort
     * @return type
     */
    public function findAll(array $sort = null)
    {
        return $this->getRepository()->findBy(array(), $sort);
    }

    public function remove($object)
    {
        $this->om->remove($object);
        $this->om->flush();
    }

    public function create()
    {
        $class = $this->getClass();

        return new $class();
    }

    public function save($object, $flush = false)
    {
        $class = $this->getClass();

        if (!$object instanceof $class) {
            throw new \Exception(sprintf('You must provide an instance of %s, %s provided', $class, get_class($object)));
        }

        $errors = $this->validator->validate($object);

        if (0 == count($errors)) {
            $this->om->persist($object);
        } else {
            throw new ValidatorException($errors);
        }

        if ($flush) {
            $this->om->flush();
        }

        return $object;
    }

    public function flush()
    {
        $this->om->flush();
    }

    public function supports($object)
    {
        return get_class($object) === $this->getClass();
    }

    /**
     * 
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository()
    {
        return $this->repository? : $this->om->getRepository($this->getClass());
    }

    abstract protected function getClass();
}
