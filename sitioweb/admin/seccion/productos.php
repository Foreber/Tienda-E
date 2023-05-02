<?php include("../template/head.php"); ?>
<?php

$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "";
$txtImagen = (isset($_FILES['txtImagen']['name'])) ? $_FILES['txtImagen']['name'] : "";
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";


include("../configuracion/bd.php");

switch ($accion) {
    case "Agregar":

        $sentenciasSQL = $conexion->prepare("INSERT INTO electrodomesticos (nombre, imagen) VALUES (:nombre,:imagen);");
        $sentenciasSQL->bindParam(':nombre', $txtNombre);

        $fecha = new DateTime();
        $nombreArchivo = ($txtImagen != "") ? $fecha->getTimestamp() . "_" . $_FILES["txtImagen"]["name"] : "imagen.jpg";

        $tmpImagen = $_FILES["txtImagen"]["tmp_name"];

        if ($tmpImagen != "") {
            move_uploaded_file($tmpImagen, "../../img/" . $nombreArchivo);
        }



        $sentenciasSQL->bindParam(':imagen', $nombreArchivo);
        $sentenciasSQL->execute();

        header("Location:productos.php");

        break;

    case "Modificar":
        $sentenciasSQL = $conexion->prepare("UPDATE electrodomesticos SET nombre=:nombre WHERE id=:id");
        $sentenciasSQL->bindParam(':nombre', $txtNombre);
        $sentenciasSQL->bindParam(':id', $txtID);
        $sentenciasSQL->execute();

        if ($txtImagen != "") {

            $fecha = new DateTime();
            $nombreArchivo = ($txtImagen != "") ? $fecha->getTimestamp() . "_" . $_FILES["txtImagen"]["name"] : "imagen.jpg";
            $tmpImagen = $_FILES["txtImagen"]["tmp_name"];

            move_uploaded_file($tmpImagen, "../../img/" . $nombreArchivo);

            $sentenciasSQL = $conexion->prepare("SELECT imagen FROM electrodomesticos WHERE id=:id");
            $sentenciasSQL->bindParam(':id', $txtID);
            $sentenciasSQL->execute();
            $electrodomesticos = $sentenciasSQL->fetch(PDO::FETCH_LAZY);

            if (isset($electrodomesticos["imagen"]) && ($electrodomesticos["imagen"] != "imagen.jpg")) {

                if (file_exists("../../img/" . $electrodomesticos["imagen"])) {

                    unlink("../../img/" . $electrodomesticos["imagen"]);
                }
            }




            $sentenciasSQL = $conexion->prepare("UPDATE electrodomesticos SET imagen=:imagen WHERE id=:id");
            $sentenciasSQL->bindParam(':imagen', $nombreArchivo);
            $sentenciasSQL->bindParam(':id', $txtID);
            $sentenciasSQL->execute();
        }

        header("Location:productos.php");
        break;

    case "Cancelar":
        header("Location:productos.php");
        break;

    case "Seleccionar":
        $sentenciasSQL = $conexion->prepare("SELECT * FROM electrodomesticos WHERE id=:id");
        $sentenciasSQL->bindParam(':id', $txtID);
        $sentenciasSQL->execute();
        $electrodomesticos = $sentenciasSQL->fetch(PDO::FETCH_LAZY);

        $txtNombre = $electrodomesticos['nombre'];
        $txtImagen = $electrodomesticos['imagen'];



        //echo "Presionado boton Seleccionar";
        break;
    case "Borrar":

        $sentenciasSQL = $conexion->prepare("SELECT imagen FROM electrodomesticos WHERE id=:id");
        $sentenciasSQL->bindParam(':id', $txtID);
        $sentenciasSQL->execute();
        $electrodomesticos = $sentenciasSQL->fetch(PDO::FETCH_LAZY);

        if (isset($electrodomesticos["imagen"]) && ($electrodomesticos["imagen"] != "imagen.jpg")) {

            if (file_exists("../../img/" . $electrodomesticos["imagen"])) {

                unlink("../../img/" . $electrodomesticos["imagen"]);
            }
        }


        $sentenciasSQL = $conexion->prepare("DELETE FROM electrodomesticos WHERE id=:id");
        $sentenciasSQL->bindParam(':id', $txtID);
        $sentenciasSQL->execute();

        header("Location:productos.php");

        break;
}

$sentenciasSQL = $conexion->prepare("SELECT * FROM electrodomesticos");
$sentenciasSQL->execute();
$listaElectrodomesticos = $sentenciasSQL->fetchAll(PDO::FETCH_ASSOC);


?>

<div class="col-md-5">


    <div class="card">

        <div class="card-header">
            Datos de electrodomesticos
        </div>

        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="txtID">ID</label>
                    <input type="text" required readonly class="form-control" value="<?php echo $txtID ?>" name="txtID" id="txtID" placeholder="ID">
                </div>

                <div class="form-group">
                    <label for="txtNombre">Nombre</label>
                    <input type="text" required class="form-control" value="<?php echo $txtNombre ?>" name="txtNombre" id="txtNombre" placeholder="Nombre del electrodomestico">
                </div>

                <div class="form-group">
                    <label for="txtImagen">Imagen:</label>

                    <br>

                    <?php
                    if ($txtImagen != "") { ?>

                        <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen; ?>" width="50" alt="">

                    <?php   } ?>

                    <input type="file" class="form-control" name="txtImagen" id="txtImagen" placeholder="ID">
                </div>

                <div class="btn-group" role="group" aria-label="">
                    <button type="submit" name="accion" <?php echo ($accion == "Seleccionar") ? "disabled" : ""; ?> value="Agregar" class="btn btn-success">Agregar</button>
                    <button type="submit" name="accion" <?php echo ($accion != "Seleccionar") ? "disabled" : ""; ?> value="Modificar" class="btn btn-warning">Modificar</button>
                    <button type="submit" name="accion" <?php echo ($accion != "Seleccionar") ? "disabled" : ""; ?> value="Cancelar" class="btn btn-info">Cancelar</button>
                </div>

            </form>

        </div>


    </div>





</div>
<div class="col-md-7">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>

        </thead>
        <tbody>
            <?php foreach ($listaElectrodomesticos as $electrodomesticos) { ?>

                <tr>
                    <td><?php echo $electrodomesticos['id']; ?></td>
                    <td><?php echo $electrodomesticos['nombre']; ?></td>
                    <td>

                        <img class="img-thumbnail rounded" src="../../img/<?php echo $electrodomesticos['imagen']; ?>" width="50" alt="">


                    </td>



                    <td>
                        <form method="post">
                            <input type="hidden" name="txtID" id="txtID" value="<?php echo $electrodomesticos['id']; ?>" />

                            <input type="submit" name="accion" value="Seleccionar" class="btn btn-secondary" />

                            <input type="submit" name="accion" value="Borrar" class="btn btn-danger" />

                        </form>

                    </td>

                </tr>
            <?php } ?>

        </tbody>
    </table>

</div>


<?php include("../template/footer.php") ?>