<?php
// En la vista (al inicio del archivo):
/** @var array $backups */
$backups = getBackupFiles();
?>

<div class="panel-header" style="background-color: #4e73df;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Mantenimiento de Base de Datos</h3>
                <span class="opacity-75 mb-2">Exportar y restaurar copias de seguridad</span>
            </div>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Exportar Base de Datos</h5>
                            <p class="card-text">Genera una copia de seguridad completa del sistema</p>
                            <button id="exportarBtn" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Exportar ahora
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Restaurar Base de Datos</h5>
                            <p class="card-text">Selecciona un archivo de copia de seguridad</p>
                            <form id="restaurarForm" enctype="multipart/form-data" class="d-flex flex-column align-items-center">
                                <div class="custom-file mb-3" style="width: 100%; max-width: 300px;">
                                    <input type="file" class="custom-file-input" name="backupFile" id="backupFile" accept=".sql" required>
                                    <label class="custom-file-label" for="backupFile">Seleccionar archivo (.sql)</label>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-upload me-2"></i>Restaurar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Copias de seguridad disponibles</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="backupsTable" class="datatable table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre del archivo</th>
                                    <th>Tamaño</th>
                                    <th>Fecha de creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backups as $backup): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($backup['filename']) ?></td>
                                        <td><?= formatSizeUnits($backup['filesize']) ?></td>
                                        <td><?= date('d/m/Y H:i', $backup['filemtime']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-success restoreBackup" 
                                                    data-file="<?= htmlspecialchars($backup['filepath']) ?>">
                                                <i class="fas fa-undo"></i> Restaurar
                                            </button>
                                            <button class="btn btn-sm btn-danger deleteBackup" 
                                                    data-file="<?= htmlspecialchars($backup['filepath']) ?>">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    const table = $('#backupsTable').DataTable({
        
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [3] }
        ]
    });

    // Mostrar loading
    function showLoading() {
        $('body').append('<div class="loading-overlay"><div class="spinner-border text-primary"></div></div>');
    }

    // Ocultar loading
    function hideLoading() {
        $('.loading-overlay').remove();
    }

    // Exportar BD
    $('#exportarBtn').click(function() {
        if(confirm('¿Estás seguro de que deseas generar una copia de seguridad?')) {
            showLoading();
            $.post('Respaldo/Index', { action: 'exportar' }, function(response) {
                hideLoading();
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Recargar para mostrar el nuevo backup
                } else {
                    alert('Error: ' + response.message);
                }
            }).fail(function() {
                hideLoading();
                alert('Ocurrió un error al procesar la solicitud');
            });
        }
    });

    // Restaurar desde formulario (upload)
    $('#restaurarForm').submit(function(e) {
        e.preventDefault();
        
        if(confirm('¡ADVERTENCIA! Esta acción sobrescribirá todos los datos actuales. ¿Estás seguro de que deseas continuar?')) {
            showLoading();
            const formData = new FormData(this);
            formData.append('action', 'restaurar');
            
            $.ajax({
                url: 'Respaldo/Index',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    hideLoading();
                    alert('Ocurrió un error al procesar la solicitud');
                }
            });
        }
    });

    // Restaurar desde lista
    $('#backupsTable').on('click', '.restoreBackup', function() {
        const filePath = $(this).data('file');
        const fileName = filePath.split('/').pop();
        
        if(confirm(`¿Estás seguro de que deseas restaurar la copia de seguridad "${fileName}"?\n\n¡ADVERTENCIA! Esta acción sobrescribirá todos los datos actuales.`)) {
            showLoading();
            $.post('Respaldo/Index', { 
                action: 'restaurar',
                filePath: filePath 
            }, function(response) {
                hideLoading();
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }).fail(function() {
                hideLoading();
                alert('Ocurrió un error al procesar la solicitud');
            });
        }
    });

    // Eliminar backup
    $('#backupsTable').on('click', '.deleteBackup', function() {
        const filePath = $(this).data('file');
        const fileName = filePath.split('/').pop();
        
        if(confirm(`¿Estás seguro de que deseas eliminar la copia de seguridad "${fileName}"?`)) {
            showLoading();
            $.post('Respaldo/Index', { 
                action: 'eliminarBackup',
                filePath: filePath 
            }, function(response) {
                hideLoading();
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }).fail(function() {
                hideLoading();
                alert('Ocurrió un error al procesar la solicitud');
            });
        }
    });

    // Mostrar nombre de archivo seleccionado
    $('#backupFile').change(function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(fileName || 'Seleccionar archivo (.sql)');
    });
});
</script>

<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.custom-file-input:focus ~ .custom-file-label {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}
</style>