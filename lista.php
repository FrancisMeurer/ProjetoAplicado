<?php
include 'conexao.php';

// Inicializar variáveis de filtro
$id = $_GET['id'] ?? '';
$nome = $_GET['nome'] ?? '';
$cargo = $_GET['cargo'] ?? '';
$setor = $_GET['setor'] ?? '';

// Obter lista de cargos distintos do banco de dados
$cargos_sql = "SELECT DISTINCT cargo FROM usuarios ORDER BY cargo";
$cargos_result = $conn->query($cargos_sql);

// Obter lista de setores distintos do banco de dados
$setores_sql = "SELECT DISTINCT setor FROM usuarios ORDER BY setor";
$setores_result = $conn->query($setores_sql);

// Preparar a consulta SQL com filtros
$sql = "SELECT id, nome, sobrenome, email_address, cargo, setor FROM usuarios WHERE 1=1";

if (!empty($id)) {
    $sql .= " AND id = " . intval($id);
}

if (!empty($nome)) {
    $sql .= " AND (nome LIKE '%" . $conn->real_escape_string($nome) . "%' OR sobrenome LIKE '%" . $conn->real_escape_string($nome) . "%')";
}

if (!empty($cargo)) {
    $sql .= " AND cargo = '" . $conn->real_escape_string($cargo) . "'";
}

if (!empty($setor)) {
    $sql .= " AND setor = '" . $conn->real_escape_string($setor) . "'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Funcionários</title>
    <link rel="stylesheet" href="lista.css">
    <!-- Link para o Font Awesome para usar os ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Cadastro de Funcionários</h1>

        <!-- Formulário de busca -->
        <form method="GET" action="lista.php" class="filter-form">
            <div>
                <label for="id">ID:</label>
                <input type="number" name="id" id="id" value="<?php echo htmlspecialchars($id); ?>">
            </div>
            <div>
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($nome); ?>">
            </div>
            <div>
                <label for="cargo">Cargo:</label>
                <select name="cargo" id="cargo">
                    <option value="">Todos</option>
                    <?php
                    if ($cargos_result && $cargos_result->num_rows > 0) {
                        while ($row = $cargos_result->fetch_assoc()) {
                            $selected = ($cargo === $row['cargo']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['cargo']) . "' $selected>" . htmlspecialchars($row['cargo']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="setor">Setor:</label>
                <select name="setor" id="setor">
                    <option value="">Todos</option>
                    <?php
                    if ($setores_result && $setores_result->num_rows > 0) {
                        while ($row = $setores_result->fetch_assoc()) {
                            $selected = ($setor === $row['setor']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['setor']) . "' $selected>" . htmlspecialchars($row['setor']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn">Filtrar</button>
            <a href="lista.php" class="btn reset-btn">Limpar Filtros</a>
        </form>

        <!-- Botões de navegação -->
        <div class="buttons">
            <a href="adicionar_usuario.php" class="btn">Adicionar Usuário</a>
            <a href="cadastrar_setor.php" class="btn">Cadastrar Setor e Gestor</a>
            <a href="dashboard.php" class="btn">Voltar para o Dashboard</a>
        </div>

        <!-- Tabela de resultados -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Sobrenome</th>
                    <th>E-mail</th>
                    <th>Cargo</th>
                    <th>Setor</th> <!-- Adicionado setor -->
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['nome'] . "</td>";
                        echo "<td>" . $row['sobrenome'] . "</td>";
                        echo "<td>" . $row['email_address'] . "</td>";
                        echo "<td>" . $row['cargo'] . "</td>";
                        echo "<td>" . $row['setor'] . "</td>"; // Exibindo setor
                        echo "<td><a href='editar_usuario.php?id=" . $row['id'] . "'><i class='fas fa-pencil-alt'></i></a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Nenhum usuário encontrado.</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <style>
        .btn {
            padding: 10px 20px;
            margin: 10px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .filter-form {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .filter-form div {
            display: flex;
            flex-direction: column;
        }
        .filter-form label {
            margin-bottom: 5px;
        }
        .filter-form input,
        .filter-form select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        /* Estilo do ícone de edição */
        td a i {
            color: #007BFF;
            font-size: 20px;
        }
        td a i:hover {
            color: #0056b3;
        }
    </style>
</body>
</html>
