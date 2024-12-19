<?php

    session_start();

    if (!isset($_SESSION['user_id'])) {

        header('Location: login.php');
        exit;
    }

    $userId = $_SESSION['user_id'];
    $conn = new mysqli("localhost", "root", "", "trabalhopi");
    $result = $conn->query("SELECT foto_perfil FROM usuarios WHERE id = $userId");
    $row = $result->fetch_assoc();
    $profilePicture = $row['foto_perfil'] ? "uploads/" . $row['foto_perfil'] : "images/perfil-de-usuario.png";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
        $comentario = trim($_POST['comentario']);
        
        if (!empty($comentario)) {
            $stmt = $conn->prepare("INSERT INTO comentarios (user_id, comentario) VALUES (?, ?)");
            $stmt->bind_param("is", $userId, $comentario);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $stmt = $conn->prepare("SELECT c.comentario, c.data_criacao, u.username, u.foto_perfil 
                        FROM comentarios c
                        JOIN usuarios u ON c.user_id = u.id
                        ORDER BY c.data_criacao DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $comentarios = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/style.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <title>FitTrack Home</title>
</head>

<body>

    <script>
        function abrirMenu() {
            document.getElementById("menu-lateral").style.width = "210px"; 
        }
  
        function fecharMenu() {
            document.getElementById("menu-lateral").style.width = "0";
        }
    </script>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <header id="home">
        <nav>
                <div class="nav-container">
                    <div class="nav-logo">
                        <h1>Fit</h1>
                        <h1><span>Track</span></h1>
                    </div>
                    <div class="nav-links">
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#sobre">Sobre</a></li>
                            <li><a href="#atividades">Atividades</a></li>
                            <li><a href="#feedbacks">Feedbacks</a></li>
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
                                    <a href="plano.php" target="_blank">Planos e preços</a>
                                    <a href="#">Histórico de atividades</a>
                                </div>
                                <div class="menu-baixo">
                                    <a href="#">Sugerir melhoria</a>
                                    <a href="#">Indicar amigos</a>
                                    <a href="politica.php" target="_blank">Politica de privacidade</a>
                                    <a href="logout.php">Sair</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

        <div class="header-container">
            <div class="div-header-texto" data-aos="fade-right" data-aos-delay="400" data-aos-duration="600">
                <div class="texto">
                    <h1>Feeling better starts with</h1>
                    <h1><span>MOVING BETTER</span></h1>
                </div>
                <div class="div-header-saiba-mais">
                    <button><a href="atividade.php">Pronto para começar as atividades?</a></button>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container-sobre" id="sobre">
            <div class="imagem-sobre" data-aos="fade-up" data-aos-delay="150" data-aos-duration="600">
                <img src="images/undraw_Team_spirit_re_yl1v.png" alt="Imagem Sobre">
            </div>
            <div class="geral-texto">
                <div class="texto-titulo" data-aos="fade-right" data-aos-delay="150" data-aos-duration="860">
                    <h2>Sobre Nós</h2>
                </div>
                <div class="txt">
                    <div class="texto-sobre" data-aos="fade-right" data-aos-delay="250" data-aos-duration="600">
                        <p>FitTrack é uma plataforma completa de rastreamento de atividades físicas, projetada para auxiliar você a monitorar e gerenciar seu desempenho de forma prática e eficiente.</p>
                    </div>
                    <div class="texto-meta" data-aos="fade-right" data-aos-delay="350" data-aos-duration="600">
                        <p>Na FitTrack, acreditamos que cada conquista começa com uma meta. Sabemos que o caminho para uma vida mais saudável pode ser desafiador, mas estamos aqui para ajudar você a se manter motivado e no controle do seu progresso.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-funcionalidades">
            <div class="func-cards">
                <div class="func" data-aos="fade-right" data-aos-delay="150" data-aos-duration="600">
                    <div class="func-icone">
                        <img src="images/conquistar.png" alt="Icone de Conquista">
                    </div>
                    <div class="func-texto">
                        <div class="func-texto-titulo">
                            <h3><span>Planeje Suas Rotinas de Treino</span></h3>
                        </div>
                        <div class="func-txt">
                            <p>Com a FitTrack, você pode criar e organizar suas rotinas de treino de forma prática. Monte seu cronograma semanal de exercícios e ajuste conforme necessário, garantindo que seu planejamento esteja sempre alinhado aos seus objetivos e disponibilidade.                            </p>
                        </div>
                    </div>
                </div>
                <div class="func" data-aos="fade-right" data-aos-delay="250" data-aos-duration="600">
                    <div class="func-icone">
                        <img src="images/trofeu.png" alt="Icone Trofeu">
                    </div>
                    <div class="func-texto">
                        <div class="func-texto-titulo">
                            <h3><span>Adicione Suas Atividades com Facilidade</span></h3>
                        </div>
                        <div class="func-txt">
                            <p>Com a FitTrack, você pode registrar suas atividades físicas rapidamente, mantendo um histórico completo dos seus treinos e ajustando seu planejamento para melhores resultados.</p>
                        </div>
                    </div>
                </div>
                <div class="func" data-aos="fade-right" data-aos-delay="350" data-aos-duration="600">
                    <div class="func-icone">
                        <img src="images/prancheta.png" alt="Icone Prancheta">
                    </div>
                    <div class="func-texto">
                        <div class="func-texto-titulo">
                            <h3><span>Monitore Suas Metas</span></h3>
                        </div>
                        <div class="func-txt">
                            <p>Acompanhe suas metas de forma fácil com a FitTrack! Veja seu progresso em tempo real por meio de gráficos e relatórios detalhados, garantindo que você esteja sempre no caminho certo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-atividades" id="atividades">
            <div class="atividades-esquerda" data-aos="fade-left" data-aos-delay="150" data-aos-duration="600">
                <div class="atividades-esquerda-texto">
                    <p id="txt-maior"><span>Você é capaz de alcançar suas metas!</span></p>
                    <p>Cada passo que você dá te aproxima do seu objetivo. Mantenha o foco, celebre as pequenas conquistas e nunca desista. Lembre-se: é o esforço diário que constrói o sucesso. Acredite em si mesmo e siga em frente – você pode chegar lá!</p>
                </div>
                <div class="btn-esquerda">
                    <button><a href="atividade.php">Registrar Atividade!</a></button>
                </div>
            </div>
            <div class="container-slider" data-aos="fade-left" data-aos-delay="250" data-aos-duration="600">
                <div class="slides">
                    <input type="radio" name="radiobtn" id="radio1">
                    <input type="radio" name="radiobtn" id="radio2">
                    <input type="radio" name="radiobtn" id="radio3">

                    <div class="slide first">
                        <img src="images/imagem-corrida-de-rua.jpg" alt="Imagem Corrida">
                    </div>
                    <div class="slide">
                        <img src="images/imagem-ciclismo.jpg" alt="Imagem Ciclismo">
                    </div>
                    <div class="slide">
                        <img src="images/imagem-academia.jpg" alt="Imagem Academia">
                    </div>

                    <div class="navigation-auto">
                        <div class="auto-btn1"></div>
                        <div class="auto-btn2"></div>
                        <div class="auto-btn3"></div>
                    </div>
                </div>

                <div class="manual-navigation">
                    <label class="manual-btn" for="radio1"></label>
                    <label class="manual-btn" for="radio2"></label>
                    <label class="manual-btn" for="radio3"></label>
                </div>
            </div>
        </div>

        <div class="container-feedbacks" id="feedbacks">
            <p><span>Feedbacks</span></p>
            <div class="container-feed">
                <div class="feed-icone">
                    <img src="<?php echo $profilePicture; ?>" alt="Perfil">
                </div>
                <form action="" method="POST" class="feed-form">
                    <div class="feed-comentario">
                        <textarea name="comentario" id="comentario" placeholder="O seu comentário faz toda a diferença!!" required></textarea>
                    </div>
                    <div class="feed-btn">
                        <button type="submit">Enviar</button>
                    </div>
                </form>
            </div>

            <div class="container-comentarios">
                <?php if (!empty($comentarios)): ?>
                    <?php foreach ($comentarios as $comentario): ?>
                        <div class="comentario-item">
                            <div class="comentario-texto">
                                <h4><?php echo htmlspecialchars($comentario['username']); ?></h4>
                                <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                                <small><?php echo date('d/m/Y H:i', strtotime($comentario['data_criacao'])); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <h4>Seja o primeiro a comentar!</h4>
                <?php endif; ?>
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

</body>

</html>