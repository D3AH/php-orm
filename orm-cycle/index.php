<?php declare(strict_types=1);
include 'vendor/autoload.php';

use Cycle\ORM;
use Spiral\Database;

$dbal = new Database\DatabaseManager(
    new Database\Config\DatabaseConfig([
        'default'     => 'default',
        'databases'   => [
            'default' => ['connection' => 'mysql']
        ],
        'connections' => [
            'mysql' => [
                'driver'  => Database\Driver\MySQL\MySQLDriver::class,
                'connection' => 'mysql:host=127.0.0.1;dbname=orm_cycle',
                'username'   => 'root',
                'password'   => 'password',
            ]
        ]
    ])
);

print_r($dbal->database('default')->getTables());
$orm = new ORM\ORM(new ORM\Factory($dbal));

// Up schema
$finder = (new \Symfony\Component\Finder\Finder())->files()->in([__DIR__ . '/src']); // __DIR__ here is folder with entities
$classLocator = new \Spiral\Tokenizer\ClassLocator($finder);
print_r($classLocator->getClasses());

// Up schema generation pipeline.

use Cycle\Schema;
use Cycle\Annotated;
use Doctrine\Common\Annotations\AnnotationRegistry;

// Autoload annotations
AnnotationRegistry::registerLoader('class_exists');

$schema = (new Schema\Compiler())->compile(new Schema\Registry($dbal), [
    new Schema\Generator\ResetTables(),       // re-declared table schemas (remove columns)
    new Annotated\Embeddings($classLocator),  // register embeddable entities
    new Annotated\Entities($classLocator),    // register annotated entities
    new Annotated\MergeColumns(),             // add @Table column declarations
    new Schema\Generator\GenerateRelations(), // generate entity relations
    new Schema\Generator\ValidateEntities(),  // make sure all entity schemas are correct
    new Schema\Generator\RenderTables(),      // declare table schemas
    new Schema\Generator\RenderRelations(),   // declare relation keys and indexes
    new Annotated\MergeIndexes(),             // add @Table column declarations
    new Schema\Generator\SyncTables(),        // sync table changes to database
    new Schema\Generator\GenerateTypecast(),  // typecast non string columns
]);

$orm = $orm->withSchema(new ORM\Schema($schema));

// ! Work entity

// Persist entity

// $u = new \Example\User();
// $u->setName("Hello World2");

$phone = new \Example\Phone();
$phone->setPhoneNumber('0000');


// $u->getPhones()->add($phone);

$t = new ORM\Transaction($orm); // Like EntityManager

// $t->persist($u);
// // $t->persist($phone);
// $t->run();

// print_r($u);
// print_r($phone);

$users = $orm->getRepository(\Example\User::class)
    ->select()
    ->load('phones')
    ->fetchAll();

foreach ($users as $u) {
    $phone = new \Example\Phone();
    $phone->setPhoneNumber('0003');

    $u->getPhones()->add($phone);
    print_r($u->getPhones());

    $t->persist($u);
}

$t->run();