<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'trabalhopi';
$username = 'root';
$password = '';

$userId = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "trabalhopi");
$result = $conn->query("SELECT foto_perfil FROM usuarios WHERE id = $userId");
$row = $result->fetch_assoc();
$profilePicture = $row['foto_perfil'] ? "uploads/" . $row['foto_perfil'] : "images/perfil-de-usuario.png";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Inserir metas no banco
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_meta'])) {
    $usuario_id = $_SESSION['user_id'];
    $tempo_atividade = $_POST['tempoAtividade'];
    $quantidade_agua = $_POST['quantidadeAgua'];
    $refeicoes_saudaveis = $_POST['refeicoesSaudaveis'];

    // Inserir metas na tabela
    $sql = "INSERT INTO metas (usuario_id, tempo_atividade, quantidade_agua, refeicoes_saudaveis, criado_em) 
            VALUES (:usuario_id, :tempo_atividade, :quantidade_agua, :refeicoes_saudaveis, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ 
        ':usuario_id' => $usuario_id,
        ':tempo_atividade' => $tempo_atividade,
        ':quantidade_agua' => $quantidade_agua,
        ':refeicoes_saudaveis' => $refeicoes_saudaveis
    ]);

    header('Location: atividade.php');
    exit;
}

// Inserir atividades e atualizar metas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_atividade'])) {
    $usuario_id = $_SESSION['user_id'];
    $tipo_atividade = $_POST['tipoAtividade'];
    $tempo = $_POST['tempoAtividade'];
    $calorias = $_POST['caloriasGastadas'];

    // Inserir atividade na tabela
    $sql = "INSERT INTO atividades (usuario_id, tipo_atividade, tempo, calorias_gastas, criado_em) 
            VALUES (:usuario_id, :tipo_atividade, :tempo, :calorias_gastas, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ 
        ':usuario_id' => $usuario_id,
        ':tipo_atividade' => $tipo_atividade,
        ':tempo' => $tempo,
        ':calorias_gastas' => $calorias
    ]);

    // Atualizar as metas
    $sql = "UPDATE metas SET 
            tempo_atividade = GREATEST(tempo_atividade - :tempo, 0),
            quantidade_agua = GREATEST(quantidade_agua - :agua_consumida, 0),
            refeicoes_saudaveis = GREATEST(refeicoes_saudaveis - :refeicoes, 0)
        WHERE usuario_id = :usuario_id AND criado_em = (
            SELECT MAX(criado_em) FROM metas WHERE usuario_id = :usuario_id
        )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tempo' => $tempo,
        ':agua_consumida' => $_POST['agua_consumida'] ?? 0,
        ':refeicoes' => $_POST['refeicoes_realizadas'] ?? 0,
        ':usuario_id' => $usuario_id
    ]);

    header('Location: atividade.php');
    exit;
}

// Buscar metas do usuário
$metas = [];
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT * FROM metas WHERE usuario_id = :usuario_id ORDER BY criado_em DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':usuario_id' => $_SESSION['user_id']]);
    $metas = $stmt->fetch(PDO::FETCH_ASSOC);
}

$meta_batida = false;
if ($metas && $metas['tempo_atividade'] == 0 && $metas['quantidade_agua'] == 0 && $metas['refeicoes_saudaveis'] == 0) {
    $meta_batida = true;
}

// Passar as novas metas para o JavaScript
$tempo_atividade_restante = $metas['tempo_atividade'] ?? 0;
$quantidade_agua_restante = $metas['quantidade_agua'] ?? 0;
$refeicoes_saudaveis_restante = $metas['refeicoes_saudaveis'] ?? 0;

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/atividade.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>FitTrack Atividade</title>
</head>

