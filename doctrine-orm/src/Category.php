<?php

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="categories"
 * )
 */
class Category
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** 
     * @ORM\Column(type="string") 
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="categories")
     * @ORM\JoinColumn(name="product", referencedColumnName="name")
     */
    protected $product;

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

    public function setProduct(Product $product)
    {
        $this->product = $product;
    }
}