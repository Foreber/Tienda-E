<?php include("template/head.php") ?>

<?php
include("admin/configuracion/bd.php");
$sentenciasSQL = $conexion->prepare("SELECT * FROM electrodomesticos");
$sentenciasSQL->execute();
$listaElectrodomesticos = $sentenciasSQL->fetchAll(PDO::FETCH_ASSOC);
?>


<?php 

foreach($listaElectrodomesticos as $electrodomesticos){ ?>

<div class="col-md-3">
    <div class="card">
        <img class="card-img-top" src="./img/<?php echo $electrodomesticos['imagen']; ?>" width="200" height="280" alt="">
        <div class="card-body">
            <h4 class="card-title"><?php echo $electrodomesticos['nombre']; ?></h4>
            <a name="" id="" class="btn btn-primary" href="#" role="button">Ver mas</a>
        </div>
    </div>
</div>
<?php } ?>


<?php include("template/footer.php") ?>