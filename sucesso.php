<?php
// Captura as informações passadas via URL
$nome = $_GET['nome'] ?? '';
$email = $_GET['email'] ?? '';
$senha = $_GET['senha'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Realizado com Sucesso</title>
    <link rel="stylesheet" href="sucesso.css">
</head>
<body>
    <div class="form-container">
        <header>
            <h1>Cadastro Realizado com Sucesso!</h1>
        </header>
        
        <div>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome); ?></p>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Senha Gerada:</strong> <?php echo htmlspecialchars($senha); ?></p>
        </div>

        <div class="button-group">
            <!-- Botão para voltar para o Dashboard -->
            <button class="exit" onclick="window.location.href='Dashboard.php'">Voltar para o Dashboard</button>
            <!-- Botão para ver a lista de usuários -->
            <button class="save" onclick="window.location.href='lista.php'">Ver Lista de Funcionários</button>
            <!-- Botão para adicionar outro usuário -->
            <button class="delete" onclick="window.location.href='adicionar_usuario.php'">Adicionar Outro Usuário</button>
        </div>
    </div>
</body>
</html>
