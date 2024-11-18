<?php
include 'conexao.php';

// Definir o fuso horário para evitar problemas de data
date_default_timezone_set('America/Sao_Paulo');

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    die("ID do agendamento não especificado.");
}

$id = $_GET['id'];

// Consultar os dados do agendamento
$sql = "SELECT a.id, a.gestor, a.data_inicio, a.data_termino_prev, 
               a.funcionario, a.curso, a.cargo, a.setor, a.status, 
               u.nome AS nome_gestor, f.nome AS nome_funcionario, c.nome_curso
        FROM agendamentos a
        LEFT JOIN usuarios u ON a.gestor = u.id
        LEFT JOIN usuarios f ON a.funcionario = f.id
        LEFT JOIN cursos c ON a.curso = c.id
        WHERE a.id = $id";

$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Agendamento não encontrado.");
}

$row = $result->fetch_assoc();

// Atualizar os dados do agendamento
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gestor = $_POST['gestor'];
    $funcionario = $_POST['funcionario'];
    $data_inicio = $_POST['data_inicio'];
    $data_termino_prev = $_POST['data_termino_prev'];
    $curso = $_POST['curso'];
    $cargo = $_POST['cargo'];
    $setor = $_POST['setor'];
    $status = $_POST['status'];

    // Montar a consulta de atualização
    $update_sql = "UPDATE agendamentos SET 
                        gestor = '$gestor', 
                        funcionario = '$funcionario', 
                        data_inicio = '$data_inicio', 
                        data_termino_prev = '$data_termino_prev', 
                        curso = '$curso', 
                        cargo = '$cargo', 
                        setor = '$setor', 
                        status = '$status'";

    // Se o status for "Finalizado", atualizar a data_termino_real com a data atual usando CURDATE()
    if ($status == 'Finalizado') {
        $update_sql .= ", data_termino_real = CURDATE()";
    }

    $update_sql .= " WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Agendamento atualizado com sucesso!'); window.location.href = 'lista_agendamentos.php';</script>";
    } else {
        echo "Erro ao atualizar o agendamento: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Agendamento</title>
    <link rel="stylesheet" href="editar_agendamento.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Editar Agendamento</h1>
        </header>

        <form method="POST" class="form-editar">
            <div class="form-group">
                <label for="gestor">Gestor:</label>
                <select name="gestor" id="gestor" required>
                    <option value="">Selecione</option>
                    <?php
                    $gestores_sql = "SELECT id, nome FROM usuarios WHERE gestor = 1";
                    $gestores_result = $conn->query($gestores_sql);
                    while ($gestor_row = $gestores_result->fetch_assoc()) {
                        $selected = ($gestor_row['id'] == $row['gestor']) ? 'selected' : '';
                        echo "<option value='" . $gestor_row['id'] . "' $selected>" . $gestor_row['nome'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="funcionario">Funcionário:</label>
                <select name="funcionario" id="funcionario" required>
                    <option value="">Selecione</option>
                    <?php
                    $funcionarios_sql = "SELECT id, nome FROM usuarios WHERE gestor = 0";
                    $funcionarios_result = $conn->query($funcionarios_sql);
                    while ($funcionario_row = $funcionarios_result->fetch_assoc()) {
                        $selected = ($funcionario_row['id'] == $row['funcionario']) ? 'selected' : '';
                        echo "<option value='" . $funcionario_row['id'] . "' $selected>" . $funcionario_row['nome'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="data_inicio">Data de Início:</label>
                <input type="date" name="data_inicio" id="data_inicio" value="<?php echo $row['data_inicio']; ?>" required>
            </div>

            <div class="form-group">
                <label for="data_termino_prev">Data de Término Previsto:</label>
                <input type="date" name="data_termino_prev" id="data_termino_prev" value="<?php echo $row['data_termino_prev']; ?>" required>
            </div>

            <div class="form-group">
                <label for="curso">Curso:</label>
                <select name="curso" id="curso" required>
                    <option value="">Selecione o Curso</option>
                    <?php
                    $cursos_sql = "SELECT id, nome_curso FROM cursos";
                    $cursos_result = $conn->query($cursos_sql);
                    while ($curso_row = $cursos_result->fetch_assoc()) {
                        $selected = ($curso_row['id'] == $row['curso']) ? 'selected' : '';
                        echo "<option value='" . $curso_row['id'] . "' $selected>" . htmlspecialchars($curso_row['nome_curso']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="cargo">Cargo:</label>
                <input type="text" name="cargo" id="cargo" value="<?php echo $row['cargo']; ?>" required>
            </div>

            <div class="form-group">
                <label for="setor">Setor:</label>
                <input type="text" name="setor" id="setor" value="<?php echo $row['setor']; ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="Em andamento" <?php echo ($row['status'] == 'Em andamento') ? 'selected' : ''; ?>>Em andamento</option>
                    <option value="Finalizado" <?php echo ($row['status'] == 'Finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                    <option value="Cancelado" <?php echo ($row['status'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                    <option value="Pendente" <?php echo ($row['status'] == 'Pendente') ? 'selected' : ''; ?>>Pendente</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-submit">Atualizar Agendamento</button>
                <a href="lista_agendamentos.php" class="btn-voltar">Voltar</a>
            </div>
        </form>
    </div>
</body>
</html>
