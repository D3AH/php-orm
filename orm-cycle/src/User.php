<?php declare(strict_types=1);

namespace Example;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\Annotated\Annotation\Relation;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(
 *      indexes={
 *             @Index(columns = {"name"}, unique = true)
 *      }
 * )
 */
class User
{
    /**
     * @Column(type="primary")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;

    /** @Relation\HasMany(target="Phone", fkCreate=false, outerKey="user", innerKey="name") */
    protected $phones;

    public function __construct()
    {
        $this->phones = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhones()
    {
        return $this->phones;
    }
}

/**
 * @Entity
 */
class Phone
{
    /**
     * @Column(type="primary")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $phoneNumber;

    protected $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber): Phone
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function setUser($user): Phone
    {
        $this->user = $user;
        
        return $this;
    }
}