<?php
if (isset($_POST["idUsuario"]) && isset($_FILES["foto"])) {
    $idUsuario = $_POST['idUsuario'];
    $target_dir = "uploads/";
    $target_file = $target_dir . "usuario_" . $idUsuario . ".jpg"; // Nombre único por usuario
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Comprobar si el archivo es una imagen
    if (isset($_FILES["foto"]["tmp_name"]) && $_FILES["foto"]["tmp_name"] != "") {
        $check = getimagesize($_FILES["foto"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "El archivo no es una imagen.";
            $uploadOk = 0;
        }
    } else {
        echo "No se ha recibido el archivo.";
        $uploadOk = 0;
    }

    // Limitar los tipos de archivos permitidos
    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
        echo "Solo se permiten archivos JPG, JPEG, y PNG.";
        $uploadOk = 0;
    }

    // Subir imagen
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            // Conectar a la base de datos
            $conexion = new mysqli('localhost', 'root', '', 'inciosesiondb');
            if ($conexion->connect_error) {
                die("Conexión fallida: " . $conexion->connect_error);
            }

            // Actualizar la ruta de la imagen en la base de datos
            $stmt = $conexion->prepare("UPDATE usuarios SET foto = ? WHERE ID_Usuario = ?");
            if (!$stmt) {
                die("Error en la preparación de la declaración: " . $conexion->error);
            }
            $stmt->bind_param("si", $target_file, $idUsuario);

            if ($stmt->execute()) {
                echo "La imagen ha sido subida y actualizada correctamente.";
            } else {
                echo "Hubo un error al actualizar la foto en la base de datos: " . $stmt->error;
            }

            $stmt->close();
            $conexion->close();
        } else {
            echo "Hubo un error al subir la imagen.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<link rel="stylesheet" href="esi.css">
<head>
    <meta http-equiv="refresh" content="2;url=Prueba2.php">
    <title>Subida de Imagen</title>
</head>
<body>
    <p>Redirigiendo de vuelta a la página principal...</p>
</body>
</html>
