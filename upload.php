<?php
session_start(); // Para associar a imagem ao usuário logado

$userId = $_SESSION['user_id']; // ID do usuário logado
$targetDir = "uploads/"; // Diretório onde as imagens serão salvas
$fileName = $userId . "_" . basename($_FILES["profile_picture"]["name"]);
$targetFilePath = $targetDir . $fileName;
$imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

// Verifica se o arquivo é uma imagem válida
$check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
if ($check !== false) {
    // Permite apenas alguns tipos de arquivo (jpg, jpeg, png, gif)
    if (in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
            // Salva o caminho da imagem no banco de dados
            $conn = new mysqli("localhost", "root", "", "trabalhopi");
            $sql = "UPDATE usuarios SET foto_perfil = '$fileName' WHERE id = $userId";
            $conn->query($sql);
            $conn->close();
        }
    }
}

header('Location: user_dashboard.php');

?>
