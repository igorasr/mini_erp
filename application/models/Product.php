<?php

class Product extends My_Model
{
    protected string $table = 'products';

    public string $name = '';
    public float $price = 0.0;
    public array $variants = [];
    public int $stock = 0;

    public function __construct()
    {
        parent::__construct();
        $this->tabel = 'products';
    }
}
