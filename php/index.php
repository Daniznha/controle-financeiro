<?php
// Inclui o arquivo de conexão com o banco de dados
include 'conectarbanco.php';
session_start();

// Verifica se o ID do usuário está definido na sessão
if (!isset($_SESSION['id_usuario'])) {
    die("ID do usuário não está definido na sessão.");
}

$id = $_SESSION['id_usuario'];
$conexao = new conectarbanco();
$conn = $conexao->conectar();

// Consulta para recuperar a renda do usuário
$sql_renda_usuario = "SELECT valor FROM renda WHERE id_usuario = $id";
$result_renda_usuario = $conn->query($sql_renda_usuario);

if ($result_renda_usuario === false) {
    die("Erro na consulta SQL: " . $conn->error);
}

$row_renda_usuario = $result_renda_usuario->fetch_assoc();

if ($row_renda_usuario !== null && isset($row_renda_usuario['valor'])) {
    $renda_usuario = $row_renda_usuario['valor'];
} else {
    $renda_usuario = 0.00; // Defina um valor padrão se a renda não estiver disponível
}

// Consulta para calcular o total de receitas do usuário
$sql_total_receitas = "SELECT SUM(valor) AS total_receitas FROM receitas WHERE id_usuario = $id";
$result_total_receitas = $conn->query($sql_total_receitas);
$row_total_receitas = $result_total_receitas->fetch_assoc();
$total_receitas = ($row_total_receitas['total_receitas'] ?? 0) + $renda_usuario;

// Consulta para calcular o total de gastos do usuário
$sql_total_gastos = "SELECT SUM(valor) AS total_gastos FROM gastos WHERE id_usuario = $id";
$result_total_gastos = $conn->query($sql_total_gastos);
$row_total_gastos = $result_total_gastos->fetch_assoc();
$total_gastos = $row_total_gastos['total_gastos'] ?? 0.00;

// Calcula o saldo total
$saldo_total = $total_receitas - $total_gastos;

if ($total_receitas != 0) {
    $percentual_gastos = ($total_gastos / $total_receitas) * 100;
} else {
    $percentual_gastos = 0; // ou qualquer valor padrão que faça sentido para o seu caso
}

// Alertas de gastos com classes de cores
$alerta = '';
$alerta_classe = '';
if ($percentual_gastos >= 90) {
    $alerta = 'Atenção: Você atingiu ' . number_format($percentual_gastos, 2, ',', '.') . '% do total de suas receitas!';
    $alerta_classe = 'alert-vermelho';
} elseif ($percentual_gastos >= 80) {
    $alerta = 'Atenção: Você atingiu ' . number_format($percentual_gastos, 2, ',', '.') . '% do total de suas receitas!';
    $alerta_classe = 'alert-laranja';
} elseif ($percentual_gastos >= 70) {
    $alerta = 'Atenção: Você atingiu ' . number_format($percentual_gastos, 2, ',', '.') . '% do total de suas receitas!';
    $alerta_classe = 'alert-amarelo';
}

// Consulta para obter a distribuição dos gastos por categoria
$sql_distribuicao_gastos = "SELECT categoria, SUM(valor) AS total_categoria FROM gastos WHERE id_usuario = $id GROUP BY categoria";
$result_distribuicao_gastos = $conn->query($sql_distribuicao_gastos);

// Inicializa um array para armazenar os dados do gráfico
$dados_grafico = [];
$porcentagens = [];
while ($row = $result_distribuicao_gastos->fetch_assoc()) {
    $dados_grafico[$row['categoria']] = $row['total_categoria'];
    // Calcula a porcentagem do gasto em relação ao total de gastos
    $porcentagem = ($row['total_categoria'] / $total_gastos) * 100;
    $porcentagens[] = number_format($porcentagem, 2, ',', '.') . '%';
}

// Consulta para calcular os gastos mensais do usuário
$sql_gastos_mensais = "
    SELECT YEAR(dt_gasto) AS ano, MONTH(dt_gasto) AS mes, SUM(valor) AS total_mensal 
    FROM gastos 
    WHERE id_usuario = $id 
    GROUP BY YEAR(dt_gasto), MONTH(dt_gasto)
    ORDER BY ano, mes";
$result_gastos_mensais = $conn->query($sql_gastos_mensais);

$gastos_mensais = [];
while ($row = $result_gastos_mensais->fetch_assoc()) {
    $ano_mes = str_pad($row['mes'], 2, '0', STR_PAD_LEFT) . '-' . $row['ano'];
    $gastos_mensais[$ano_mes] = $row['total_mensal'];
}
// Consulta para calcular os gastos anuais do usuário
$sql_gastos_anuais = "
    SELECT YEAR(dt_gasto) AS ano, SUM(valor) AS total_anual 
    FROM gastos 
    WHERE id_usuario = $id 
    GROUP BY YEAR(dt_gasto)
    ORDER BY ano";
$result_gastos_anuais = $conn->query($sql_gastos_anuais);

