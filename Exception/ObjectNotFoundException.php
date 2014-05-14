<?php

namespace Nazka\ObjectManagerBundle\Exception;

/**
 * Description of ObjectNotFoundException
 *
 * @author javier
 */
class ObjectNotFoundException extends \RuntimeException
{
    protected $message = "Object not found";
}