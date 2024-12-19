<?php
// Conexão com o banco de dados ( para testar na sua maquina não precisa mudar nada, abre o xampp, liga mysql, vai em admin e cria o banco igualzinho do arquivo banco.sql)
$host = 'localhost';
$db = 'trabalhopi';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro na conexão: ' . $e->getMessage());
}

// Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['senha']);
    $confirm_password = trim($_POST['confirma_senha']);
    $telefone = trim($_POST['telefone']);

    // Validação básica
    if (empty($username) || empty($email) || empty($password) || empty($telefone)) {
        echo "Preencha todos os campos!";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "As senhas não coincidem!";
        exit;
    }

    // Verificar se o usuário já existe ( mesma tabela de exemplo que tinha feito)
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "Email ja cadastrado!";
    } else {
        // Hash da senha para segurança
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Inserir o novo usuário no banco de dados
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, senha, telefone, perfil) VALUES (?, ?, ?, ?, 'comum')");
        if ($stmt->execute([$username, $email, $hashedPassword, $telefone])) {
            echo "Cadastro realizado com sucesso!";
            // Redireciona para a página de login (opcional/ não vai ser mais opcional não....)
            header("Location: login.php");
            exit;
        } else {
            echo "Erro ao cadastrar usuário.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/cadastro.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <title>FitTrack Cadastro</title>
</head>
<body>

    <main>
        <div class="container-main">
            <div class="container-main-cadastro">
                <div class="container-main-cadastro-titulo">
                    <h2>Fit<span>Track</span></h2>
                    <h3>Cadastro.</h3>
                </div>
                <div class="container-main-txt">
                    <p>Realize seu cadastro e faça parte da nossa <span>comunidade!</span></p>
                    <p>Já possui uma conta? <a href="login.php">Entrar!</a></p>
                </div>
                <form method="POST" action="cadastro.php"> <!-- Ação definida para enviar ao próprio arquivo -->
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" placeholder="" required>
                        <label for="username">Nome</label>
                    </div>
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="" required>
                        <label for="email">E-Mail</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="" required>
                        <label for="senha">Senha</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" placeholder="" required>
                        <label for="confirma_senha">Repita sua Senha</label>
                    </div>
                    <div class="form-floating">
                        <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="" required>
                        <label for="telefone">Telefone</label>
                    </div>
                    <div class="container-main-login-btn">
                        <button type="submit">Criar Conta</button>
                    </div>
                </form>
            </div>
            <div class="container-imagem">
                <img src="images/undraw_Profile_details_re_ch9r.png" alt="Imagem Cadastro">
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    
</body>
</html>
