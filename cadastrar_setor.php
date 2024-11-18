<?php
include 'conexao.php';

// Buscar os nomes dos usuários no banco de dados
$usuarios_sql = "SELECT CONCAT(Nome, ' ', Sobrenome) AS nome_completo FROM usuarios ORDER BY Nome";
$usuarios_result = $conn->query($usuarios_sql);

$nomes_usuarios = [];

if ($usuarios_result && $usuarios_result->num_rows > 0) {
    while ($row = $usuarios_result->fetch_assoc()) {
        $nomes_usuarios[] = $row['nome_completo'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Setor e Gestores</title>
    <link rel="stylesheet" href="cadastrar_setor.css">
    <script>
        // Função para adicionar um novo campo de gestor com autocomplete
        function adicionarGestor() {
            const container = document.getElementById('gestores-container');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'gestores[]';
            input.placeholder = 'Nome do Gestor (Opcional)';
            input.classList.add('gestor-input');
            input.setAttribute('list', 'usuarios-list'); // Adiciona a lista de sugestões
            container.appendChild(input);
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h1>Cadastrar Setor e Gestores</h1>
        <form method="POST" action="processar_setor.php">
            <label for="nome_setor">Nome do Setor:</label>
            <input type="text" id="nome_setor" name="nome_setor" required>

            <div id="gestores-container">
                <label>Nome do Gestor (Opcional):</label>
                <input type="text" name="gestores[]" placeholder="Nome do Gestor (Opcional)" class="gestor-input" list="usuarios-list">
            </div>

            <!-- Lista de sugestões de usuários -->
            <datalist id="usuarios-list">
                <?php
                foreach ($nomes_usuarios as $nome) {
                    echo "<option value='" . htmlspecialchars($nome) . "'>";
                }
                ?>
            </datalist>

            <button type="button" onclick="adicionarGestor()">Adicionar Gestor</button>
            <button type="submit">Cadastrar</button>
            <a href="lista.php" class="btn">Voltar para a Lista</a>
        </form>
    </div>
</body>
</html>
