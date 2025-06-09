<?php

class OrderService
{
  private OrderRepository $orderRepository;
  private ProductRepository $productRepository;
  private CouponRepository $couponRepository;

  public function __construct()
  {
    $this->orderRepository = new OrderRepository();
    $this->productRepository = new ProductRepository();
    $this->couponRepository = new CouponRepository();
  }

  public function saveOrder(ShopCart $chart, string $couponCode): Order
  {
    $order = new Order();
    $productService = new ProductService();

    // Valida estoque
    foreach ($chart as $item) {
      $product = $productService->getProductById($item['id']);

      if (!$this->productRepository->checkStock($product , $item['quantity'])) {
          throw new DomainException("Sem estoque para o produto: " . $item['product']->name);
      }

      if ($product) {
        $order->addItem($product, $item['quantity']);
      }      
    }

    if (!empty($couponCode)) {
      $couponService = new CouponService();
      $coupon = $couponService->getCouponByCode($couponCode);
      if ($coupon) {
        $order->applyCoupon($coupon);
        $this->couponRepository->markAsUsed($order->coupon);
      }
    }

    $order->calculateTotals();
    $savedOrder = $this->orderRepository->save($order);
    
    // Dá baixa no estoque
    foreach ($order->items as $item) {
        $this->productRepository->decreaseStock($item['product'], $item['quantity']);
    }

    return $savedOrder;
  }
} 