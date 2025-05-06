<?php
// Inclui o arquivo de conexão com o banco de dados
include 'conectarbanco.php';
session_start();
$id = $_SESSION['id_usuario'];
$conexao = new conectarbanco();
$conn = $conexao->conectar();

$mensagem_gasto = '';
$mensagem_receita = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se é uma inserção de gasto
    if (isset($_POST['tipo']) && $_POST['tipo'] == 'gasto') {
        // Coleta os dados do formulário
        $nome = $_POST['nome'];
        $categoria = $_POST['categoria'];
        $valor = str_replace(',', '.', $_POST['valor']); // Substitui a vírgula por ponto
        $data_gasto = $_POST['data_gasto'];

        // Insere os dados na tabela de gastos
        $sql_insert_gasto = "INSERT INTO gastos (id_usuario, nome, categoria, valor, dt_gasto) VALUES ($id, '$nome', '$categoria', '$valor', '$data_gasto')";
        if ($conn->query($sql_insert_gasto) === TRUE) {
            $mensagem_gasto = "Gasto inserido com sucesso.";
        } else {
            $mensagem_gasto = "Erro ao inserir gasto: " . $conn->error;
        }
    }
    // Verifica se é uma inserção de receita
    elseif (isset($_POST['tipo']) && $_POST['tipo'] == 'receita') {
        // Coleta os dados do formulário
        $nome = $_POST['nome'];
        $valor = str_replace(',', '.', $_POST['valor']); // Substitui a vírgula por ponto
        $data_receita = $_POST['data_receita'];
        $observacoes = $_POST['observacoes'];

        // Insere os dados na tabela de receitas
        $sql_insert_receita = "INSERT INTO receitas (id_usuario, nome, valor, dt_receita, observacoes) VALUES ($id, '$nome', '$valor', '$data_receita', '$observacoes')";
        if ($conn->query($sql_insert_receita) === TRUE) {
            $mensagem_receita = "Receita inserida com sucesso.";
        } else {
            $mensagem_receita = "Erro ao inserir receita: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserir Informações</title>
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
    

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h3>Inserir Gasto</h3>
                
                    <div class="alert alert-info"><?php echo $mensagem_gasto; ?></div>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="tipo" value="gasto">
                    <div class="mb-3">
                        <label for="nome_gasto" class="form-label">Nome do Gasto:</label>
                        <input type="text" id="nome_gasto" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria_gasto" class="form-label">Categoria do Gasto:</label>
                        <select id="categoria_gasto" name="categoria" class="form-select" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="alimentação">Alimentação</option>
                            <option value="transporte">Transporte</option>
                            <option value="saúde">Saúde</option>
                            <option value="educação">Educação</option>
                            <option value="lazer">Lazer</option>
                            <option value="vestuário">Vestuário</option>
                            <option value="dívidas">Dívidas</option>
                            <option value="moradia">Moradia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="valor_gasto" class="form-label">Valor do Gasto:</label>
                        <input type="text" id="valor_gasto" name="valor" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_gasto" class="form-label">Data do Gasto:</label>
                        <input type="date" id="data_gasto" name="data_gasto" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Inserir Gasto</button>
                </form>
            </div>
            <div class="col-md-6">
                <h3>Inserir Receita</h3>
                 
                <div class="alert alert-info"><?php echo $mensagem_receita; ?></div>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="tipo" value="receita">
                    <div class="mb-3">
                        <label for="nome_receita" class="form-label">Nome da Receita:</label>
                        <input type="text" id="nome_receita" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="valor_receita" class="form-label">Valor da Receita:</label>
                        <input type="text" id="valor_receita" name="valor" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_receita" class="form-label">Data da Receita:</label>
                        <input type="date" id="data_receita" name="data_receita" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações:</label>
                        <textarea id="observacoes" name="observacoes" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Inserir Receita</button>
                </form>
            </div>
        </div>
    </div>
    <script src="../js/bootstrap.min.js"></script>
</body>

</html>