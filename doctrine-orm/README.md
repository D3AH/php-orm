# Doctrine

## Instalation
```bash
composer install
```

## Context and desired behaviour
Context:

Tables that have a primary key auto incremented but is not used.
Relationships between tables's string columns.

Desired behaviour:

Manage with Doctrine a relationship without primary keys and foreign keys.


## Workaround

Entities annotations:

Parent entity (Product)
```php
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
     * @ORM\Column(type="string") 
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

    ...
```

Child entity (Category)
```php
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

    ...
}
```

If we look what is Doctrine trying to do, Doctrine generate this SQL statements.
```bash
// Doctrine schema-tool
php vendor/bin/doctrine orm:schema-tool:update --dump-sql
```

```sql
CREATE TABLE products (id INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B3BA5A5A5E237E06 (name), PRIMARY KEY(id, name)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, product VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_3AF34668D34A04AD (product), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
ALTER TABLE categories ADD CONSTRAINT FK_3AF34668D34A04AD FOREIGN KEY (product) REFERENCES products (name);
```
> Working with MySQL

To replicate behaviour we make some changes.

First remove the primary key for name in product table.
```sql
CREATE TABLE products (id INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B3BA5A5A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
```

Remove the foreign key statement.
```diff
- ALTER TABLE categories ADD CONSTRAINT FK_3AF34668D34A04AD FOREIGN KEY (product) REFERENCES products (name);
```

The statements to execute are:
```sql
CREATE TABLE products (id INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B3BA5A5A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, product VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_3AF34668D34A04AD (product), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
```

You can found the used to persist de the product with category in `create_product.php`.
```php
// Usage: create_product.php <name>
require_once "bootstrap.php";

$newProductName = $argv[1];

$product = new Product(1);
$product->setName($newProductName);

$category = new Category();
$category->setName('RPG');

// $product->addCategory($category);
$category->setProduct($product);


print_r($category);

$entityManager->persist($product);
$entityManager->persist($category);
$entityManager->flush();

echo "Created Product with ID " . $product->getId() . "\n";
```
> @TODO Test if this work in bidirectional way 😅

After run `php create_product.php Icecream` the entites are persisted. 🎉