<?php
// create_product.php <name>
require_once "bootstrap.php";

$newProductName = $argv[1];

$respository = $entityManager->getRepository(Product::class);

$product = $respository->findOneBy([
    'name' => $newProductName
]);

dump($product->getCategories()->toArray());


// $product = new Product(1);
// $product->setName($newProductName);

// $category = new Category();
// $category->setName('RPG');

// // $product->addCategory($category);
// $category->setProduct($product);


// print_r($category);

// $entityManager->persist($product);
// $entityManager->persist($category);
// $entityManager->flush();

// echo "Created Product with ID " . $product->getId() . "\n";