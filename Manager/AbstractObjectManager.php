<?php

namespace Nazka\ObjectManagerBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator;
use Nazka\ObjectManagerBundle\Exception;

/**
 * Description of AbstractManager
 * 
 * Candidate to move to a company bundle
 *
 * @author javier
 */
abstract class AbstractObjectManager
{

    protected $om;
    protected $validator;
    protected $repository;

    public function __construct(ObjectManager $om, Validator $validator)
    {
        $this->om = $om;
        $this->validator = $validator;
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

    public function findAll()
    {
        return $this->getRepository()->findAll();
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
