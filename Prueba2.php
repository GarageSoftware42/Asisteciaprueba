<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registro de Asistencia</title>
    <link rel="stylesheet" href="esi.css">
</head>
<body>
<header>Registro de Asistencia</header>

<?php
session_start();

if (isset($_SESSION['mensaje'])) {
    echo "<p>" . $_SESSION['mensaje'] . "</p>";
    unset($_SESSION['mensaje']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['asistencia'])) {
    $idHorario = $_POST['idHorario'];
    $asistencia = $_POST['asistencia'];

    $conexion = new mysqli('localhost', 'root', '', 'inciosesiondb');

    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    $stmt = $conexion->prepare("UPDATE horario SET Asistencia = ? WHERE ID_Horario = ?");
    $stmt->bind_param("si", $asistencia, $idHorario);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Asistencia actualizada correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar la asistencia: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<table>
    <thead>
        <tr>
            <th>Foto</th>
            <th>Nombre Completo</th>
            <th>Fecha</th>
            <th>Asistencia</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $conexion = new mysqli('localhost', 'root', '', 'inciosesiondb');

        if ($conexion->connect_error) {
            die("Conexión fallida: " . $conexion->connect_error);
        }

        $sql = "SELECT h.ID_Horario, h.ID_Usuario, u.N_completo, u.foto, h.Dia, h.Asistencia
                FROM horario h
                JOIN usuarios u ON h.ID_Usuario = u.ID_Usuario";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            while($fila = $resultado->fetch_assoc()) {
                $claseAsistencia = '';
                switch($fila['Asistencia']) {
                    case 'vino':
                        $claseAsistencia = 'label-vino';
                        break;
                    case 'no':
                        $claseAsistencia = 'label-no';
                        break;
                    case 'entró tarde':
                        $claseAsistencia = 'label-tarde';
                        break;
                }

                $foto = !empty($fila['foto']) ? '<img src="' . htmlspecialchars($fila['foto']) . '" alt="Foto de ' . htmlspecialchars($fila['N_completo']) . '" class="profile-pic" onclick="document.getElementById(\'file_' . $fila['ID_Usuario'] . '\').click()">' : 'Sin foto';

                echo "<tr>
                        <td>
                            <div class='foto-container'>
                                {$foto}
                                <span class='hover-text'>Actualizar</span>
                                <form method='POST' action='upload.php' enctype='multipart/form-data' class='foto-form'>
                                    <input type='hidden' name='idUsuario' value='{$fila['ID_Usuario']}'>
                                    <input type='file' name='foto' class='file-input' id='file_{$fila['ID_Usuario']}' accept='image/*' onchange='this.form.submit()'>
                                </form>
                            </div>
                        </td>
                        <td>{$fila['N_completo']}</td>
                        <td>{$fila['Dia']}</td>
                        <td class='{$claseAsistencia}'>" . ucfirst($fila['Asistencia']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No se encontraron registros de asistencia.</td></tr>";
        }

        $conexion->close();
        ?>
    </tbody>
</table>

</body>
</html>
