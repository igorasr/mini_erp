<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller
{
  private ProductService $productService;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    $this->load->library('Response');
    $this->load->helper(['url']);

    $this->load->library('ProductService');
    $this->productService = new ProductService();
  }

  public function create()
  {
    if($this->input->post('id')){
      $productId = (int)$this->input->post('id');
      $product = $this->productService->getProductById($productId);

      if (!$product) {
        show_404();
      }
      
      $this->productService->updateProduct($productId, [
        'name' => $this->input->post('name'),
        'price' => (float)$this->input->post('price'),
        'stock' => (int)$this->input->post('stock', true) ?: 0,
        'variants' => $this->input->post('variacoes', true) ?: []
      ]);
      return;
    }

    $this->productService->createProduct([
      'name' => $this->input->post('name'),
      'price' => (float)$this->input->post('price'),
      'stock' => (int)$this->input->post('stock', true) ?: 0,
      'variants' => $this->input->post('variacoes', true) ?: []
    ]);

    Response::json(
      [
        'success' => true,
        'message' => 'Product created successfully.'
      ]
    );
  }
}