$gastos_anuais = [];
while ($row = $result_gastos_anuais->fetch_assoc()) {
    $gastos_anuais[$row['ano']] = $row['total_anual'];
}

// Converte os dados do gráfico em formato JSON
$dados_grafico_json = json_encode($dados_grafico);
$porcentagens_json = json_encode($porcentagens);
$gastos_mensais_json = json_encode($gastos_mensais);
$gastos_anuais_json = json_encode($gastos_anuais);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início</title>
    <link rel="shortcut icon" type="imagex/png" href="../imagens/dinheiro.ico">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login-cadastro.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>

    </style>
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
                    <a class="nav-link" href="../php/novaRenda.php">Adicionar renda</a>
                    <a class="nav-link" href="../php/Inserir.php">Inserir</a>
                    <a class="nav-link" href="../php/listagem.php">Lista de gastos</a>
                    <a class="nav-link" href="../php/listaReceitas.php">Lista de receitas</a>
                    <a class="nav-link" href="../php/aprender.php">Educação financeira</a>
                </div>

                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="../php/logout.php"><img src="../imagens/sair.png" alt="" width="30px"></a>
                </div>
            </div>
        </div>
    </nav>

    <div class="row mt-4">
        <div class="col-md-12">

            <div class="container d-flex flex-wrap ">
                <div class="p-2">
                    <p><strong>Renda:</strong> R$ <?php echo number_format($renda_usuario, 2, ',', '.'); ?></p>
                </div>
                <div class="p-2">
                    <p><strong>Total de Receitas:</strong> R$ <?php echo number_format($total_receitas, 2, ',', '.'); ?></p>
                </div>
                <div class="p-2">
                    <p><strong>Total de Gastos:</strong> R$ <?php echo number_format($total_gastos, 2, ',', '.'); ?></p>
                </div>
                <div class="p-2">
                    <p><strong>Saldo Total:</strong> R$ <?php echo number_format($saldo_total, 2, ',', '.'); ?></p>
                </div>
                <?php if ($alerta) : ?>
                <div class="alert <?php echo $alerta_classe; ?>" role="alert">
                    <?php echo $alerta; ?>
                </div>
            <?php endif; ?>
            </div>
            
        </div>
    </div>


    <div class="container mt-4">
        <div class="row mt-4">
            <div class="col-md-6 ">
                <h3>Gastos Mensais</h3>
                <div id="grafico-mensal-container">
                    <canvas id="grafico-mensal"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <h3>Gastos Anuais</h3>
                <div id="grafico-anual-container">
                    <canvas id="grafico-anual"></canvas>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                <div class="text-center">
                    <h3>Distribuição dos Gastos por Categoria</h3>
                    <div id="grafico-container">
                        <canvas id="grafico"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="../js/bootstrap.min.js"></script>
</body>

</html>

<script>
    // Dados dos gráficos em formato JSON
    const dadosGrafico = <?php echo $dados_grafico_json; ?>;
    const porcentagens = <?php echo $porcentagens_json; ?>;
    const gastosMensais = <?php echo $gastos_mensais_json; ?>;
    const gastosAnuais = <?php echo $gastos_anuais_json; ?>;

    // Gráfico de Distribuição dos Gastos por Categoria
    const ctx = document.getElementById('grafico').getContext('2d');
    const grafico = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: Object.keys(dadosGrafico),
            datasets: [{
                data: Object.values(dadosGrafico),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 166, 47, 0.7)',
                    'rgba(128, 128, 128, 0.9)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 215, 0, 0.7)',
                    'rgba(165, 42, 42, 0.7)',
                    'rgba(50, 205, 50, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 166, 47, 1)',
                    'rgba(128, 128, 128, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 215, 0, 1)',
                    'rgba(165, 42, 42, 1)',
                    'rgba(50, 205, 50, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + ' (' + porcentagens[context.dataIndex] + ')';
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Gastos Mensais
    // Dados para o gráfico de gastos mensais
    var ctxMensal = document.getElementById('grafico-mensal').getContext('2d');
    var dadosMensais = <?php echo $gastos_mensais_json; ?>;

    new Chart(ctxMensal, {
        type: 'line',
        data: {
            labels: Object.keys(dadosMensais),
            datasets: [{
                label: 'Gastos Mensais',
                data: Object.values(dadosMensais),
                backgroundColor: '#FFF',
                borderColor: '#9900F0',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    display: true,
                    beginAtZero: true,
                    text: 'Gastos (R$)'
                }
            }
        }
    });

    // Gráfico de Gastos Anuais
    const ctxAnual = document.getElementById('grafico-anual').getContext('2d');
    const graficoAnual = new Chart(ctxAnual, {
        type: 'bar',
        data: {
            labels: Object.keys(gastosAnuais),
            datasets: [{
                label: 'Gastos Anuais',
                data: Object.values(gastosAnuais),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: '#9900F0',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Ano'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Gastos (R$)'
                    }
                }
            }
        }
    });
</script>

<script src="../js/bootstrap.min.js"></script>
</body>

</html>