<?php

namespace Pn\JobBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Child
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pn\PagesBundle\Entity\ChildRepository")
 */
class Child
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="age", type="integer")
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\Column(name="specialfeatures", type="array")
     */
    private $specialfeatures;

    /**
     * @ORM\ManyToOne(targetEntity="Pn\CoreBundle\Entity\Mother")
     */
    private $mother;









    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Child
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set age
     *
     * @param integer $age
     * @return Child
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return integer 
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Child
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set specialfeatures
     *
     * @param array $specialfeatures
     * @return Child
     */
    public function setSpecialfeatures($specialfeatures)
    {
        $this->specialfeatures = $specialfeatures;

        return $this;
    }

    /**
     * Get specialfeatures
     *
     * @return array 
     */
    public function getSpecialfeatures()
    {
        return $this->specialfeatures;
    }

    /**
     * Set mother
     *
     * @param \Pn\CoreBundle\Entity\Mother $mother
     * @return Child
     */
    public function setMother(\Pn\CoreBundle\Entity\Mother $mother = null)
    {
        $this->mother = $mother;

        return $this;
    }

    /**
     * Get mother
     *
     * @return \Pn\CoreBundle\Entity\Mother 
     */
    public function getMother()
    {
        return $this->mother;
    }
}