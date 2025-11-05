<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");

// Salva de onde veio
$origem = $_SERVER['HTTP_REFERER'] ?? 'index.php';
$id = $_GET['id'] ?? 0;

if (!isset($_SESSION['cpf'])) {
    // Manda pro login e guarda a origem
    header("Location: login.php?volta=" . urlencode($origem));
    exit;
}

// === USUÁRIO LOGADO: adiciona no carrinho ===
$produto = $pdo->prepare("SELECT nome, preco FROM produtos WHERE id = ?");
$produto->execute([$id]);
$p = $produto->fetch();

if ($p) {
    $sql = "INSERT INTO carrinho (cod_produto, data_compra, preco, nome_cliente, quantidade, total)
            VALUES (?, CURDATE(), ?, ?, 1, ?)
            ON DUPLICATE KEY UPDATE quantidade = quantidade + 1, total = preco * quantidade";
    $pdo->prepare($sql)->execute([$id, $p['preco'], $_SESSION['nome'], $p['preco']]);
}

header("Location: $origem");
exit;