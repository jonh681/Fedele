
        <?php
        if (isset($_GET['id'])) {
            $id = $_GET['id'];  // ID del título que queremos recuperar

            // Aquí deberías buscar el título en la base de datos o en un archivo
            // Ejemplo con un archivo simple
            $filename = 'titulos.txt';  // Archivo donde guardamos los títulos
            $fileContent = file_get_contents($filename);

            // Buscar el título por ID (esto es solo un ejemplo, deberías adaptarlo a tu base de datos)
            preg_match("/ID: $id, Título: (.+)/", $fileContent, $matches);

            if (isset($matches[1])) {
                echo $matches[1];  // Enviar el título encontrado al cliente
            } else {
                echo 'Sin título';  // Si no se encuentra, se devuelve un valor por defecto
            }
        }
        ?>
    