function hideExportDatatable (settings) {
    var api = this.api();
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

function defaultHideButtonsDataTable () {
    console.log('defaultHideButtonsDataTable');
    $.extend(true, $.fn.dataTable.defaults, {
        // 3. Aplicamos la lógica de visibilidad en el drawCallback global
        "drawCallback": hideExportDatatable
    }); 
    
}
