<?php
include 'conexao.php';

// Inicializar variáveis de filtro
$nome_curso = $_GET['nome_curso'] ?? '';
$carga_horaria = $_GET['carga_horaria'] ?? '';
$area_do_curso = $_GET['area_do_curso'] ?? '';

// Obter lista de áreas do curso distintas do banco de dados
$areas_sql = "SELECT DISTINCT area_do_curso FROM cursos ORDER BY area_do_curso";
$areas_result = $conn->query($areas_sql);

// Preparar a consulta SQL com filtros
$sql = "SELECT id, nome_curso, carga_horaria, area_do_curso FROM cursos WHERE 1=1";

if (!empty($nome_curso)) {
    $sql .= " AND nome_curso LIKE '%" . $conn->real_escape_string($nome_curso) . "%'";
}

if (!empty($carga_horaria)) {
    $sql .= " AND carga_horaria = '" . $conn->real_escape_string($carga_horaria) . "'";
}

if (!empty($area_do_curso)) {
    $sql .= " AND area_do_curso = '" . $conn->real_escape_string($area_do_curso) . "'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Cursos</title>
    <link rel="stylesheet" href="lista_cursos.css"> <!-- Estilo CSS -->
    <!-- Link para o Font Awesome para usar os ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Lista de Cursos Cadastrados</h1>
        </header>

        <!-- Formulário de filtro -->
        <form method="GET" action="lista_cursos.php" class="filter-form">
            <div>
                <label for="nome_curso">Nome do Curso:</label>
                <input type="text" name="nome_curso" id="nome_curso" value="<?php echo htmlspecialchars($nome_curso); ?>">
            </div>
            <div>
                <label for="carga_horaria">Carga Horária em Horas:</label>
                <input type="text" name="carga_horaria" id="carga_horaria" value="<?php echo htmlspecialchars($carga_horaria); ?>">
            </div>
            <div>
                <label for="area_do_curso">Área do Curso:</label>
                <select name="area_do_curso" id="area_do_curso">
                    <option value="">Todas</option>
                    <?php
                    if ($areas_result && $areas_result->num_rows > 0) {
                        while ($row = $areas_result->fetch_assoc()) {
                            $selected = ($area_do_curso === $row['area_do_curso']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['area_do_curso']) . "' $selected>" . htmlspecialchars($row['area_do_curso']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn">Filtrar</button>
            <a href="lista_cursos.php" class="btn reset-btn">Limpar Filtros</a>
        </form>

        <!-- Botões de navegação -->
        <div class="buttons">
            <a href="cadastro_curso.php" class="btn">Cadastrar Novo Curso</a>
            <a href="dashboard.php" class="btn">Voltar para o Dashboard</a>
        </div>

        <!-- Tabela de cursos -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Curso</th>
                    <th>Carga Horária (Horas)</th>
                    <th>Área do Curso</th>
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['nome_curso'] . "</td>";
                        echo "<td>" . $row['carga_horaria'] . "</td>";
                        echo "<td>" . $row['area_do_curso'] . "</td>";
                        echo "<td><a href='editar_curso.php?id=" . $row['id'] . "'><i class='fas fa-pencil-alt'></i></a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nenhum curso encontrado.</td></tr>";
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
        .reset-btn {
            background-color: #f44336;
        }
        .reset-btn:hover {
            background-color: #e53935;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f1f1f1;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</body>
</html>
