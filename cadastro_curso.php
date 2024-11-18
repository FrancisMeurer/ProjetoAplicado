<?php
include 'conexao.php';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_curso = $_POST['nome_curso'] ?? '';
    $carga_horaria = $_POST['carga_horaria'] ?? '';
    $area_do_curso = $_POST['area_do_curso'] ?? '';

    // Validar se todos os campos foram preenchidos
    if ($nome_curso && $carga_horaria && $area_do_curso) {
        $sql = "INSERT INTO cursos (nome_curso, carga_horaria, area_do_curso) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome_curso, $carga_horaria, $area_do_curso);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Curso cadastrado com sucesso!</p>";
        } else {
            echo "<p style='color: red;'>Erro ao cadastrar o curso: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red;'>Por favor, preencha todos os campos.</p>";
    }
}

// Buscar setores da tabela usuarios
$setores_sql = "SELECT DISTINCT setor FROM usuarios ORDER BY setor";
$setores_result = $conn->query($setores_sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Curso</title>
    <link rel="stylesheet" href="cadastro_curso.css"> <!-- Estilo CSS -->
</head>
<body>
    <div class="container">
        <header>
            <h1>Cadastrar Novo Curso</h1>
        </header>

        <!-- Formulário de Cadastro -->
        <form method="POST">
            <label for="nome_curso">Nome do Curso em:</label>
            <input type="text" id="nome_curso" name="nome_curso" required>

            <label for="carga_horaria">Carga Horária em Horas:</label>
            <input type="text" id="carga_horaria" name="carga_horaria" required>

            <label for="area_do_curso">Área do Curso:</label>
            <select id="area_do_curso" name="area_do_curso" required>
                <option value="">Selecione a área</option>
                <?php
                // Verifica se há setores cadastrados
                if ($setores_result && $setores_result->num_rows > 0) {
                    while ($row = $setores_result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['setor']) . "'>" . htmlspecialchars($row['setor']) . "</option>";
                    }
                } else {
                    echo "<option value=''>Nenhum setor encontrado</option>";
                }
                ?>
            </select>

            <div class="buttons">
                <button type="submit" class="add">Cadastrar</button>
                <!-- Botão Voltar direcionando para o Dashboard -->
                <a href="lista_cursos.php" class="back">Voltar</a>
            </div>
        </form>
    </div>
</body>
</html>
