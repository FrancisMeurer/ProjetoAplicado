<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_cargo'])) {
    header('Location: Login.php');
    exit();
}

$user_email = $_SESSION['user_email'];
$user_cargo = $_SESSION['user_cargo'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="Dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Barra lateral -->
        <div class="sidebar">
            <h2>Menu</h2>
            <a href="#" class="icon">
                <i class="fas fa-home"></i>
                <span>Início</span>
            </a>

            <!-- Exibir "Cadastros" apenas para cargos permitidos -->
            <?php if (in_array($user_cargo, ['T.i', 'Gerente', 'Supervisor'])): ?>
                <a href="lista.php" class="icon">
                    <i class="fas fa-user"></i>
                    <span>Cadastros</span>
                </a>
            <?php endif; ?>

            <a href="#" class="icon">
                <i class="fas fa-book"></i>
                <span>Cursos</span>
            </a>
            <a href="#" class="icon">
                <i class="fas fa-clock"></i>
                <span>Horários</span>
            </a>
            <a href="Logout.php" class="icon logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </div>

        <!-- Conteúdo principal -->
        <div class="main-content">
            <header>
                <h1>Bem-vindo ao Dashboard</h1>
                <div class="user-info">
                    <span>Usuário: <?php echo htmlspecialchars($user_email); ?></span> |
                    <span>Cargo: <?php echo htmlspecialchars($user_cargo); ?></span>
                </div>
            </header>

            <section class="updates">
                <h2>Atualizações Recentes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Gestor</th>
                            <th>Curso</th>
                            <th>Funcionário</th>
                            <th>Status do curso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>14/11/2024</td>
                            <td>21:37</td>
                            <td>Glaucio</td>
                            <td>Ponte rolante</td>
                            <td>Glauber Ferreira</td>
                            <td>Em andamento</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</body>
</html>
