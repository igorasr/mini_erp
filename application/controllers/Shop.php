<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shop extends CI_Controller
{
  private ProductService $productService;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    $this->load->library('ProductService');
    $this->load->library('Response');
    $this->load->model('ShopCart');

    $this->productService = new ProductService();
  }

  public function index()
  {
    $products = $this->productService->getAllProducts();

    if (!$this->session->has_userdata('carrinho')) {
      $this->session->set_userdata('carrinho', serialize(new ShopCart()));
    }

    $this->load->helper(['form']);
    $this->load->view('ShopView', ['products' => $products]);
  }

  public function addToCart(int $productId)
  {
    $product = $this->productService->getProductById($productId);
    $cartData = $this->session->userdata('carrinho');
    $carrinho = null;

    if (!empty($cartData)) {
      $carrinho = @unserialize($cartData);
    }
    if (!$carrinho instanceof ShopCart) {
      $carrinho = new ShopCart();
    }

    $carrinho->addItem($product, 1);

    $this->session->set_userdata('carrinho', serialize($carrinho));

    Response::json(
      [
        'success' => true,
        'total' => count($carrinho)
      ]
    );
  }

  public function removeCart(int $productId)
  {
    $cartData = $this->session->userdata('carrinho');
    $carrinho = null;

    if (!empty($cartData)) {
      $carrinho = @unserialize($cartData);
    }
    if (!$carrinho instanceof ShopCart) {
      $carrinho = new ShopCart();
    }

    $carrinho->removeItem($productId);

    $this->session->set_userdata('carrinho', serialize($carrinho));
    Response::json(
      [
        'success' => true,
        'total' => count($carrinho)
      ]
    );
  }


  public function checkout()
  {
    $this->load->library('OrderService');
    $this->load->model('Order');

    $orderService = new OrderService();

    $cartData = $this->session->userdata('carrinho');
    $carrinho = null;

    if (!empty($cartData)) {
      $carrinho = @unserialize($cartData);
    }
    if (!$carrinho instanceof ShopCart) {
      $carrinho = new ShopCart();
    }

    if (count($carrinho) <= 0) {
      redirect('shop/');
    }

    // Aqui você pode implementar a lógica de checkout, como processar o pagamento, etc.
    $couponCode = $this->input->post('coupon_code') ?? '';
    $order = $orderService->saveOrder($carrinho, $couponCode);

    // Limpa o carrinho após o checkout
    $carrinho->clear();
    $this->session->set_userdata('carrinho', serialize($carrinho));

    Response::json(
      [
        'success' => true,
        'total' => count($carrinho)
      ]
    );
  }
}
