<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=habib;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ===== CARREGA PRODUTOS =====
$produtos = $pdo->query("SELECT * FROM produtos LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);

// ===== DADOS DA EMPRESA =====
$empresa = $pdo->query("SELECT * FROM empresa LIMIT 1")->fetch();

// ===== USUÁRIO LOGADO =====
$logado = isset($_SESSION['cpf']);
$nome   = $logado ? $_SESSION['nome'] : 'Visitante';

// ===== QUANTIDADE NO CARRINHO =====
$carrinho_qtd = 0;
if ($logado) {
    $stmt = $pdo->prepare("SELECT SUM(quantidade) FROM carrinho WHERE nome_cliente = ?");
    $stmt->execute([$_SESSION['nome']]);
    $carrinho_qtd = $stmt->fetchColumn() ?: 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habib Special Coffee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="imagens/logo.png">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
        body{background:#f5f5f5;color:#333;}
        header{background:#8B4513;color:#fff;padding:15px 20px;}
        .topbar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;}
        .logo img{height:60px;}
        .search{flex:1;max-width:400px;position:relative;}
        .search input{width:100%;padding:12px 45px 12px 15px;border:none;border-radius:25px;font-size:16px;}
        .search button{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:#666;font-size:20px;cursor:pointer;}
        .user, .cart a{color:#fff;text-decoration:none;}
        .cart{position:relative;}
        .cart span{position:absolute;top:-10px;right:-10px;background:#ff4444;color:#fff;width:20px;height:20px;border-radius:50%;font-size:12px;display:flex;align-items:center;justify-content:center;}
        nav{background:#fff;text-align:center;padding:15px;box-shadow:0 2px 5px rgba(0,0,0,.1);}
        nav a{margin:0 30px;color:#333;text-decoration:none;font-weight:bold;font-size:18px;position:relative;}
        nav a::after{content:'';position:absolute;bottom:-8px;left:0;width:0;height:3px;background:#8B4513;transition:.3s;}
        nav a:hover::after{width:100%;}
        .banner{margin:20px auto;max-width:1200px;overflow:hidden;border-radius:15px;}
        .banner img{width:100%;display:block;}
        .destaques{display:flex;justify-content:center;flex-wrap:wrap;gap:30px;margin:40px auto;max-width:1200px;padding:0 20px;}
        .box{text-align:center;max-width:220px;}
        .box .icon{background:#8B4513;color:#fff;width:70px;height:70px;line-height:70px;border-radius:50%;margin:0 auto 15px;font-size:32px;}
        .produtos{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:25px;max-width:1200px;margin:40px auto;padding:0 20px;}
        .card{background:#fff;border-radius:15px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,.1);text-align:center;padding:15px;transition:.3s;}
        .card:hover{transform:translateY(-5px);}
        .card img{width:100%;height:200px;object-fit:cover;}
        .card h3{margin:15px 0 8px;font-size:18px;}
        .preco{color:#8B4513;font-size:22px;font-weight:bold;}
        .btn-add{display:inline-block;margin-top:10px;padding:10px 20px;background:#8B4513;color:#fff;border-radius:25px;text-decoration:none;font-size:14px;}
        footer{background:#8B4513;color:#fff;padding:40px 20px;}
        .footer-content{max-width:1200px;margin:auto;display:flex;flex-wrap:wrap;gap:40px;align-items:flex-start;}
        .social a{color:#fff;font-size:24px;margin-right:15px;}
        @media(max-width:768px){.topbar{flex-direction:column;} nav a{margin:0 15px;font-size:16px;}}
    </style>
</head>
<body>

<header>
    
    <div class="topbar">
        <div class="logo"><a href="inicial.php"><img src="imagens/logo.png" alt="Habib"></a></div>
        <div class="search">
            <form><input type="text" placeholder="Buscar cafés..."><button><i class="fas fa-search"></i></button></form>
        </div>
        <!-- ======= BOTÃO INTELIGENTE: ENTRE OU CADASTRE-SE ======= -->
<div class="user">
    <i class="far fa-user"></i> Olá, <strong><?= $nome ?></strong><br>
    <?php if($logado): ?>
        <a href="logout.php">Sair</a>
    <?php else: ?>
        <a href="#" id="btn-abre-modal" style="color:#fff;text-decoration:underline;">
            Entre ou cadastre-se
        </a>
    <?php endif; ?>
</div>
<div class="cart">
    <a href="carrinho.php">
        <i class="fas fa-shopping-bag"></i>
        <?php if($carrinho_qtd>0): ?><span><?= $carrinho_qtd ?></span><?php endif; ?>
    </a>
</div>

<!-- ======= MODAL 100% CORRIGIDO - VAI PRO CADASTRO! ======= -->
<?php if(!$logado): ?>
<div id="modal-login" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.8);z-index:9999;">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;width:90%;max-width:450px;border-radius:20px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.6);">
        
        <!-- HEADER MARROM -->
        <div style="background:#8B4513;color:#fff;padding:25px;text-align:center;">
            <i class="fas fa-coffee"></i> BEM-VINDO À HABIB COFFEE
        </div>
        
        <!-- CONTEÚDO -->
        <div style="padding:40px;text-align:center;">
            <p style="font-size:18px;color:#555;margin-bottom:30px;">
                Escolha uma opção:
            </p>
            
            <a href="login.php?volta=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
               style="display:block;margin:15px 0;padding:18px;background:#8B4513;color:#fff;border-radius:12px;text-decoration:none;font-size:18px;font-weight:bold;">
                <i class="fas fa-sign-in-alt"></i> JÁ TENHO CONTA
            </a>
            
                        <!-- BOTÃO QUE FUNCIONA 100% -->
                        <a href="cadastro.php?volta=inicial.php" 
               style="display:block;margin:18px 0;padding:20px;background:#28a745;color:#fff;border-radius:14px;text-decoration:none;font-size:19px;font-weight:bold;">
               <i class="fas fa-user-plus"></i> CRIAR CONTA GRÁTIS
            </a>
            
            <!-- FECHAR -->
            <button onclick="document.getElementById('modal-login').style.display='none'" 
                    style="margin-top:25px;background:none;border:none;color:#999;font-size:30px;cursor:pointer;width:100%;">
                ✕ FECHAR
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('btn-abre-modal').onclick = function(e) {
    e.preventDefault();
    document.getElementById('modal-login').style.display = 'block';
}
</script>
<?php endif; ?>
        
</header>

<nav>
    <a href="#">CAFÉ</a>
    <a href="#">ACESSÓRIOS</a>
    <a href="#">PROMOÇÕES</a>
</nav>

<div class="banner">
    <img src="imagens/inicio.png" alt="Café Brasil">
</div>

<div class="destaques">
    <div class="box"><div class="icon"><i class="fas fa-headset"></i></div><h3>Fale com especialistas<br>e tire suas dúvidas</h3></div>
    <div class="box"><div class="icon"><i class="fas fa-medal"></i></div><h3>Mais de 16 anos<br>de experiência</h3></div>
    <div class="box"><div class="icon"><i class="fas fa-truck"></i></div><h3>Entrega rápida e segura<br>em todo o Brasil</h3></div>
    <div class="box"><div class="icon"><i class="fas fa-credit-card"></i></div><h3>Pix com desconto<br>ou até 6x sem juros</h3></div>
</div>

<div class="produtos">
    <?php foreach($produtos as $p): ?>
    <div class="card">
        <img src="<?= $p['imagem'] ?>" alt="<?= $p['nome'] ?>">
        <h3><?= $p['nome'] ?></h3>
        <div class="preco">R$ <?= number_format($p['preco'],2,',','.') ?><sup>*cada</sup></div>
        <a href="adicionar.php?id=<?= $p['id'] ?>" class="btn-add">Adicionar</a>
    </div>
    <?php endforeach; ?>
</div>

<footer>
    <div class="footer-content">
        <img src="imagens/logo.png" style="height:100px;" alt="Habib">
        <div><strong>Loja</strong><br><a href="#" style="color:#fff;">Café</a><br><a href="#" style="color:#fff;">Acessórios</a><br><a href="#" style="color:#fff;">Promoções</a></div>
        <div>
            <strong>Atendimento</strong><br><?= nl2br($empresa['telefone'] ?? '(45) 99933-7261') ?><br>
            <strong>Endereço:</strong><br><?= nl2br($empresa['endereco'] ?? 'Rua Curitiba, 1417 – Neva\nCascavel – PR') ?>
        </div>
        <div class="social">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-whatsapp"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </div>
</footer>

</body>
</html>
