<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ProductRepository extends My_Repository
{

    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('Product');
    }

    public function hydrate(array $data): Product
    {
        $product = new Product();
        $product->fill($data);
        return $product;
    }

    /**
     * Recupera todos os produtos do banco de dados, incluindo o estoque de cada produto.
     *
     * @return array<Product> Lista de produtos com informações de estoque.
     */
    public function getAllProducts(): array
    {
        $products = [];
        $query = $this->db->get('products')->result_array();

        foreach ($query as &$product) {
            $product['stock'] = $this->getStock($product['id']);
        }

        $products = array_map([$this, 'hydrate'], $query);

        return $products;
    }

    /**
     * Recupera um produto específico pelo ID.
     *
     * @param int $id ID do produto a ser recuperado.
     * @return Product|null Retorna o produto se encontrado, ou null se não existir.
     */
    public function getProductById(int $id): ?Product
    {
        $query = $this->db->get_where('products', ['id' => $id])->row_array();

        if (empty($query)) {
            return null;
        }

        $query['stock'] = $this->getStock($id);
        return $this->hydrate($query);
    }


    /**
     * Obtém a quantidade em estoque de um produto específico.
     *
     * @param int $product_id O ID do produto para consulta do estoque.
     * @return int Retorna a quantidade em estoque do produto. Caso não exista, retorna 0.
     */
    public function getStock($product_id): int
    {
        $query = $this->db->select('stock')
            ->from('product_stock')
            ->where('product_id', $product_id)
            ->get()
            ->row_array();

        return $query['stock'] ?? 0;
    }

    public function checkStock(Product $product, int $quantity): bool
    {
        $stock = $this->db->get_where('product_stock', ['product_id' => $product->id()])->row_array();
        return $stock && $stock['stock'] >= $quantity;
    }

    public function decreaseStock(Product $product, int $quantity): void
    {
        $stock = $this->db->get_where('product_stock', ['product_id' => $product->id()])->row_array();

        if (!$stock || $stock['stock'] < $quantity) {
            throw new DomainException("Estoque insuficiente do produto {$product->name}");
        }

        $newStock = $stock['stock'] - $quantity;
        $this->db->update('product_stock', ['stock' => $newStock], ['product_id' => $product->id()]);
    }

    public function save(Product $product): Product
    {
        $data = $product->_to_db();

        $this->db->trans_start();

        $this->db->insert('products', $data);

        if ($this->db->affected_rows() === 0) {
            throw new \RuntimeException('Failed to insert product');
        }

        $product->setId($this->db->insert_id());

        if ($product->stock > 0) {
            $this->db->insert('product_stock', [
                'product_id' => $product->id(),
                'stock' => $product->stock
            ]);
        }

        $this->db->trans_complete();

        return $product;
    }

    public function update(Product $product)
    {
        if (!$product->id()) {
            throw new \InvalidArgumentException('Product ID must be set before updating');
        }

        $this->db->trans_start();

        if ($this->db->update('products', $product->_to_db(), ['id' => $product->id()])) {
            $this->db->where('product_id', $product->id());
            $this->db->update('product_stock', ['stock' => $product->stock]);
        }

        $this->db->trans_complete();

        return $product;
    }
}
