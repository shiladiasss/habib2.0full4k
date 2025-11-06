<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");

// Página para onde o usuário será redirecionado
$volta_para = $_GET['volta'] ?? 'inicial.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1) Limpa o CPF
    $cpf_limpo = preg_replace('/\D/', '', $_POST['cpf']);

    // 2) Busca usuário no banco
    $sql = "SELECT * FROM cadastro WHERE cpf = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cpf_limpo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3) Verifica senha
    if ($user && password_verify($_POST['senha'], $user['senha'])) {
        $_SESSION['cpf']  = $user['cpf'];
        $_SESSION['nome'] = $user['nome'];
        header("Location: $volta_para");
        exit;
    } else {
        $erro = "CPF ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login Habib</title>
    <link rel="icon" type="image/png" href="imagens/logo.png">
    <style>
        body{background:#f5f5f5;font-family:Arial;text-align:center;padding:60px;}
        .box{max-width:380px;margin:auto;background:#fff;padding:40px;border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);}
        h2{margin-bottom:20px;color:#8B4513;}
        input{width:100%;padding:14px;margin:12px 0;border:1px solid #ccc;border-radius:8px;font-size:16px;}
        button{width:100%;padding:14px;background:#8B4513;color:#fff;border:none;border-radius:8px;font-size:18px;cursor:pointer;}
        button:hover{background:#723a0f;}
        .erro{color:red;margin:10px 0;font-weight:bold;}
        .voltar{margin-top:20px;}
    </style>
</head>
<body>

<div class="box">
    <h2>☕ BEM-VINDO DE VOLTA</h2>

    <?php if(isset($erro)) echo "<div class='erro'>$erro</div>"; ?>

    <form method="post">
        <input type="text" name="cpf" placeholder="CPF (com ou sem pontos)" required>
        <input type="password" name="senha" placeholder="Sua senha" required>

        <p><a href="cadastro.php">Ainda não tem conta? Cadastre-se</a></p>
        <p><a href="inicial.php">← Voltar</a></p>

        <button type="submit">ENTRAR</button>
    </form>
</div>

</body>
</html>
