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
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Relação N:N entre variantes e valores
CREATE TABLE product_variant_values (
    variation_value_id INT NOT NULL,
    PRIMARY KEY (variation_value_id),
    FOREIGN KEY (variation_value_id) REFERENCES variation_values(id)
);

