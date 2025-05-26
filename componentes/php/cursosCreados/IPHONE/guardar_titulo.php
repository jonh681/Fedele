
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];  // ID del título
        $titulo = $_POST['titulo'];  // Nuevo título

        $filename = 'titulos.txt';  // Archivo donde guardar los títulos

        // Leer el archivo y obtener todo su contenido
        $fileContent = file_get_contents($filename);

        // Buscar si el ID ya existe en el archivo
        $pattern = "/ID: $id, Título: (.+)/";
        if (preg_match($pattern, $fileContent, $matches)) {
            // Si el ID ya existe, reemplazar el título con el nuevo valor
            $newData = preg_replace($pattern, "ID: $id, Título: $titulo", $fileContent);
        } else {
            // Si el ID no existe, agregar la nueva entrada
            $newData = $fileContent . "ID: $id, Título: $titulo\n";
        }

        // Guardar el archivo con los nuevos datos (reemplazando o agregando)
        if (file_put_contents($filename, $newData) !== false) {
            echo 'Título guardado correctamente';
        } else {
            http_response_code(500);
            echo "Error al guardar el título.";
        }
    }
    ?>
    