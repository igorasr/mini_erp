<?php


class Order extends My_Model
{
    public float $total = 0.0;
    public float $discount = 0.0;
    public Coupon $coupon;
    public array $items = [];

    public function __construct()
    {
        parent::__construct();
        $this->table = 'orders';
    }

    public function calculateTotals(): void
    {
        $subtotal = array_reduce($this->items, fn($acc, $item) =>
            $acc + ($item['price'] * $item['quantity']), 0
        );

        switch (true) {
            case ($subtotal > 200):
            $shipping = 0;
            break;
            case ($subtotal >= 52 && $subtotal <= 166.59):
            $shipping = 15;
            break;
            default:
            $shipping = 20;
            break;
        }

        $this->total = $subtotal + $shipping;

        if (isset($this->coupon) && $this->coupon->isValid()) {
            $this->total -= $this->coupon->discount;
        }
    }

    public function addItem(Product $product, int $quantity): void
    {
        $this->items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'price' => $product->price
        ];
        $this->total += $product->price * $quantity;
    }

    public function applyCoupon(Coupon $coupon): void
    {
        if (!$coupon->active) {
            throw new InvalidArgumentException('Coupon is not active');
        }

        if ($this->total < $coupon->discount_value) {
            throw new InvalidArgumentException('Total is less than the coupon discount value');
        }

        $this->coupon = $coupon;
        $this->discount = $coupon->discount_value;
        $this->total -= $this->discount;
    }

    public function _to_db(): array
    {   
        $data = parent::_to_db();
        $data['coupon_id'] = isset($this->coupon) ? $this->coupon->id() : null;

        return $data;
    }
}