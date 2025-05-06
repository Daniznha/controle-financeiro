document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('grafico').getContext('2d');
    var grafico;

    // Função para criar e atualizar o gráfico
    function criarGrafico(tipo) {
        if (grafico) {
            grafico.destroy(); // Destruir o gráfico anterior antes de criar um novo
        }

        var dados;
        if (tipo === 'mensal') {
            dados = {
                labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                datasets: [{
                    label: 'Gastos Mensais',
                    data: [100, 200, 150, 300, 250, 180, 220, 190, 280, 230, 320, 270],
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };
        } else if (tipo === 'anual') {
            dados = {
                labels: ['2019', '2020', '2021', '2022'],
                datasets: [{
                    label: 'Gastos Anuais',
                    data: [1200, 2400, 1800, 3600],
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };
        }

        grafico = new Chart(ctx, {
            type: 'bar',
            data: dados,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Listener para mudanças na seleção do dropdown
    document.getElementById('tipo-grafico').addEventListener('change', function () {
        criarGrafico(this.value);
    });

    // Criar gráfico inicial com tipo 'mensal'
    criarGrafico('mensal');
});
