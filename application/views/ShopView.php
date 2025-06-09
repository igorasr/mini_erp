<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Loja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar bg-light p-3">
        <div class="container-fluid">
            <span class="navbar-brand">Minha Loja</span>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#carrinhoModal">
                Carrinho (<?= count(unserialize($this->session->userdata('carrinho')) ?? []) ?>)
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#produtoModal">+ Produto</button>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5><?= $product->name ?></h5>
                            <p>R$ <?= number_format($product->price, 2, ',', '.') ?></p>
                            <a href="/index.php/shop/addToCart/<?= $product->id() ?>" class="btn btn-sm btn-primary">Adicionar no Carrinho</a>
                            <button
                                class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#produtoModal"
                                data-id="<?= $product->id() ?>"
                                data-name="<?= htmlspecialchars($product->name) ?>"
                                data-price="<?= $product->price ?>"
                                data-stock="<?= $product->stock ?>">
                                Editar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <!-- Modal de Produto -->
    <div class="modal fade" id="produtoModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="/products/create">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-produto-id" name="id">
                    <label for="name">Nome</label>
                    <input id="produto-name" name="name" class="form-control mb-2" placeholder="Nome" required>
                    <label for="price">Preco (R$)</label>
                    <input id="produto-price" name="price" class="form-control mb-2" placeholder="Preço" type="number" step="0.01" required>
                    <label for="price">Estoque</label>
                    <input id="produto-stock" name="stock" class="form-control mb-2" placeholder="Estoque" type="number" step="1" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Carrinho -->
    <div class="modal fade" id="carrinhoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Carrinho</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php
                    $carrinho = unserialize($this->session->userdata('carrinho')) ?? [];

                    if (count($carrinho)) {
                        echo '<table class="table table-striped">';
                        echo '<thead><tr><th>Produto</th><th>Qtd</th><th>Preço</th><th>Total</th></tr></thead>';
                        echo '<tbody>';
                        foreach ($carrinho as $item) {
                            $totalItem = $item['price'] * $item['quantity'];
                            echo "<tr>
                                <td>{$item['name']}</td>
                                <td>{$item['quantity']}</td>
                                <td>R$ " . number_format($item['price'], 2, ',', '.') . "</td>
                                <td>R$ " . number_format($totalItem, 2, ',', '.') . "</td>
                                <td>
                                    <span>➖</span>
                                    <a href='/index.php/shop/addToCart/{$item['id']}'>➕</a>
                                    <a href='/index.php/shop/removeCart/{$item['id']}'>❌</a>
                                </td>
                              </tr>";
                        }
                        echo '</tbody></table>';

                        $subtotal = $carrinho->subtotal;
                        $frete = ($subtotal > 200) ? 0 : (($subtotal >= 52 && $subtotal <= 166.59) ? 15 : 20);
                        $total = $subtotal + $frete;

                        echo "<p><strong>Subtotal:</strong> R$ " . number_format($subtotal, 2, ',', '.') . "</p>";
                        echo "<p><strong>Frete:</strong> R$ " . number_format($frete, 2, ',', '.') . "</p>";
                        echo "<p><strong>Total:</strong> R$ " . number_format($total, 2, ',', '.') . "</p>";
                    } else {
                        echo "<p>Carrinho vazio</p>";
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <?php if (count($carrinho) > 0): ?>
                        <a href="/index.php/shop/checkout" class="btn btn-success">Finalizar Compra</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('produtoModal');
            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('edit-produto-id').value = button.getAttribute('data-id');
                document.getElementById('produto-name').value = button.getAttribute('data-name');
                document.getElementById('produto-price').value = button.getAttribute('data-price');
                document.getElementById('produto-stock').value = button.getAttribute('data-stock');
            });
        });
    </script>

</body>

</html>