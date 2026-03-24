<?php
require 'config.php';
if(!estaLogueado()) { header("Location: login.php"); exit(); }

$winner = null;
if(isset($_POST['spin'])) {
    $res = $conexion->query("SELECT * FROM boletos WHERE estado='vendido' ORDER BY RAND() LIMIT 1");
    $winner = $res->fetch_assoc();
}
?>
<body style="text-align:center; background:#0f172a; color:white; padding:50px;">
    <h1>SORTEO EN VIVO</h1>
    <form method="POST">
        <button type="submit" name="spin" style="padding:20px; background:gold; font-size:2rem; cursor:pointer;">¡GIRAR TÓMBOLA!</button>
    </form>
    
    <?php if($winner): ?>
        <div style="border:5px solid gold; margin-top:30px; padding:40px;">
            <h2 style="font-size:5rem;">#<?=$winner['id']?></h2>
            <h3>GANADOR: <?=$winner['nombre_comprador']?></h3>
        </div>
    <?php endif; ?>
</body>