<?php
include 'conexao.php';

// Verificar se o nome do setor foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nome_setor'])) {
    $nome_setor = $_POST['nome_setor'];
    $gestores = $_POST['gestores'] ?? []; // Pode estar vazio

    // Verificar se o setor foi criado e exibir uma mensagem
    echo "<!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Cadastrar Setor</title>
        <link rel='stylesheet' href='style.css'>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #f1f9fc; /* Fundo claro */
            }
            .response-container {
                background-color: #ffffff; /* Fundo branco para o quadro */
                border-radius: 8px;
                padding: 30px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 80%;
                max-width: 600px;
                text-align: center;
            }
            .success {
                color: #007bff; /* Azul para sucesso */
            }
            .error {
                color: #dc3545; /* Vermelho para erro */
            }
            .warning {
                color: #ffc107; /* Amarelo para warning */
            }
            h2 {
                font-size: 1.5em;
                margin-bottom: 20px;
                color: #003366; /* Azul escuro para os títulos */
            }
            p {
                font-size: 1.2em;
                color: #333; /* Texto em cinza escuro */
            }
            .btn-container {
                margin-top: 20px;
                display: flex;
                justify-content: center;
                gap: 15px;
            }
            .btn {
                padding: 10px 20px;
                background-color: #007bff; /* Azul claro para os botões */
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                transition: background-color 0.3s ease;
            }
            .btn:hover {
                background-color: #0056b3; /* Azul escuro para hover */
            }
        </style>
    </head>
    <body>";

    echo "<div class='response-container success'>";
    echo "<h2>Setor '$nome_setor' criado com sucesso!</h2>";

    // Atualizar os usuários selecionados como gestores
    if (!empty($gestores)) {
        foreach ($gestores as $gestor_nome) {
            if (!empty(trim($gestor_nome))) {
                // Separar o nome e sobrenome do gestor
                $nome_parts = explode(' ', $gestor_nome, 2);
                $nome = $nome_parts[0];
                $sobrenome = $nome_parts[1] ?? '';

                // Atualizar o campo setor e marcar como gestor na tabela de usuários
                $update_usuario_sql = "UPDATE usuarios SET setor = ?, gestor = 1 WHERE Nome = ? AND Sobrenome = ?";
                $stmt_usuario = $conn->prepare($update_usuario_sql);
                $stmt_usuario->bind_param("sss", $nome_setor, $nome, $sobrenome);

                if ($stmt_usuario->execute()) {
                    echo "<p>Usuário '$gestor_nome' atualizado como gestor do setor '$nome_setor'.</p>";
                } else {
                    echo "<p class='error'>Erro ao atualizar o usuário '$gestor_nome': " . $stmt_usuario->error . "</p>";
                }

                $stmt_usuario->close();
            }
        }
        echo "<p class='success'>Gestores atribuídos com sucesso!</p>";
    } else {
        echo "<p class='warning'>Nenhum gestor foi definido para o setor.</p>";
    }

    echo "<div class='btn-container'>
            <a href='lista.php' class='btn'>Voltar para a Lista</a>
            <a href='cadastrar_setor.php' class='btn'>Cadastrar Novo Setor</a>
        </div>";
    echo "</div>";
    $conn->close();
    
    echo "</body></html>";
} else {
    echo "<!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Cadastrar Setor</title>
        <link rel='stylesheet' href='style.css'>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #f1f9fc; /* Fundo claro */
            }
            .response-container {
                background-color: #ffffff; /* Fundo branco para o quadro */
                border-radius: 8px;
                padding: 30px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 80%;
                max-width: 600px;
                text-align: center;
            }
            .error {
                color: #dc3545; /* Vermelho para erro */
            }
            h2 {
                font-size: 1.5em;
                margin-bottom: 20px;
                color: #003366; /* Azul escuro para os títulos */
            }
            p {
                font-size: 1.2em;
                color: #333; /* Texto em cinza escuro */
            }
            .btn-container {
                margin-top: 20px;
                display: flex;
                justify-content: center;
                gap: 15px;
            }
            .btn {
                padding: 10px 20px;
                background-color: #007bff; /* Azul claro para os botões */
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                transition: background-color 0.3s ease;
            }
            .btn:hover {
                background-color: #0056b3; /* Azul escuro para hover */
            }
        </style>
    </head>
    <body>";

    echo "<div class='response-container error'>";
    echo "<h2>Erro!</h2>";
    echo "<p>Nome do setor não foi informado. Por favor, tente novamente.</p>";
    echo "<div class='btn-container'>
            <a href='lista.php' class='btn'>Voltar para a Lista</a>
            <a href='cadastrar_setor.php' class='btn'>Cadastrar Novo Setor</a>
        </div>";
    echo "</div>";
    
    echo "</body></html>";
}
?>
