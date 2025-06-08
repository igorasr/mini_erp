<?php

class ProductService
{
  private ProductRepository $repository;

  public function __construct()
  {
    $this->repository = new ProductRepository();
  }

  public function getAllProducts(): array
  {
    $products = $this->repository->getAllProducts();

    return $products;
  }

  public function getProductById(int $id): ?Product
  {
    $product = $this->repository->getProductById($id);
    
    return $product;
  }

  public function createProduct(array $dto): Product
  {
    $product = new Product();
    $product->fill($dto);
    
    $product = $this->repository->save($product);

    return $product;
  }

  public function updateProduct(int $id, array $dto): Product
  {
    $product = new Product();
    $product->fill($dto);
    $product->setId($id);

    $product = $this->repository->update($product);
    return $product;
  }
}