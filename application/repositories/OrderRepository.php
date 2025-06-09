<?php

class OrderRepository extends My_Repository
{
  public function __construct()
  {
    parent::__construct();
    $this->CI->load->model('Order');
  }

  public function hydrate(array $data): Order
  {
    $order = new Order();
    $order->fill($data);
    return $order;
  }

  public function save(Order $order): Order
  {
    $data = $order->_to_db();

    $this->db->trans_start();

    $this->db->insert('orders', $data);
    $order->setId($this->db->insert_id());

    $items = [];
    foreach ($order->items as $item) {
      $product_stock = $this->db->get_where('product_stock', ['product_id' => $item['product']->id()])->row_array();

      $items[] = [
        'order_id' => $order->id(),
        'product_stock_id' => $product_stock['id'],
        'quantity' => $item['quantity'],
        'price' => $item['price']
      ];

    }

    if (!empty($items)) {
      $this->db->insert_batch('order_items', $items);
    }

    $this->db->trans_complete();

    return $order;
  }

  public function getOrderById(int $id): ?Order
  {
    $query = $this->db->get_where('orders', ['id' => $id])->row_array();

    if (empty($query)) {
      return null;
    }

    $order = $this->hydrate($query);
    $order->items = $this->getOrderItems($id);
    return $order;
  }

  public function getOrderItems(int $orderId): array
  {
    $this->CI->load->model('OrderItem');

    $items = $this->db->get_where('order_items', ['order_id' => $orderId])->result_array();

    return $items;
  }
}
