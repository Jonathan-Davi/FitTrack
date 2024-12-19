<?php

include 'config.php'; // Conexão com o banco

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']); // Verifica o campo username (arrumado)
    $password = trim($_POST['senha']);

    // Verifica se o nome de usuário e senha foram preenchidos
    if (empty($username) || empty($password)) {
        echo "Preencha todos os campos!";
        exit;
    }

    // Buscar o usuário no banco de dados (campo username é o nome do cara)
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$username]);  // Aqui, ele busca pelo nome inserido no login
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe e se a senha está correta
    if ($usuario && password_verify($password, $usuario['senha'])) {
        // Login bem-sucedido: iniciar a sessão
        session_start();
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['username'] = $usuario['username']; // Usar o nome
        $_SESSION['perfil'] = $usuario['perfil'];

        // Redirecionar com base no perfil do usuário
        if ($usuario['perfil'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        echo "Usuário ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <title>FitTrack Login</title>
</head>

<body>

    <main>
        <div class="container-main">
            <div class="container-imagem">
                <img src="images/undraw_Access_account_re_8spm.png" alt="Imagem Login">
            </div>
            <div class="container-main-login">
                <div class="container-main-login-titulo">
                    <h2>Fit<span>Track</span></h2>
                    <h3>Login.</h3>
                </div>
                <form method="POST" action="login.php"> 
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" placeholder="" required>
                        <label for="username">E-mail</label>
                    </div>

                    <div class="form-floating">
                        <input type="password" name="senha" class="form-control" placeholder="Senha" required>
                        <label for="senha">Senha</label>
                    </div>
                    <div class="container-main-login-btn">
                        <button type="submit">Entrar</button>
                    </div>
                </form>

                <div class="container-main-login-forget">
                    <p>Esqueceu a senha? <a href="#">Recuperar Senha.</a></p>
                    <p>Não possui uma conta? <a href="cadastro.php">Cadastrar.</a></p>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>

</body>

</html>