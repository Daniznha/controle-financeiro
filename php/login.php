<?php
include 'conectarbanco.php';
$conexao = new conectarbanco();
$conn = $conexao->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Consulta para verificar se o usuário existe
    $stmt = $conn->prepare("SELECT id_usuario, nome, email, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Usuário encontrado, verificar senha
        $stmt->bind_result($id_usuario, $nome, $db_email, $db_senha);
        $stmt->fetch();

        if (password_verify($senha, $db_senha)) {
            // Senha correta, iniciar a sessão
            session_start();
            $_SESSION['id_usuario'] = $id_usuario;
            $_SESSION['nome'] = $nome;
            $_SESSION['email'] = $db_email;

            // Redirecionar para a página de perfil (ou outra página desejada)
            header("Location: index.php");
            exit();
        } else {
            $mensagem = "Senha incorreta";
        }
    } else {
        $mensagem = "Usuário não encontrado";
    }

    $stmt->close();
}

// Fechar a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Bolso Amigo</title>
    <link rel="shortcut icon" type="imagex/png" href="../imagens/dinheiro.ico">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css'>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/login-cadastro.css">
</head>

<body id="body">
    <div class="container">
        <div class="heading">Login Bolso Amigo</div><br>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input required="" class="input" type="email" name="email" id="email" placeholder="E-mail"><br><br>
            <input required="" class="input" type="password" name="senha" id="password" placeholder="Senha"><br><br>
            <input class="login-button" type="submit" value="Entrar">

        </form><br>

        <div class="mensagem">
            <?php if (isset($mensagem)) : ?>
                <p><?php echo $mensagem; ?></p>
            <?php endif; ?>
        </div>
        <p class="cad">Ainda nao possui um login? cadastre-se <a href="./cadastro.php" target="_blank">aqui</a></p>
    </div>
    <script src="../js/bootstrap.min.js"></script>

</body>

</html>