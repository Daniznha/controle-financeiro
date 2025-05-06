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

// Inicializa os filtros
$filtroCategoria = isset($_GET['filtro_categoria']) ? $_GET['filtro_categoria'] : '';
$filtroDataInicio = isset($_GET['filtro_data_inicio']) ? $_GET['filtro_data_inicio'] : '';
$filtroDataFim = isset($_GET['filtro_data_fim']) ? $_GET['filtro_data_fim'] : '';

// Consulta para obter os gastos do usuário com filtros
$sqlGastos = "SELECT id_gastos, nome, valor, categoria, DATE_FORMAT(dt_gasto, '%d/%m/%Y') AS dt_gasto 
              FROM gastos WHERE id_usuario = $id";
if ($filtroCategoria) {
    $sqlGastos .= " AND categoria = '$filtroCategoria'";
}
if ($filtroDataInicio && $filtroDataFim) {
    $sqlGastos .= " AND dt_gasto BETWEEN '$filtroDataInicio' AND '$filtroDataFim'";
}
$resultGastos = $conn->query($sqlGastos);

// Consulta para obter as receitas do usuário com filtros
$sqlReceitas = "SELECT id_receita, nome, valor, DATE_FORMAT(dt_receita, '%d/%m/%Y') AS dt_receita, observacoes 
                FROM receitas WHERE id_usuario = $id";
if ($filtroDataInicio && $filtroDataFim) {
    $sqlReceitas .= " AND dt_receita BETWEEN '$filtroDataInicio' AND '$filtroDataFim'";
}
$resultReceitas = $conn->query($sqlReceitas);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Gastos</title>
    <link rel="shortcut icon" type="imagex/png" href="../imagens/dinheiro.ico">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login-cadastro.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
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
        <h3>Listagem de gastos</h3>

        <!-- Formulário de Filtro -->
        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="filtro_categoria" class="form-label">Categoria</label>
                <select id="filtro_categoria" name="filtro_categoria" class="form-select">
                    <option value="">Todas</option>
                    <option value="Alimentação" <?php if ($filtroCategoria == 'Alimentação') echo 'selected'; ?>>Alimentação</option>
                    <option value="Transporte" <?php if ($filtroCategoria == 'Transporte') echo 'selected'; ?>>Transporte</option>
                    <option value="Saúde" <?php if ($filtroCategoria == 'Saúde') echo 'selected'; ?>>Saúde</option>
                    <option value="Educação" <?php if ($filtroCategoria == 'Educação') echo 'selected'; ?>>Educação</option>
                    <option value="Lazer" <?php if ($filtroCategoria == 'Lazer') echo 'selected'; ?>>Lazer</option>
                    <option value="Vestuário" <?php if ($filtroCategoria == 'Vestuário') echo 'selected'; ?>>Vestuário</option>
                    <option value="Dívidas" <?php if ($filtroCategoria == 'Dívidas') echo 'selected'; ?>>Dívidas</option>
                    <option value="Moradia" <?php if ($filtroCategoria == 'Moradia') echo 'selected'; ?>>Moradia</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtro_data_inicio" class="form-label">Data Início</label>
                <input type="date" id="filtro_data_inicio" name="filtro_data_inicio" class="form-control" value="<?php echo $filtroDataInicio; ?>">
            </div>
            <div class="col-md-3">
                <label for="filtro_data_fim" class="form-label">Data Fim</label>
                <input type="date" id="filtro_data_fim" name="filtro_data_fim" class="form-control" value="<?php echo $filtroDataFim; ?>">
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>

        <h3>Gastos</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Categoria</th>
                        <th scope="col">Data</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultGastos->num_rows > 0) {
                        while ($row = $resultGastos->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['nome'] . "</td>";
                            echo "<td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>";
                            echo "<td>" . $row['categoria'] . "</td>";
                            echo "<td>" . $row['dt_gasto'] . "</td>";
                            echo "<td><a href='editar_gastos.php?id=" . $row['id_gastos'] . "'class='btn btn-sm btn-primary'>Editar</a> | <button onclick='confirmarExclusao(" . $row['id_gastos'] . ")' class='btn btn-sm btn-danger'>Excluir</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Nenhum gasto encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../js/bootstrap.min.js"></script>
    <script>
        function confirmarExclusao(id) {
            if (confirm("Tem certeza que deseja excluir este gasto?")) {
                window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?excluir_gasto=' + id;
            }
        }
    </script>
</body>

</html>
