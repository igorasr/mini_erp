<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends CI_Controller
{
  private ProductService $productService;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    $this->load->helper(['url']);

    $this->load->model('ShopCart');
    $this->load->library('ProductService');
    $this->productService = new ProductService();
  }

  public function index()
  {
    $products = $this->productService->getAllProducts();
    $this->load->helper(['form', 'url']);
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
    redirect('shop/');
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
    if(count($carrinho) <= 0){
      redirect('shop/');
    }
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

    redirect('shop/');
  }
}
