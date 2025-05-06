<?php
// Inclui o arquivo de conexão com o banco de dados
include 'conectarbanco.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("ID do usuário não está definido na sessão.");
}

$id = $_SESSION['id_usuario'];
$conexao = new conectarbanco();
$conn = $conexao->conectar();

$mensagem = '';

// Verifica se a requisição é do tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se a chave "receitas" existe e não está vazia
    if (isset($_POST['receitas']) && is_array($_POST['receitas'])) {
        // Itera sobre os dados enviados para atualizar as receitas
        foreach ($_POST['receitas'] as $receita) {
            $id_receita = $receita['id'];
            $nome_receita = $receita['nome'];
            $valor_receita = str_replace(',', '.', $receita['valor']); // Substitui a vírgula por ponto
            $data_receita = $receita['data_receita'];
            $observacoes = $receita['observacoes'];

            // Atualiza os dados na tabela de receitas
            $sql_update_receita = "UPDATE receitas SET nome='$nome_receita', valor='$valor_receita', dt_receita='$data_receita', observacoes='$observacoes' WHERE id_receita=$id_receita";
            if ($conn->query($sql_update_receita) === TRUE) {
                $mensagem = "Receitas atualizadas com sucesso.";
            } else {
                $mensagem = "Erro ao atualizar receitas: " . $conn->error;
            }
        }
    }
}

// Consulta para obter as receitas do usuário
$sql_receitas = "SELECT id_receita, nome, valor, DATE_FORMAT(dt_receita, '%Y-%m-%d') AS dt_receita, observacoes FROM receitas WHERE id_usuario = $id";
$result_receitas = $conn->query($sql_receitas);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Receitas</title>
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
    <div class="justify-content-between align-items-center mb-3">
        <h3>Receitas</h3>
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
                <th>Valor</th>
                <th>Observações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_receitas->num_rows > 0) {
                while ($row = $result_receitas->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><input type='date' name='receitas[{$row['id_receita']}][data_receita]' value='" . $row['dt_receita'] . "'></td>";
                    echo "<input type='hidden' name='receitas[{$row['id_receita']}][id]' value='" . $row['id_receita'] . "'>";
                    echo "<td><input type='text' name='receitas[{$row['id_receita']}][nome]' value='" . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . "'></td>";
                    echo "<td><input type='text' name='receitas[{$row['id_receita']}][valor]' value='" . htmlspecialchars($row['valor'], ENT_QUOTES, 'UTF-8') . "'></td>";
                    echo "<td><input type='text' name='receitas[{$row['id_receita']}][observacoes]' value='" . htmlspecialchars($row['observacoes'], ENT_QUOTES, 'UTF-8') . "'></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Nenhuma receita encontrada.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    </form>
</div>
<script src="../js/bootstrap.min.js"></script>
</body>
</html>
