<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_cargo'])) {
    header('Location: Login.php');
    exit();
}

$user_email = $_SESSION['user_email'];
$user_cargo = $_SESSION['user_cargo'];

// Conexão com o banco de dados
include 'conexao.php';

// Verificar se o usuário é gestor
$is_gestor = false;
$gestor_check_query = "SELECT gestor FROM usuarios WHERE email_address = '$user_email'";
$gestor_check_result = $conn->query($gestor_check_query);

if ($gestor_check_result && $gestor_check_result->num_rows > 0) {
    $gestor_data = $gestor_check_result->fetch_assoc();
    $is_gestor = ($gestor_data['gestor'] == 1);
}

// Obter a lista de gestores
$gestores = [];
$gestor_query = "SELECT id, nome FROM usuarios WHERE gestor = 1";
$gestor_result = $conn->query($gestor_query);
if ($gestor_result->num_rows > 0) {
    while ($gestor_row = $gestor_result->fetch_assoc()) {
        $gestores[] = $gestor_row;
    }
}

// Verificar se um filtro foi aplicado
$filter_gestor_id = isset($_GET['filter_gestor']) ? $_GET['filter_gestor'] : '';

// Definir o número de linhas a exibir por página
$rows_per_page = isset($_GET['rows_per_page']) ? (int)$_GET['rows_per_page'] : 5;
$rows_per_page = in_array($rows_per_page, [5, 10, 15, 20, 50]) ? $rows_per_page : 5;

// Consulta para buscar as atualizações recentes com filtro e limite
$sql = "SELECT a.data_inicio, a.data_termino_real, a.gestor, a.curso, a.funcionario, a.status,
               CONCAT(u.nome, ' ', u.sobrenome) AS nome_gestor, f.nome AS nome_funcionario, c.nome_curso
        FROM agendamentos a
        LEFT JOIN usuarios u ON a.gestor = u.id
        LEFT JOIN usuarios f ON a.funcionario = f.id
        LEFT JOIN cursos c ON a.curso = c.id
        " . ($filter_gestor_id ? "WHERE a.gestor = '$filter_gestor_id'" : "") . "
        ORDER BY a.data_inicio DESC
        LIMIT $rows_per_page";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Dashboard.css">
    <script>
        // Detectar se é um dispositivo móvel
        function isMobile() {
            return /Android|iPhone|iPad|iPod|Windows Phone/i.test(navigator.userAgent);
        }

        // Adicionar classe ao body se for móvel
        window.onload = function() {
            if (isMobile()) {
                document.body.classList.add('mobile');
            }
        };
    </script>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Menu</h2>
            <a href="#" class="icon">
                <i class="fas fa-home"></i>
                <span>Início</span>
            </a>

            <?php if ($is_gestor): ?>
                <a href="lista.php" class="icon">
                    <i class="fas fa-user"></i>
                    <span>Cadastro de Usuários</span>
                </a>
                <a href="lista_cursos.php" class="icon">
                    <i class="fas fa-book"></i>
                    <span>Cadastro de Cursos</span>
                </a>
                <a href="lista_agendamentos.php" class="icon">
                    <i class="fas fa-clock"></i>
                    <span>Agendamentos</span>
                </a>
            <?php endif; ?>

            <a href="Logout.php" class="icon logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </div>

        <div class="main-content">
            <header>
                <h1>Bem-vindo ao Dashboard</h1>
                <div class="user-info">
                    <span>Usuário: <?php echo htmlspecialchars($user_email); ?></span> |
                    <span>Cargo: <?php echo htmlspecialchars($user_cargo); ?></span>
                </div>
            </header>

            <section class="updates">
                <div class="updates-header">
                    <h2>Atualizações Recentes</h2>
                    <form method="GET" action="" class="filter-form">
                        <select name="filter_gestor" class="filter-select">
                            <option value="">Selecione o Gestor</option>
                            <?php foreach ($gestores as $gestor): ?>
                                <option value="<?php echo $gestor['id']; ?>" <?php echo ($filter_gestor_id == $gestor['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($gestor['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Adicionar o seletor para o número de linhas -->
                        <select name="rows_per_page" class="filter-select">
                            <option value="5" <?php echo ($rows_per_page == 5) ? 'selected' : ''; ?>>5</option>
                            <option value="10" <?php echo ($rows_per_page == 10) ? 'selected' : ''; ?>>10</option>
                            <option value="15" <?php echo ($rows_per_page == 15) ? 'selected' : ''; ?>>15</option>
                            <option value="20" <?php echo ($rows_per_page == 20) ? 'selected' : ''; ?>>20</option>
                            <option value="50" <?php echo ($rows_per_page == 50) ? 'selected' : ''; ?>>50</option>
                        </select>

                        <button type="submit" class="btn">Filtrar</button>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Data Início</th>
                            <th>Data de Conclusão</th>
                            <th>Gestor</th>
                            <th>Curso</th>
                            <th>Funcionário</th>
                            <th>Status do curso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $data_inicio = date('d/m/Y', strtotime($row['data_inicio']));
                                $data_termino_real = $row['data_termino_real'] ? date('d/m/Y', strtotime($row['data_termino_real'])) : 'Em andamento';
                                if ($row['status'] != 'Finalizado') {
                                    $data_termino_real = 'Em andamento';
                                }

                                echo "<tr>";
                                echo "<td>" . $data_inicio . "</td>";
                                echo "<td>" . $data_termino_real . "</td>";
                                echo "<td>" . $row['nome_gestor'] . "</td>";
                                echo "<td>" . $row['nome_curso'] . "</td>";
                                echo "<td>" . $row['nome_funcionario'] . "</td>";
                                echo "<td>" . $row['status'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Nenhuma atualização recente.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</body>
</html>
