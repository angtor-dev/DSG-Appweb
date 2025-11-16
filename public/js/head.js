
function hideExportDatatable (settings) {
    var api = new $.fn.dataTable.Api(settings);
    // Comprueba si la tabla tiene 0 filas de datos
    if (api.rows({ filter: 'applied' }).count() === 0) {
        // Oculta el contenedor de todos los botones

        if(api.buttons().count() > 0){
            api.buttons().container().hide();
        }
        
        // Oculta el div donde se renderizan los botones (si usas un contenedor específico)
        // $('#contenedor-botones-exportar').hide(); 
        
    } else {
        // Muestra el contenedor si la tabla tiene datos
        if(api.buttons().count() > 0){
            api.buttons().container().show();
        }
        
        // $('#contenedor-botones-exportar').show();
    }
}

function defaultHideButtonsDataTable() {
    // Definimos la función principal que será el drawCallback por defecto.
    var defaultCallbackExecutor = function(settings) {
        // 1. Ejecutar la función base requerida por defecto (hideExportDatatable)
        // Se asume que 'hideExportDatatable' existe en el scope global.
        if (typeof hideExportDatatable === 'function') {
            hideExportDatatable(settings); // Pasamos 'settings' por buena práctica
        }

        // 2. Obtener el valor de 'drawCallbackPlus' de la configuración del DataTable actual (settings)
        var plusCallbacks = settings.oInit.drawCallbackPlus;

        if (plusCallbacks) {
            // Verificar si 'drawCallbackPlus' es un ARRAY de funciones
            if (Array.isArray(plusCallbacks)) {
                plusCallbacks.forEach(function(callback) {
                    if (typeof callback === 'function') {
                        callback(settings); // Ejecutar cada función en el array
                    }
                });
            }
            // Verificar si 'drawCallbackPlus' es una FUNCIÓN única
            else if (typeof plusCallbacks === 'function') {
                plusCallbacks(settings); // Ejecutar la función única
            }
        }
    };

    // 3. Aplicar la lógica al objeto de configuraciones por defecto de DataTables
    $.extend(true, $.fn.dataTable.defaults, {
        "drawCallback": defaultCallbackExecutor,
        // 4. Se mantiene el parámetro para facilitar la configuración por tabla
        "drawCallbackPlus": null
    });
}
