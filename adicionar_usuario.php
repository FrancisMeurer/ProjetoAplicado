<?php
include 'conexao.php';

// Variáveis para manter os valores preenchidos pelo usuário
$nome = $sobrenome = $email_address = $cargo = $telefone = $sexo = $data_nascimento = $observacao = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coleta os dados do formulário
    $nome = $_POST['nome'] ?? '';
    $sobrenome = $_POST['sobrenome'] ?? '';
    $email_address = $_POST['email_address'] ?? ''; 
    $cargo = $_POST['cargo'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? '';
    $observacao = $_POST['observacao'] ?? ''; // Coletando observação

    // Verifica se o campo "email_address" está vazio ou ausente
    if (empty($email_address)) {
        echo "O campo E-mail é obrigatório!<br>";
        // Exibe o formulário novamente com os dados preenchidos
        exibirFormulario($nome, $sobrenome, $email_address, $cargo, $telefone, $sexo, $data_nascimento, $observacao);
        exit;
    }

    // Verifica se já existe um usuário com o mesmo nome
    $sql_check = "SELECT id FROM usuarios WHERE nome = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $nome);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Exibe o formulário novamente e ativa o popup
        echo "<script>alert('Erro: Já existe um usuário cadastrado com o nome \"$nome\".');</script>";
        exibirFormulario($nome, $sobrenome, $email_address, $cargo, $telefone, $sexo, $data_nascimento, $observacao);
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // Converte a data do formato brasileiro para o formato do banco de dados (AAAA-MM-DD)
    $data_formatada = DateTime::createFromFormat('d/m/Y', $data_nascimento);

    if (!$data_formatada) {
        echo "A data fornecida está no formato errado. Por favor, use o formato DD/MM/AAAA.<br>";
        exibirFormulario($nome, $sobrenome, $email_address, $cargo, $telefone, $sexo, $data_nascimento, $observacao);
        exit;
    }

    $data_formatada = $data_formatada->format('Y-m-d');

    // Função para gerar senha aleatória
    function gerarSenha($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }

    // Gera a senha aleatória
    $senha = gerarSenha();

    // Criptografa a senha utilizando PASSWORD_BCRYPT
    $senha_criptografada = password_hash($senha, PASSWORD_BCRYPT);

    // Insere os dados no banco de dados
    $sql = "INSERT INTO usuarios (nome, sobrenome, email_address, cargo, telefone, sexo, data_nascimento, senha) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $nome, $sobrenome, $email_address, $cargo, $telefone, $sexo, $data_formatada, $senha_criptografada);

    if ($stmt->execute()) {
        // Redireciona para a página sucesso.php e passa as informações via URL
        header("Location: sucesso.php?nome=$nome&email=$email_address&senha=$senha");
        exit;
    } else {
        echo "<script>alert('Erro ao cadastrar o usuário: " . $stmt->error . "');</script>";
    }
    

    $stmt->close();
    $conn->close();
} else {
    // Exibe o formulário se não houver envio
    exibirFormulario($nome, $sobrenome, $email_address, $cargo, $telefone, $sexo, $data_nascimento, $observacao);
}

// Função para exibir o formulário com dados preenchidos
function exibirFormulario($nome, $sobrenome, $email_address, $cargo, $telefone, $sexo, $data_nascimento, $observacao) {
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionários</title>
    <link rel="stylesheet" href="cadastro.css">
    <script>
        // Função para exibir o popup
        function exibirPopup(msg) {
            alert(msg);
        }
    </script>
</head>
<body>
    <div class="form-container">
        <header>
            <h1>Cadastro de Funcionários</h1>
        </header>
        <form method="POST" action="adicionar_usuario.php">
            <div class="left-panel">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required placeholder="Nome" value="<?php echo htmlspecialchars($nome); ?>">

                <label for="sobrenome">Sobrenome</label>
                <input type="text" id="sobrenome" name="sobrenome" required placeholder="Sobrenome" value="<?php echo htmlspecialchars($sobrenome); ?>">

                <label for="sexo">Sexo</label>
                <input type="text" id="sexo" name="sexo" required placeholder="Sexo" value="<?php echo htmlspecialchars($sexo); ?>">

                <label for="data_nascimento">Data de Nascimento</label>
                <input type="text" id="data_nascimento" name="data_nascimento" required placeholder="DD/MM/AAAA" value="<?php echo htmlspecialchars($data_nascimento); ?>">

                <label for="cargo">Cargo</label>
                <input type="text" id="cargo" name="cargo" required placeholder="Cargo" value="<?php echo htmlspecialchars($cargo); ?>">

                <label for="email_address">E-mail</label>
                <input type="email" id="email_address" name="email_address" required placeholder="E-mail" value="<?php echo htmlspecialchars($email_address); ?>">

                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" required placeholder="Telefone" value="<?php echo htmlspecialchars($telefone); ?>">
            </div>
            <div class="right-panel">
                <div class="photo-preview">
                    <img id="preview-img" src="" alt="Pré-visualização da Foto">
                </div>
                <button type="button" id="upload-button" onclick="document.getElementById('upload-photo').click()">Carregar Foto</button>
                <input type="file" id="upload-photo" accept="image/*" hidden>
            </div>

            <textarea placeholder="Observação" name="observacao"><?php echo htmlspecialchars($observacao); ?></textarea>

            <!-- Botões dentro do form -->
            <div class="button-group">
                <button class="save" type="submit">Salvar</button>
                <button class="delete" type="button" onclick="window.location.href='lista.php'">Voltar</button>
                <button class="exit" type="button" onclick="window.location.href='Dashboard.php'">Sair</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php
}
?>
