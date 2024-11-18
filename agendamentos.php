<?php
include 'conexao.php';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gestor = $_POST['gestor'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_termino_prev = $_POST['data_termino_prev'] ?? '';
    $setor = $_POST['setor'] ?? '';
    $cargo = $_POST['cargo'] ?? '';
    $funcionario = $_POST['funcionario'] ?? '';
    $curso = $_POST['curso'] ?? '';
    $status = $_POST['status'] ?? '';

    // Validar se todos os campos foram preenchidos
    if ($gestor && $data_inicio && $data_termino_prev && $setor && $cargo && $funcionario && $curso && $status) {
        $sql = "INSERT INTO agendamentos (gestor, data_inicio, data_termino_prev, setor, cargo, funcionario, curso, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $gestor, $data_inicio, $data_termino_prev, $setor, $cargo, $funcionario, $curso, $status);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Agendamento cadastrado com sucesso!</p>";
        } else {
            echo "<p style='color: red;'>Erro ao cadastrar agendamento: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red;'>Por favor, preencha todos os campos.</p>";
    }
}

// Buscar todos os funcionários
$funcionarios_sql = "SELECT id, nome, sobrenome FROM usuarios";
$funcionarios_result = $conn->query($funcionarios_sql);
$funcionarios = [];
if ($funcionarios_result && $funcionarios_result->num_rows > 0) {
    while ($row = $funcionarios_result->fetch_assoc()) {
        $funcionarios[] = $row;
    }
}

// Buscar todos os cursos
$cursos_sql = "SELECT id, nome_curso FROM cursos";
$cursos_result = $conn->query($cursos_sql);
$cursos = [];
if ($cursos_result && $cursos_result->num_rows > 0) {
    while ($row = $cursos_result->fetch_assoc()) {
        $cursos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Agendamento</title>
    <link rel="stylesheet" href="agendamentos.css"> <!-- Estilo CSS -->
</head>
<body>
    <div class="container">
        <header>
            <h1>Cadastrar Agendamento</h1>
        </header>

        <!-- Formulário de Cadastro -->
        <form method="POST">
            <!-- Gestor -->
            <label for="gestor">Gestor:</label>
            <select id="gestor" name="gestor" required>
                <option value="">Selecione o Gestor</option>
                <?php
                // Obter lista de gestores (usuarios com gestor = 1)
                $gestores_sql = "SELECT id, nome, sobrenome FROM usuarios WHERE gestor = 1";
                $gestores_result = $conn->query($gestores_sql);
                if ($gestores_result && $gestores_result->num_rows > 0) {
                    while ($row = $gestores_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['nome'] . " " . $row['sobrenome'] . "</option>";
                    }
                }
                ?>
            </select>

            <!-- Data de Início -->
            <label for="data_inicio">Data de Início:</label>
            <input type="date" id="data_inicio" name="data_inicio" required>

            <!-- Data de Término Previsto -->
            <label for="data_termino_prev">Data de Término Previsto:</label>
            <input type="date" id="data_termino_prev" name="data_termino_prev" required>

            <!-- Setor -->
            <label for="setor">Setor:</label>
            <select id="setor" name="setor" required>
                <option value="">Selecione o Setor</option>
                <?php
                // Obter lista de setores
                $setores_sql = "SELECT DISTINCT setor FROM usuarios ORDER BY setor";
                $setores_result = $conn->query($setores_sql);
                if ($setores_result && $setores_result->num_rows > 0) {
                    while ($row = $setores_result->fetch_assoc()) {
                        echo "<option value='" . $row['setor'] . "'>" . $row['setor'] . "</option>";
                    }
                }
                ?>
            </select>

            <!-- Cargo -->
            <label for="cargo">Cargo:</label>
            <select id="cargo" name="cargo" required>
                <option value="">Selecione o Cargo</option>
                <?php
                // Obter lista de cargos
                $cargos_sql = "SELECT DISTINCT cargo FROM usuarios ORDER BY cargo";
                $cargos_result = $conn->query($cargos_sql);
                if ($cargos_result && $cargos_result->num_rows > 0) {
                    while ($row = $cargos_result->fetch_assoc()) {
                        echo "<option value='" . $row['cargo'] . "'>" . $row['cargo'] . "</option>";
                    }
                }
                ?>
            </select>

            <!-- Funcionário -->
            <label for="funcionario">Funcionário:</label>
            <select id="funcionario" name="funcionario" required>
                <option value="">Selecione o Funcionário</option>
                <?php
                // Exibir todos os funcionários
                foreach ($funcionarios as $funcionario) {
                    echo "<option value='" . $funcionario['id'] . "'>" . $funcionario['nome'] . " " . $funcionario['sobrenome'] . "</option>";
                }
                ?>
            </select>

            <!-- Curso -->
            <label for="curso">Curso:</label>
            <select id="curso" name="curso" required>
                <option value="">Selecione o Curso</option>
                <?php
                // Exibir todos os cursos
                foreach ($cursos as $curso) {
                    echo "<option value='" . $curso['id'] . "'>" . $curso['nome_curso'] . "</option>";
                }
                ?>
            </select>

            <!-- Status -->
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="">Selecione o Status</option>
                <option value="Em andamento">Em andamento</option>
                <option value="Finalizado">Finalizado</option>
                <option value="Cancelado">Cancelado</option>
                <option value="Pendente">Pendente</option> <!-- Nova opção Pendente -->
            </select>

            <div class="buttons">
                <button type="submit" class="add">Cadastrar</button>
                <a href="lista_agendamentos.php" class="back">Voltar</a>
            </div>
        </form>
    </div>
</body>
</html>
