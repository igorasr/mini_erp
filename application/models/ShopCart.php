<?php


class ShopCart implements \Iterator, \Serializable, \Countable
{
    private array $items = [];
    public float $subtotal = 0.0;
    public string $couponCode = '';

    public function addItem(Product $product, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero');
        }

        if (!isset($this->items[$product->id()])) {
            $this->items[$product->id()] = [
                'id' => $product->id(),
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 0
            ];
        }
        $this->subtotal += $product->price * $quantity;

        $this->items[$product->id()]['quantity'] += $quantity;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function current(): array
    {
        return current($this->items);
    }

    public function key(): mixed
    {
        return key($this->items);
    }
    public function next(): void
    {
        next($this->items);
    }

    public function rewind(): void
    {
        reset($this->items);
    }
    public function valid(): bool
    {
        return key($this->items) !== null;
    }
    
    public function removeItem(string $productId): void
    {
        if (!isset($this->items[$productId])) {
            throw new InvalidArgumentException('Product not found in cart');
        }
        $item = $this->items[$productId];

        $this->subtotal -= $item['price'] * $item['quantity'];

        if ($this->subtotal < 0) {
            $this->subtotal = 0.0; // Ensure subtotal does not go negative
        }
        unset($this->items[$productId]);
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function clear(): void
    {
        $this->items = [];
        $this->subtotal = 0.0;
    }

    public function serialize()
    {
        return serialize([
            'items' => $this->items,
            'subtotal' => $this->subtotal
        ]);
    }

    public function unserialize($data)
    {
        $unserialized = unserialize($data);
        $this->items = $unserialized['items'];
        $this->subtotal = $unserialized['subtotal'];
    }
}