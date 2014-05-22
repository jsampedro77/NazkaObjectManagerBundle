NazkaObjectManagerBundle
========================

Basic Object Manager actions to work with Doctrine or MongoDB. Validates entities/documents before being persisted. Provides basic repository methods.




Installation
------------

Create a composer.json in your projects root-directory:

    {
        "require": {
            "nazka/object-manager-bundle": "*"
        }
    }

and run:

    curl -s http://getcomposer.org/installer | php
    php composer.phar install

Usage
-----

Create your manager class extending Nazka\ObjectManagerBundle\Manager\AbstractObjectManager and implement getClass() to point to the managed Entity/Document

    class NodeManager extends AbstractObjectManager
    {
        protected function getClass()
        {
            return 'Control\NodeBundle\Document\Node';
        }
    }

Create your manager service using the right parent service. An ORM Entity Manager should use *nazka_object_manager.abstract.mongodb.manager*, while and MongoDB Document Manager shoud use *nazka_object_manager.abstract.mongodb.manager* as service parent.
YAML sample:

    control_node.node.manager:
        class: Control\NodeBundle\Manager\NodeManager
        parent: nazka_object_manager.abstract.mongodb.manager
        
        
TODO
----
Dispatch events on entity/document actions