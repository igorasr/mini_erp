-- Produtos principais
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tipos de variação: ex: Cor, Tamanho
CREATE TABLE variation_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL -- ex: "Cor", "Tamanho"
);

-- Valores possíveis por tipo: ex: "Vermelho" para "Cor"
CREATE TABLE variation_values (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variation_type_id INT NOT NULL,
    value VARCHAR(100) NOT NULL, -- ex: "G", "Vermelho"
    FOREIGN KEY (variation_type_id) REFERENCES variation_types(id)
);

-- Combinações de variações de um produto (ex: Camiseta Vermelha G)
CREATE TABLE product_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    stock INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Relação N:N entre variantes e valores
CREATE TABLE product_variant_values (
    product_stock_id INT NOT NULL,
    variation_value_id INT NOT NULL,
    PRIMARY KEY (product_stock_id, variation_value_id),
    FOREIGN KEY (product_stock_id) REFERENCES product_stock(id),
    FOREIGN KEY (variation_value_id) REFERENCES variation_values(id)
);

-- Pedidos
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    coupon_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id)
);

-- Itens do pedido (com variação de produto)
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_stock_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL, -- preço unitário no momento do pedido
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_stock_id) REFERENCES product_stock(id)
);

-- Cupons de desconto
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    discount_value DECIMAL(10,2) NOT NULL,
    usage_limit INT DEFAULT NULL, -- null = ilimitado
    used_count INT DEFAULT 0,
    valid_from DATE,
    valid_until DATE,
    active BOOLEAN DEFAULT FALSE
);