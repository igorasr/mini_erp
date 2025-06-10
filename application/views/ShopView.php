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
                            <button class="add-to-cart btn btn-sm btn-primary" data-id="<?= $product->id() ?>">Adicionar no Carrinho</button>
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
            <form id="produto-form" class="modal-content" method="post" action="/products/create">
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

                    <label>Variações</label>
                    <div id="variacoes-container"></div>
                    <button type="button" class="btn btn-sm btn-outline-primary mb-2" id="add-variacao">+ Variação</button>
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
                                    <div  class='btn-group' role='group' aria-label='Actions'>
                                        <button type='button' data-id='{$item['id']}' class='btn incrementToCart'>➕</button>
                                        <button type='button' data-id='{$item['id']}' class='btn removeToCart'>❌</button>
                                    </div>
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

            document.querySelectorAll('.add-to-cart').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    fetch(`/index.php/shop/addToCart/${productId}`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector('.btn.btn-primary').innerText = `Carrinho (${data.total})`;
                            } else {
                                alert('Erro ao adicionar no carrinho.');
                            }

                            window.location.reload();
                        });
                });
            });

            document.querySelectorAll('.incrementToCart').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    fetch(`/index.php/shop/addToCart/${productId}`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert('Produto adicionado ao carrinho!');
                                // Atualize contagem no botão
                                document.querySelector('.btn.btn-primary').innerText = `Carrinho (${data.total})`;
                            } else {
                                alert('Erro ao adicionar no carrinho.');
                            }
                            window.location.reload()
                        });
                });
            });
            document.querySelectorAll('.removeToCart').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    fetch(`/index.php/shop/removeCart/${productId}`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert('Produto removido do carrinho!');
                                // Atualize contagem no botão
                                document.querySelector('.btn.btn-primary').innerText = `Carrinho (${data.total})`;
                            } else {
                                alert('Erro ao remover do carrinho.');
                            }
                        });
                });
            });

            const form = document.getElementById('produto-form');

            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Impede o redirecionamento

                const formData = new FormData(form);
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Produto salvo com sucesso!');
                            form.reset();
                            window.location.reload()
                        } else {
                            alert('Erro ao salvar produto.');
                        }
                    })
                    .catch(() => alert('Erro na requisição.'));
            });

            const variacoesContainer = document.getElementById('variacoes-container');
            const addBtn = document.getElementById('add-variacao');

            addBtn.addEventListener('click', function() {
                const index = variacoesContainer.children.length;
                const row = document.createElement('div');
                row.className = 'd-flex mb-2 gap-2';

                row.innerHTML = `
                    <input type="text" name="variacoes[${index}][atributo]" class="form-control" placeholder="Atributo (ex: cor)" required>
                    <input type="text" name="variacoes[${index}][valor]" class="form-control" placeholder="Valor (ex: vermelho)" required>
                    <button type="button" class="btn btn-danger btn-sm remove-variacao">x</button>
                `;

                variacoesContainer.appendChild(row);
            });

            variacoesContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-variacao')) {
                    e.target.closest('.d-flex').remove();
                }
            });
        });
    </script>

</body>

</html>