<?php
include 'conexao.php';
date_default_timezone_set('America/Sao_Paulo');


// Definir variáveis de filtro
$gestor = $_GET['gestor'] ?? '';
$funcionario = $_GET['funcionario'] ?? '';
$status = $_GET['status'] ?? '';
$data_inicio = $_GET['data_inicio'] ?? '';
$data_termino_prev = $_GET['data_termino_prev'] ?? '';

// Construir a consulta com filtros
$sql = "SELECT a.id, a.gestor, a.data_inicio, a.data_termino_prev, a.data_termino_real,
               a.funcionario, a.curso, a.cargo, a.setor, a.status, u.nome AS nome_gestor, f.nome AS nome_funcionario, c.nome_curso
        FROM agendamentos a
        LEFT JOIN usuarios u ON a.gestor = u.id
        LEFT JOIN usuarios f ON a.funcionario = f.id
        LEFT JOIN cursos c ON a.curso = c.id
        WHERE 1=1";

// Adicionar filtros à consulta
if ($gestor) {
    $sql .= " AND a.gestor = '$gestor'";
}
if ($funcionario) {
    $sql .= " AND a.funcionario = '$funcionario'";
}
if ($status) {
    $sql .= " AND a.status = '$status'";
}
if ($data_inicio) {
    $sql .= " AND a.data_inicio >= '$data_inicio'";
}
if ($data_termino_prev) {
    $sql .= " AND a.data_termino_prev <= '$data_termino_prev'";
}

$sql .= " ORDER BY a.data_inicio DESC"; // Ordenar pela data de início

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Agendamentos</title>
    <link rel="stylesheet" href="lista_agendamentos.css"> <!-- Link para o seu arquivo CSS -->
</head>
<body>
    <div class="container">
        <header>
            <h1>Lista de Agendamentos</h1>
        </header>

        <!-- Botões de Ação -->
        <div class="buttons">
            <!-- Botão para adicionar agendamento -->
            <a href="agendamentos.php" class="btn">Adicionar Agendamento</a>
        </div>

        <!-- Formulário de Filtros -->
        <form method="GET" class="filter-form">
            <div>
                <label for="gestor">Gestor:</label>
                <select name="gestor" id="gestor">
                    <option value="">Selecione</option>
                    <?php
                    // Carregar gestores para o filtro
                    $gestores_sql = "SELECT id, nome FROM usuarios WHERE gestor = 1";
                    $gestores_result = $conn->query($gestores_sql);
                    while ($row = $gestores_result->fetch_assoc()) {
                        $selected = ($row['id'] == $gestor) ? 'selected' : '';
                        echo "<option value='" . $row['id'] . "' $selected>" . $row['nome'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="funcionario">Funcionário:</label>
                <select name="funcionario" id="funcionario">
                    <option value="">Selecione</option>
                    <?php
                    // Carregar funcionários para o filtro
                    $funcionarios_sql = "SELECT id, nome FROM usuarios WHERE gestor = 0";
                    $funcionarios_result = $conn->query($funcionarios_sql);
                    while ($row = $funcionarios_result->fetch_assoc()) {
                        $selected = ($row['id'] == $funcionario) ? 'selected' : '';
                        echo "<option value='" . $row['id'] . "' $selected>" . $row['nome'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="status">Status:</label>
                <select name="status" id="status">
                    <option value="">Selecione</option>
                    <option value="Em andamento" <?php echo ($status == 'Em andamento') ? 'selected' : ''; ?>>Em andamento</option>
                    <option value="Finalizado" <?php echo ($status == 'Finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                    <option value="Cancelado" <?php echo ($status == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                    <option value="Pendente" <?php echo ($status == 'Pendente') ? 'selected' : ''; ?>>Pendente</option>
                </select>
            </div>

            <div>
                <label for="data_inicio">Data de Início:</label>
                <input type="date" name="data_inicio" id="data_inicio" value="<?php echo $data_inicio; ?>">
            </div>

            <div>
                <label for="data_termino_prev">Data de Término Previsto:</label>
                <input type="date" name="data_termino_prev" id="data_termino_prev" value="<?php echo $data_termino_prev; ?>">
            </div>

            <div class="buttons">
                <button type="submit" class="btn">Filtrar</button>
                <a href="lista_agendamentos.php" class="btn reset-btn">Limpar</a>
            </div>
        </form>

        <!-- Tabela de Agendamentos -->
        <table>
        <thead>
    <tr>
        <th>ID</th>
        <th>Gestor</th>
        <th>Data Início</th>
        <th>Data Término Previsto</th>
        <th>Data Término Real</th> <!-- Coluna Data Término Real adicionada -->
        <th>Funcionário</th>
        <th>Curso</th>
        <th>Cargo</th>
        <th>Setor</th>
        <th>Status</th>
        <th>Editar</th>
    </tr>
</thead>
<tbody>
    <?php
    if ($result->num_rows > 0) {
        // Exibir cada agendamento
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['nome_gestor'] . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($row['data_inicio'])) . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($row['data_termino_prev'])) . "</td>";
            echo "<td>" . ($row['data_termino_real'] ? date('d/m/Y', strtotime($row['data_termino_real'])) : 'Não Informada') . "</td>"; // Exibe a data real ou uma mensagem padrão
            echo "<td>" . $row['nome_funcionario'] . "</td>";
            echo "<td>" . $row['nome_curso'] . "</td>";
            echo "<td>" . $row['cargo'] . "</td>";
            echo "<td>" . $row['setor'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td><a href='editar_agendamento.php?id=" . $row['id'] . "' class='btn'>Editar</a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='11'>Nenhum agendamento encontrado.</td></tr>"; // Atualizado para 11 colunas
    }
    ?>
</tbody>
        </table>

        <div class="buttons">
            <a href="dashboard.php" class="btn">Voltar</a>
        </div>
    </div>
</body>
</html>
