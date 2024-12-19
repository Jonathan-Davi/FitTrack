<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "trabalhopi");

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $peso = isset($_POST['peso']) ? (float) $_POST['peso'] : null;
    $altura = isset($_POST['altura']) ? (float) $_POST['altura'] : null;

    $stmt = $conn->prepare("UPDATE usuarios SET peso = ?, altura = ? WHERE id = ?");
    $stmt->bind_param("ddi", $peso, $altura, $userId);

    if ($stmt->execute()) {
        echo "<script>alert('Dados atualizados com sucesso!');</script>";
        header("Refresh:0");
    } else {
        echo "<script>alert('Erro ao atualizar os dados. Tente novamente.');</script>";
    }
}

// Recuperar informações do usuário
$result = $conn->query("SELECT foto_perfil, perfil, peso, altura FROM usuarios WHERE id = $userId");
$row = $result->fetch_assoc();

if ($row['perfil'] == 'admin') {
    header('Location: admin_dashboard.php'); 
    exit;
}

$profilePicture = $row['foto_perfil'] ? "uploads/" . $row['foto_perfil'] : "images/perfil-de-usuario.png";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/usuario.css">

    <title>FitTrack Usuário</title>
</head>

<body>

    <main>

        <div class="container-geral">
            <div class="msg">
                <h2>Bem-vindo, <?php echo $_SESSION['username']; ?>!</h2>
                <p>Aqui você pode gerenciar suas atividades.</p>
                <a href="index.php">Página Inicial</a>
            </div>
            <div class="main-foto">
                <img src="<?php echo $profilePicture; ?>" alt="Foto de Perfil">
            </div>
            <div class="main-geral">
                <div class="main-arquivo">
                    <form action="upload.php" method="post" enctype="multipart/form-data" id="form">
                        <input type="file" name="profile_picture" accept="image/*" required>
                        <button type="submit">Enviar</button>
                    </form>
                </div>
                <div class="flex">
                    <div class="main-esquerda">
                        <div class="esquerda-infos">
                            <div class="infos-titulo">
                                <h3>Informações.</h3>
                            </div>
                            <div class="infos">
                                <label for="nome">Usuário: <?php echo $_SESSION['username']; ?></label>
                                <label for="peso">Peso: <?php echo $row['peso'] ? $row['peso'] . ' kg' : 'Não informado'; ?></label>
                                <label for="altura">Altura: <?php echo $row['altura'] ? $row['altura'] . ' m' : 'Não informado'; ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="main-direita">
                        <div class="infos-titulo">
                            <h3>Completar cadastro.</h3>
                        </div>
                        <div class="direita-complete">
                            <form method="POST" action="">
                                <label for="peso">Peso</label>
                                <input type="text" name="peso" placeholder="Digite seu peso!" value="<?php echo $row['peso'] ?: ''; ?>">
                                <label for="altura">Altura</label>
                                <input type="text" name="altura" placeholder="Digite sua altura!" value="<?php echo $row['altura'] ?: ''; ?>">
                                <button type="submit">Concluir</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sair">
                <a href="logout.php">Logout</a>
            </div>
        </div>

    </main>

</body>

</html>

