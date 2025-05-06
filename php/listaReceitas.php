<?php
// Inclui o arquivo de conexão com o banco de dados
include 'conectarbanco.php';
session_start();
$id = $_SESSION['id_usuario'];
$conexao = new conectarbanco();
$conn = $conexao->conectar();

// Função para formatar a data no formato dd/mm/aaaa
function formatarData($data)
{
    return date('d/m/Y', strtotime($data));
}

// Verifica se o ID do gasto a ser excluído foi recebido
if (isset($_GET['excluir_gasto'])) {
    $idGasto = $_GET['excluir_gasto'];

    // Query SQL para excluir o gasto com o ID fornecido
    $sqlExcluirGasto = "DELETE FROM gastos WHERE id_gastos = $idGasto AND id_usuario = $id";

    if ($conn->query($sqlExcluirGasto) === TRUE) {
        // Se a exclusão for bem-sucedida, redirecione de volta para esta página
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } else {
        echo "Erro ao excluir gasto: " . $conn->error;
    }
}

// Verifica se o ID da receita a ser excluída foi recebido
if (isset($_GET['excluir_receita'])) {
    $idReceita = $_GET['excluir_receita'];

    // Query SQL para excluir a receita com o ID fornecido
    $sqlExcluirReceita = "DELETE FROM receitas WHERE id_receita = $idReceita AND id_usuario = $id";

    if ($conn->query($sqlExcluirReceita) === TRUE) {
        // Se a exclusão for bem-sucedida, redirecione de volta para esta página
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } else {
        echo "Erro ao excluir receita: " . $conn->error;
    }
}

// Consulta para obter os gastos do usuário
$sqlGastos = "SELECT id_gastos, nome, valor, categoria, DATE_FORMAT(dt_gasto, '%d/%m/%Y') AS dt_gasto FROM gastos WHERE id_usuario = $id";
$resultGastos = $conn->query($sqlGastos);

// Consulta para obter as receitas do usuário
$sqlReceitas = "SELECT id_receita, nome, valor, DATE_FORMAT(dt_receita, '%d/%m/%Y') AS dt_receita, observacoes FROM receitas WHERE id_usuario = $id";
$resultReceitas = $conn->query($sqlReceitas);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Receitas</title>
    <link rel="shortcut icon" type="imagex/png" href="../imagens/dinheiro.ico">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login-cadastro.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        .navbar-brand img {
            margin-right: 5px;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
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
        <h3 class="mb-3">Listagem de Receitas</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Valor</th>
                    <th>Data</th>
                    <th>Observações</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultReceitas->num_rows > 0) {
                    while ($row = $resultReceitas->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>";
                        echo "<td>" . $row['dt_receita'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['observacoes'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td><a href='editar_receita.php?id=" . $row['id_receita'] . "' class='btn btn-sm btn-primary'>Editar</a> | ";
                        echo "<a href='{$_SERVER['PHP_SELF']}?excluir_receita=" . $row['id_receita'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir esta receita?\")'>Excluir</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nenhuma receita encontrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="../js/bootstrap.min.js"></script>
</body>

</html>



