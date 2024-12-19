<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos e Preços</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .pricing-header {
            background: #f8f9fa;
            padding: 40px 20px;
            text-align: center;
        }
        .pricing-header h1 {
            margin-bottom: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .btn-choose {
            background: #007bff;
            color: #fff;
            font-weight: bold;
        }
        .btn-choose:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="pricing-header">
        <h1>Escolha o Plano Ideal para Você</h1>
        <p>Planos flexíveis para atender suas necessidades</p>
    </div>

    <div class="container">
        <div class="row">
            <!-- Plano Básico -->
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header bg-light">
                        <h3>Plano Básico</h3>
                        <p class="price">R$ 29,90/mês</p>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li>Acesso a todas as funções básicas</li>
                            <li>Suporte por email</li>
                            <li>Registre até 30 atividades por mês</li>
                        </ul>
                        <a href="#" class="btn btn-choose btn-block">Escolher Plano</a>
                    </div>
                </div>
            </div>

            <!-- Plano Intermediário -->
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header bg-primary text-white">
                        <h3>Plano Intermediário</h3>
                        <p class="price">R$ 49,90/mês</p>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li>Tudo do plano básico</li>
                            <li>Registre atividades ilimitadas</li>
                            <li>Relatórios detalhados de desempenho</li>
                            <li>Suporte prioritário</li>
                        </ul>
                        <a href="#" class="btn btn-choose btn-block">Escolher Plano</a>
                    </div>
                </div>
            </div>

            <!-- Plano Premium -->
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header bg-dark text-white">
                        <h3>Plano Premium</h3>
                        <p class="price">R$ 79,90/mês</p>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li>Tudo do plano intermediário</li>
                            <li>Consultas personalizadas com especialistas</li>
                            <li>Integração com dispositivos de fitness</li>
                            <li>Prioridade máxima no suporte</li>
                        </ul>
                        <a href="#" class="btn btn-choose btn-block">Escolher Plano</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-4">
        <p>&copy; 2024 FitTrack. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
