<?php
session_start();

$host = 'localhost';
$db = 'trabalhopi';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão: ' . $e->getMessage();
    exit;
}

$stmt = $pdo->query("SELECT id, username, email, peso, altura FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT c.id, c.comentario, c.data_criacao, u.username, u.foto_perfil
    FROM comentarios c
    JOIN usuarios u ON c.user_id = u.id
    ORDER BY c.data_criacao DESC
");
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['user_id']) || $_SESSION['perfil'] !== 'admin') {
    header('Location: user_dashboard.php');
    exit;
}

if (isset($_GET['excluir_id'])) {
    $id_usuario = $_GET['excluir_id'];
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $id_usuario);
    $stmt->execute();
    header('Location: admin_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_usuario_id'])) {
    $id_usuario = $_POST['editar_usuario_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $peso = $_POST['peso'];
    $altura = $_POST['altura'];

    $stmt = $pdo->prepare("UPDATE usuarios SET username = :username, email = :email, peso = :peso, altura = :altura WHERE id = :id");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':peso', $peso);
    $stmt->bindParam(':altura', $altura);
    $stmt->bindParam(':id', $id_usuario);
    $stmt->execute();
    header('Location: admin_dashboard.php');
    exit;
}

if (isset($_GET['excluir_comentario_id'])) {
    $id_comentario = $_GET['excluir_comentario_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = :id");
        $stmt->bindParam(':id', $id_comentario);
        $stmt->execute();
        header('Location: admin_dashboard.php');
        exit;
    } catch (PDOException $e) {
        echo 'Erro ao excluir comentário: ' . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
    <title>FitTrack Admin</title>
</head>


<body>

    <main>
        <div class="container-geral">
            <div class="msg">
                <h2>Bem-vindo ao Painel Administrativo, <?php echo $_SESSION['username']; ?>!</h2>
                <p>Aqui você pode gerenciar todos os usuários.</p>
                <a href="index.php">Página Inicial</a>
            </div>

            <div class="main-geral">
                <div class="titulo">
                    <h3>Usuários Cadastrados</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Nome de Usuário</th>
                            <th>Email</th>
                            <th>Peso</th>
                            <th>Altura</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['username']; ?></td>
                                <td><?php echo $usuario['email']; ?></td>
                                <td><?php echo $usuario['peso'] . ' kg'; ?></td>
                                <td><?php echo $usuario['altura'] . ' m'; ?></td>
                                <td>
                                    <button onclick="toggleForm(<?php echo $usuario['id']; ?>)">Editar</button>
                                    <a href="?excluir_id=<?php echo $usuario['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <form id="editForm<?php echo $usuario['id']; ?>" class="form-container" method="POST">
                                        <div class="form-flex">
                                            <input type="hidden" name="editar_usuario_id" value="<?php echo $usuario['id']; ?>">
                                            
                                            <label for="username"><h4>Nome de Usuário</h4></label>
                                            <input type="text" name="username" value="<?php echo $usuario['username']; ?>" required>
                                            
                                            <label for="email"><h4>Email</h4></label>
                                            <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
                                            
                                            <label for="peso"><h4>Peso (kg)</h4></label>
                                            <input type="text" name="peso" value="<?php echo $usuario['peso']; ?>" required>
                                            
                                            <label for="altura"><h4>Altura (m)</h4></label>
                                            <input type="text" name="altura" value="<?php echo $usuario['altura']; ?>" required>
                                            
                                            <div class="button-group">
                                                <div class="atualizar">
                                                    <button type="submit">Atualizar</button>
                                                </div>
                                                <div class="cancelar">
                                                    <button type="button" onclick="toggleForm(<?php echo $usuario['id']; ?>)">Cancelar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="comentarios">
                    <div class="comentarios-titulo">
                        <h3>Comentários dos Usuários</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Comentário</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comentarios as $comentario): ?>
                                <tr>
                                    <td>
                                        <?php echo $comentario['username']; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($comentario['comentario']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($comentario['data_criacao'])); ?></td>
                                    <td>
                                    <a href="admin_dashboard.php?excluir_comentario_id=<?php echo $comentario['id']; ?>" 
   onclick="return confirm('Tem certeza que deseja excluir este comentário?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="sair">
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleForm(id) {
            const form = document.getElementById('editForm' + id);
            form.style.display = (form.style.display === 'block') ? 'none' : 'block';
        }
    </script>

</body>

</html>
