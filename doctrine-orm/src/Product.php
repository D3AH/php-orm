<?php

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="products"
 * )
 */
class Product
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /** 
     * @ORM\Id
     * @ORM\Column(type="string", unique=true) 
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="product", cascade={"persist"})
     */
    protected $categories;

    public function __construct($id) {
        $this->id = $id;
        $this->categories = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function addCategory(Category $category)
    {
        $category->setProduct($this);
        $this->categories[] = $category;
    }

    public function getCategories(): ArrayCollection
    {
        return $this->categories;
    }
}