<body>

    <script>
        function abrirMenu() {
            document.getElementById("menu-lateral").style.width = "210px"; 
        }
  
        function fecharMenu() {
            document.getElementById("menu-lateral").style.width = "0";
        }
        function abrirModal() {
            document.getElementById("metasModal").style.display = "block";
        }

        function fecharModal() {
            document.getElementById("metasModal").style.display = "none";
        }

    </script>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <header>
        <nav>
            <div class="nav-container">
                <div class="nav-logo">
                    <h1>Fit</h1>
                    <h1><span>Track</span></h1>
                </div>
                <div class="nav-links">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php#sobre">Sobre</a></li>
                        <li><a href="index.php#atividades">Atividades</a></li>
                        <li><a href="index.php#feedbacks">Feedbacks</a></li>
                    </ul>
                </div>
                <div class="nav-conta">
                    <div class="div-nav-icone">
                        <img src="<?php echo $profilePicture; ?>" alt="Foto de Perfil" class="perfil" onclick="abrirMenu()">
                    </div>
                    <div id="menu-lateral" class="menuLateral">
                        <div class="flex-icone">
                            <div class="menu-icone">
                            <img src="<?php echo $profilePicture; ?>" alt="Foto de Perfil" class="perfil" onclick="fecharMenu()">
                            </div>
                        </div>
                        <div class="menu-geral">
                            <a href="user_dashboard.php">Perfil</a>
                            <div class="menu-meio">
                                <a href="plano.php">Planos e preços</a>
                                <a href="#">Histórico de atividades</a>
                            </div>
                            <div class="menu-baixo">
                                <a href="#">Sugerir melhoria</a>
                                <a href="#">Indicar amigos</a>
                                <a href="politica.php">Politica de privacidade</a>
                                <a href="logout.php">Sair</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container-main-metas">
            <div id="metasModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="fecharModal()">&times;</span>
                    <h2>Meta do Dia</h2>
                    <?php if ($meta_batida): ?>
                        <p>Muito bem, você bateu a sua meta hoje!</p>
                    <?php elseif ($metas): ?>
                        <ul>
                            <li>Tempo Restante: <?= $metas['tempo_atividade'] ?> minutos</li>
                            <li>Água Restante: <?= $metas['quantidade_agua'] ?> ml</li>
                            <li>Refeições Restantes: <?= $metas['refeicoes_saudaveis'] ?></li>
                        </ul>
                    <?php else: ?>
                        <p>Estipule uma meta para hoje!</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="container-geral-titulo">
                <p>Estipule <span>Metas</span> e Alcance seus Resultados!</p>
            </div>

            <div class="container-metas">
                <div class="container-geral-txt">
                    <p>Estipular metas para atividades físicas é essencial para alcançar resultados e manter a motivação. Metas claras dão direção, ajudando a transformar grandes objetivos em pequenas conquistas diárias. Além disso, elas aumentam o foco, a disciplina e o comprometimento, permitindo que você acompanhe seu progresso e celebre cada avanço, por menor que seja. Defina suas metas, seja persistente, e verá como cada esforço o aproxima de uma versão mais saudável e forte de si mesmo!</p>
                </div>
                <div class="container-geral-imagem">
                    <img src="images/undraw_Target_re_fi8j.png" alt="Imagem Metas">
                </div>
            </div>

            <div class="container-definir-metas">
                <div class="container-geral-titulo">
                    <p class="cor">Começe por aqui.</p>
                    <p>Vamos estipular uma <span>meta!</span></p>
                </div>

                <div class="container-definir-metas-form">
                    <div class="definir-metas-titulo">
                        <p>Responda as perguntas a baixo.</p>
                    </div>
                    <form action="" method="POST">
                        <label for="tempoAtividade">Meta de Tempo de Atividade (min):</label>
                        <input type="number" id="tempoAtividade" name="tempoAtividade" min="0" required>
                        <label for="quantidadeAgua">Meta de Consumo de Água (ml):</label>
                        <input type="number" id="quantidadeAgua" name="quantidadeAgua" min="0" required>
                        <label for="refeicoesSaudaveis">Meta de Refeições Saudáveis:</label>
                        <input type="number" id="refeicoesSaudaveis" name="refeicoesSaudaveis" min="0" required>
                        <button type="submit" name="submit_meta">Definir Metas</button>
                    </form>
                </div>

            </div>

        </div>

        <div class="btn-metas">
            <button id="btn" onclick="abrirModal()">Acompanhar meta!</button>
        </div>


        <div class="container-main-atividades">
            <div class="container-geral-titulo">
                <p>Conquiste Seus Objetivos: <span>Atividades</span> Fazem a Diferença!</p>
            </div>

            <div class="container-atividades">
                <div class="container-geral-imagem">
                    <img src="images/undraw_bike_ride_7xit.png" alt="Imagem Atividades">
                </div>
                <div class="container-geral-txt">
                    <p>Inicie sua jornada fitness com atividades que realmente fazem a diferença! Cada exercício é uma oportunidade de superar seus limites e conquistar suas metas. Experimente novos desafios, mantenha-se motivado e registre seu progresso. Lembre-se: a consistência é a chave para o sucesso! Ao se comprometer com essas atividades, você estará um passo mais perto de uma versão mais saudável e forte de si mesmo. Vamos juntos transformar esforço em conquistas!</p>
                </div>
            </div>

            <div class="container-geral-titulo">
                <p class="cor">Registre suas atividades!</p>
                <p>O que você já realizou hoje?</p>
            </div>

            <div class="definir-metas-titulo">
                <p>Responda as perguntas a baixo.</p>
            </div>
            <form action="" method="POST">
                <label for="tipoAtividade">Tipo de Atividade:</label>
                <select id="tipoAtividade" name="tipoAtividade">
                    <option value="corrida">Corrida</option>
                    <option value="caminhada">Caminhada</option>
                    <option value="musculacao">Musculação</option>
                </select>
                <label for="tempoAtividade">Tempo de Atividade (min):</label>
                <input type="number" id="tempoAtividade" name="tempoAtividade" min="0" required>
                <label for="caloriasGastadas">Calorias Gastas:</label>
                <input type="number" id="caloriasGastadas" name="caloriasGastadas" min="0" required>
                <label for="aguaConsumida">Água Consumida (ml):</label>
                <input type="number" id="aguaConsumida" name="agua_consumida" min="0">
                <label for="refeicoesRealizadas">Refeições Saudáveis:</label>
                <input type="number" id="refeicoesRealizadas" name="refeicoes_realizadas" min="0">
                <button type="submit" name="submit_atividade">Registrar Atividade</button>
            </form>
        </div>

        <div class="grafico-geral">
            <div class="grafico-titulo">
                <p>Acompanhe a sua <span>jornada</span> diária</p>
            </div>
            <div class="grafico">
                <canvas id="graficoMetas" width="400" height="200"></canvas>
            </div>

        </div>
    </main>

    <footer>
        <div class="container-footer">
            <div class="footer-logo">
                <h2>Fit</h2>
                <h2><span>Track</span></h2>
            </div>
            <div class="footer-diretos">
                <address>Todos os direitos reservados.&#169;</address>
            </div>
            <div class="footer-autores">
                <p>Autores</p>
                <ul>
                    <li>Jonathan Aquino</li>
                    <li>Ludmila Zanardi</li>
                    <li>Marcelo Augusto</li>
                </ul>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>

    <script>

        const metas = {
            tempoAtividade: <?= $tempo_atividade_restante ?>,
            quantidadeAgua: <?= $quantidade_agua_restante ?>,
            refeicoesSaudaveis: <?= $refeicoes_saudaveis_restante ?>,
            progresso: {
                tempo: <?= isset($_POST['tempoAtividade']) ? $_POST['tempoAtividade'] : 0 ?>,
                agua: <?= isset($_POST['agua_consumida']) ? $_POST['agua_consumida'] : 0 ?>,
                refeicoes: <?= isset($_POST['refeicoes_realizadas']) ? $_POST['refeicoes_realizadas'] : 0 ?>
            }
        };

        const ctx = document.getElementById('graficoMetas').getContext('2d');

        const restante = [
            metas.tempoAtividade, 
            metas.quantidadeAgua, 
            metas.refeicoesSaudaveis
        ];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Tempo de Atividade (min)', 'Água (ml)', 'Refeições Saudáveis'],
                datasets: [
                    {
                        label: 'Restante',
                        data: restante,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>

</body>
</html>