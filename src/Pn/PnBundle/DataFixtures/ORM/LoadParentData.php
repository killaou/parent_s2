<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 27/03/14
 * Time: 15:36
 */

// src/Pn/PnBundle/DataFixtures/ORM/LoadParentData.php
namespace Pn\PnBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Pn\PnBundle\Entity\Pparent;

class LoadParentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $parent_anna = new Pparent();
        $parent_anna->setUser($em->merge($this->getReference('user-anna')));

        $em->persist($parent_anna);

        $em->flush();
    }

    public function getOrder()
    {
        return 5; // the order in which fixtures will be loaded
    }
}