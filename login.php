<?php
// Ativar exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir o arquivo de conexão
include 'conexao.php';
session_start(); // Iniciar a sessão

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_address = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Verificar se os campos foram preenchidos
    if (empty($email_address) || empty($password)) {
        echo "<p style='color: red;'>Por favor, preencha todos os campos!</p>";
    } else {
        // Preparar a consulta para buscar o usuário e o cargo
        $sql = "SELECT email_address, Senha, Cargo FROM usuarios WHERE email_address = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email_address);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verificar se o usuário foi encontrado
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $hash_armazenado = $user['Senha'];
                $cargo = $user['Cargo'];

                // Verificar a senha
                if (password_verify($password, $hash_armazenado)) {
                    // Armazenar o e-mail e o cargo na sessão
                    $_SESSION['user_email'] = $user['email_address'];
                    $_SESSION['user_cargo'] = $cargo;

                    echo "<p style='color: green;'>Login bem-sucedido! Redirecionando...</p>";
                    header("Location: Dashboard.php");
                    exit();
                } else {
                    echo "<p style='color: red;'>Senha incorreta!</p>";
                }
            } else {
                echo "<p style='color: red;'>Usuário não encontrado!</p>";
            }

            $stmt->close();
        } else {
            echo "<p style='color: red;'>Erro ao preparar a consulta: " . $conn->error . "</p>";
        }
    }
}

$conn->close();
?>
