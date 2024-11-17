<?php
include 'conexao.php';

$id = $_GET['id'] ?? ''; // Recupera o ID do usuário a ser editado

$user = [];

// Verificar se o ID foi passado
if ($id) {
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se o usuário foi encontrado
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "<p>Usuário não encontrado.</p>";
        exit();
    }

    $stmt->close();
} else {
    echo "<p>ID do usuário não foi fornecido.</p>";
    exit();
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $cargo = $_POST['cargo'] ?? '';
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    // Verificar se a nova senha foi fornecida e se as senhas coincidem
    if ($nova_senha && $nova_senha !== $confirmar_senha) {
        echo "<p style='color: red;'>As senhas não coincidem.</p>";
    } else {
        // Atualizar dados do usuário (e-mail, telefone, cargo)
        $update_sql = "UPDATE usuarios SET email_address = ?, telefone = ?, cargo = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $email, $telefone, $cargo, $id);

        if ($stmt->execute()) {
            echo "<p>Dados atualizados com sucesso!</p>";

            // Se uma nova senha foi fornecida, atualizar a senha
            if ($nova_senha) {
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $update_senha_sql = "UPDATE usuarios SET Senha = ? WHERE id = ?";
                $stmt_senha = $conn->prepare($update_senha_sql);
                $stmt_senha->bind_param("si", $senha_hash, $id);

                if ($stmt_senha->execute()) {
                    echo "<p>Senha atualizada com sucesso!</p>";
                } else {
                    echo "<p>Erro ao atualizar a senha.</p>";
                }
                $stmt_senha->close();
            }
        } else {
            echo "<p>Erro ao atualizar os dados: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="editar_usuario.css"> 
    <!-- Font Awesome para os ícones de olho -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <header>
            <h1>Editar Usuário</h1>
        </header>
        
        <form method="POST">
            <div class="left-panel">
                <!-- Nome e Sobrenome desabilitados -->
                <label>Nome:</label>
                <input type="text" value="<?php echo htmlspecialchars($user['Nome'] ?? ''); ?>" disabled>

                <label>Sobrenome:</label>
                <input type="text" value="<?php echo htmlspecialchars($user['Sobrenome'] ?? ''); ?>" disabled>
                
                <!-- E-mail, Telefone e Cargo preenchidos com os dados do banco -->
                <label>E-mail:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email_address'] ?? ''); ?>" required>
                
                <label>Telefone:</label>
                <input type="text" name="telefone" value="<?php echo htmlspecialchars($user['Telefone'] ?? ''); ?>" required>
                
                <label>Cargo:</label>
                <input type="text" name="cargo" value="<?php echo htmlspecialchars($user['Cargo'] ?? ''); ?>" required>

                <!-- Campos para alteração de senha -->
                <label>Senha Atual:</label>
                <div class="password-container">
                    <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite a senha atual" required>
                    <span class="eye-icon" onclick="togglePasswordVisibility('senha_atual')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <label>Nova Senha:</label>
                <div class="password-container">
                    <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite a nova senha (opcional)">
                    <span class="eye-icon" onclick="togglePasswordVisibility('nova_senha')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <label>Confirmar Nova Senha:</label>
                <div class="password-container">
                    <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme a nova senha (opcional)">
                    <span class="eye-icon" onclick="togglePasswordVisibility('confirmar_senha')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="save">Salvar Alterações</button>
                <a href="lista.php" class="delete">Voltar para a lista</a>
            </div>
        </form>
    </div>

    <script>
        // Função para alternar a visibilidade das senhas
        function togglePasswordVisibility(id) {
            var passwordField = document.getElementById(id);
            var eyeIcon = passwordField.nextElementSibling; // Acessa o ícone de olho

            if (passwordField.type === "password") {
                passwordField.type = "text"; // Exibe a senha
                eyeIcon.innerHTML = '<i class="fas fa-eye-slash"></i>'; // Altera o ícone para olho fechado
            } else {
                passwordField.type = "password"; // Oculta a senha
                eyeIcon.innerHTML = '<i class="fas fa-eye"></i>'; // Altera o ícone para olho aberto
            }
        }
    </script>
</body>
</html>
