<?php
requiereAutenticacion();
requierePermiso("bitacora", "consultar");



// Obtener lista de backups existentes
function getBackupFiles() {
    $backupDir = 'bd_backup/';
    $backups = [];
    
    if (is_dir($backupDir)) {
        $files = scandir($backupDir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $filepath = $backupDir . $file;
                $backups[] = [
                    'filename' => $file,
                    'filepath' => $filepath,
                    'filesize' => filesize($filepath),
                    'filemtime' => filemtime($filepath)
                ];
            }
        }
        
        // Ordenar por fecha más reciente primero
        usort($backups, function($a, $b) {
            return $b['filemtime'] - $a['filemtime'];
        });
    }
    
    return $backups;
}

// Función auxiliar para formatear tamaño de archivo
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        if (isset($_POST['action'])) {
            $trabajador = new Trabajador();
            
            switch ($_POST['action']) {
                case 'exportar':
                    $result = $trabajador->exporDataBase();
                    echo json_encode($result);
                    exit;
                    
                case 'restaurar':
                    if (isset($_POST['filePath'])) {
                        // Restaurar desde archivo existente
                        $filePath = $_POST['filePath'];
                        $result = $trabajador->importDatabase($filePath);
                    } elseif (isset($_FILES['backupFile'])) {
                        // Restaurar desde upload
                        $uploadDir = 'bd_backup/uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        
                        $uploadedFile = $_FILES['backupFile'];
                        $fileName = uniqid() . '_' . basename($uploadedFile['name']);
                        $filePath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
                            $result = $trabajador->importDatabase($filePath);
                            // Opcional: eliminar el archivo temporal después de importar
                            // unlink($filePath);
                        } else {
                            throw new Exception('Error al subir el archivo');
                        }
                    } else {
                        throw new Exception('No se proporcionó archivo para restaurar');
                    }
                    
                    echo json_encode($result);
                    exit;
                    
                case 'eliminarBackup':
                    if (isset($_POST['filePath'])) {
                        $filePath = $_POST['filePath'];
                        if (file_exists($filePath) && unlink($filePath)) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Backup eliminado correctamente'
                            ]);
                        } else {
                            throw new Exception('Error al eliminar el archivo');
                        }
                    }
                    exit;
            }
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// Mostrar vista
renderView();
?>