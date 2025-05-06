<?php
// Inclui o arquivo de conexão com o banco de dados
include 'conectarbanco.php';
session_start();
$id = $_SESSION['id_usuario'];
$conexao = new conectarbanco();
$conn = $conexao->conectar();

// Consulta para obter as opções de categoria
$categorias = array('alimentação', 'transporte', 'saúde', 'educação', 'lazer', 'vestuário', 'dívidas', 'moradia');

// Verifica se a requisição é do tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se a chave "gastos" existe e não está vazia
    if (isset($_POST['gastos']) && is_array($_POST['gastos'])) {
        // Itera sobre os dados enviados para atualizar os gastos
        foreach ($_POST['gastos'] as $gasto) {
            $id_gasto = $gasto['id'];
            $nome_gasto = $gasto['nome'];
            $categoria_gasto = $gasto['categoria'];
            $valor_gasto = str_replace(',', '.', $gasto['valor']); // Substitui a vírgula por ponto
            $data_gasto = $gasto['data_gasto'];

            // Atualiza os dados na tabela de gastos
            $sql_update_gasto = "UPDATE gastos SET nome='$nome_gasto', categoria='$categoria_gasto', valor='$valor_gasto', dt_gasto='$data_gasto' WHERE id_gastos=$id_gasto";
            if ($conn->query($sql_update_gasto) === TRUE) {
                $mensagem = "Gastos atualizados com sucesso.";
            } else {
                $mensagem = "Erro ao atualizar gastos: " . $conn->error;
            }
        }
    }
}

// Consulta para obter os gastos do usuário
$sql_gastos = "SELECT id_gastos, nome, valor, categoria, DATE_FORMAT(dt_gasto, '%Y-%m-%d') AS dt_gasto FROM gastos WHERE id_usuario = $id";
$result_gastos = $conn->query($sql_gastos);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Gastos</title>
    <link rel="shortcut icon" type="imagex/png" href="../imagens/dinheiro.ico">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login-cadastro.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>
<nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="../php/index.php">Bolso<img src="../imagens/carteira.png" alt="" width="30px">Amigo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link" href="../php/Inserir.php">Inserir</a>
                    <a class="nav-link" href="../php/listagem.php">Lista de gastos</a>
                    <a class="nav-link" href="../php/listaReceitas.php">Lista de receitas</a>
                    <a class="nav-link" href="../php/aprender.php">Educação financeira</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class=" justify-content-between align-items-center mb-3">
            <h3>Gastos</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="submit" value="Salvar" class="botao">
        </div>

        <?php if (!empty($mensagem)) : ?>
        <div class="d-flex justify-content-center">
            <div class="alert alert-info text-center w-50">
                <?php echo $mensagem; ?>
            </div>
        </div>
    <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_gastos->num_rows > 0) {
                    while ($row = $result_gastos->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><input type='date' name='gastos[{$row['id_gastos']}][data_gasto]' value='" . $row['dt_gasto'] . "'></td>";
                        echo "<input type='hidden' name='gastos[{$row['id_gastos']}][id]' value='" . $row['id_gastos'] . "'>";
                        echo "<td><input type='text' name='gastos[{$row['id_gastos']}][nome]' value='" . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . "'></td>";
                        echo "<td><select name='gastos[{$row['id_gastos']}][categoria]'>";
                        foreach ($categorias as $categoria) {
                            $selected = ($categoria == $row['categoria']) ? "selected" : "";
                            echo "<option value='$categoria' $selected>$categoria</option>";
                        }
                        echo "</select></td>";
                        echo "<td><input type='text' name='gastos[{$row['id_gastos']}][valor]' value='" . htmlspecialchars($row['valor'], ENT_QUOTES, 'UTF-8') . "'></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Nenhum gasto encontrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        </form>
    </div>

    <script src="../js/bootstrap.min.js"></script>
</body>

</html>
