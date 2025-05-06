<?php
include 'conectarbanco.php';
$conexao = new conectarbanco();
$conn = $conexao->conectar();

// Excluir usuário se o parâmetro 'delete_id' estiver definido na URL
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Delete from all tables that have a foreign key reference to 'usuarios'
    $sql_delete = "DELETE FROM gastos WHERE id_usuario = $delete_id";
    $conn->query($sql_delete);
    
    $sql_delete = "DELETE FROM receitas WHERE id_usuario = $delete_id";
    $conn->query($sql_delete);
    
    $sql_delete = "DELETE FROM limite_gastos WHERE id_usuario = $delete_id";
    $conn->query($sql_delete);
    
    $sql_delete = "DELETE FROM renda WHERE id_usuario = $delete_id";
    $conn->query($sql_delete);
    
    $sql_delete = "DELETE FROM usuarios WHERE id_usuario = $delete_id";
    $conn->query($sql_delete);

    ("Location: listaUsuarios.php"); // Redirecionar após excluir
    exit();
}

$sql = "SELECT * FROM usuarios";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuários</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css'>
    <link rel="shortcut icon" type="imagex/png" href="../imagens/ginastica.ico">                      
</head>

<body>

    <h1>Lista de Usuários</h1>

    <?php
    if ($resultado->num_rows > 0) {
        echo "<table class='table'>";
        echo "<thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Sexo</th><th>Ações</th></tr></thead><tbody>";

        while ($row = $resultado->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id_usuario']}</td>";
            echo "<td>{$row['nome']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['sexo']}</td>";
            echo "<td><a href='listaUsuarios.php?delete_id={$row['id_usuario']}' class='btn btn-danger'>Excluir</a></td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "Nenhum usuário encontrado.";
    }

    $conn->close();
    ?>

</body>

</html>
