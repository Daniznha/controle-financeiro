<?php
include 'conectarbanco.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("ID do usuário não está definido na sessão.");
}

$id = $_SESSION['id_usuario'];
$conexao = new conectarbanco();
$conn = $conexao->conectar();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $valor = str_replace(',', '.', $_POST['valor']);
    
    // Verifica se já existe uma renda para este usuário
    $sql_verificar_renda = "SELECT id_renda FROM renda WHERE id_usuario = $id";
    $result_verificar_renda = $conn->query($sql_verificar_renda);
    
    if ($result_verificar_renda->num_rows > 0) {
        // Atualiza a renda existente
        $sql_atualizar_renda = "UPDATE renda SET valor = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql_atualizar_renda);
        $stmt->bind_param("si", $valor, $id);
    } else {
        // Insere uma nova renda
        $sql_inserir_renda = "INSERT INTO renda (id_usuario, valor) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_inserir_renda);
        $stmt->bind_param("is", $id, $valor);
    }
    
    if ($stmt->execute()) {
        $message = "Renda adicionada/atualizada com sucesso!";
    } else {
        $message = "Erro ao adicionar/atualizar renda: " . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Renda</title>
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
    <h3>Adicionar Renda</h3>
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="novaRenda.php" method="post">
        <div class="mb-3">
            <input type="text" class="form-control" id="valor" name="valor" required>
        </div>
        <button type="submit" class="btn btn-primary">Adicionar/Atualizar Renda</button>
    </form>
</div>
<script src="../js/bootstrap.min.js"></script>
</body>
</html>
