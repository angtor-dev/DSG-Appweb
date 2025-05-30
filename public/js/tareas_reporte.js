

//---------------------------------------- LLENAR DETALLES-----------------------------------------
function mostrarModalDetalle(tareaData) {
    const tarea = tareaData.tarea;
    
    // 1. Mostrar información básica de la tarea
    $("#detalle-id").text(tarea.id);
    $("#detalle-area").text(tarea.area_nombre);
    $("#detalle-departamento").text(tarea.departamento_nombre);
    $("#detalle-descripcion").text(tarea.descripcion);
    $("#detalle-fecha").text(new Date(tarea.fechaCreacion).toLocaleString());
    $("#detalle-estado").text(tarea.estado_tarea.charAt(0).toUpperCase() + tarea.estado_tarea.slice(1));

    // 2. Mostrar personal asignado y supervisor
    const $personalList = $("#detalle-personal");
    $personalList.empty();
    
    if (tarea.personal && tarea.personal.length > 0) {
        tarea.personal.forEach(persona => {
            $personalList.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${persona.nombre} ${persona.apellido}
                    <span class="badge bg-info">${persona.departamento}</span>
                </li>
            `);
        });
        
        // Mostrar supervisor (si existe)
        if (tarea.supervisor && tarea.supervisor.length > 0) {
            const supervisor = tarea.supervisor[0];
            $personalList.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                    <strong>Supervisor:</strong> ${supervisor.nombre} ${supervisor.apellido}
                    <span class="badge bg-warning text-dark">Supervisor</span>
                </li>
            `);
        }
    } else {
        $personalList.append('<li class="list-group-item">No hay personal asignado</li>');
    }

    // 3. Mostrar materiales (usados y devueltos)
    const $materialesList = $("#detalle-materiales");
    $materialesList.empty();
    
    if (tarea.materiales && tarea.materiales.length > 0) {
        tarea.materiales.forEach(material => {
            const devolucionInfo = material.devolucion === 1 ? 
                `<span class="text-success">Devolución: ${material.cantidadDevolucion}</span>` : 
                '<span class="text-danger">Sin devolución</span>';
            
            $materialesList.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${material.nombre}</strong><br>
                        <small>${material.descripcion}</small>
                    </div>
                    <div class="text-end">
                        <span class="d-block">Asignados: ${material.cantidad}</span>
                        ${devolucionInfo}
                    </div>
                </li>
            `);
        });
    } else {
        $materialesList.append('<li class="list-group-item">No se asignaron materiales</li>');
    }

    // 4. Mostrar evaluación del supervisor y director
    const $evaluacionSection = $("#detalle-comentarios");
    $evaluacionSection.empty();
    
    if (tarea.evaluacion) {
        let evaluacionHTML = `
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Evaluación del Supervisor</h6>
                </div>
                <div class="card-body">
                    <p><strong>Ponderación:</strong> ${formatPonderacion(tarea.evaluacion.evaluacion_supervisor)}</p>
                    <p><strong>Comentarios:</strong> ${tarea.evaluacion.comentario_supervisor || 'Ninguno'}</p>
                    <p><strong>Fecha:</strong> ${new Date(tarea.evaluacion.fecha_evaluacion_supervisor).toLocaleString()}</p>
                </div>
            </div>
        `;
        
        if (tarea.evaluacion.evaluacion_director) {
            evaluacionHTML += `
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Evaluación del Director</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Ponderación:</strong> ${formatPonderacion(tarea.evaluacion.evaluacion_director)}</p>
                        <p><strong>Comentarios:</strong> ${tarea.evaluacion.comentario_director || 'Ninguno'}</p>
                        <p><strong>Fecha:</strong> ${new Date(tarea.evaluacion.fecha_evaluacion_director).toLocaleString()}</p>
                    </div>
                </div>
            `;
        }
        
        $evaluacionSection.html(evaluacionHTML);
    } else {
        $evaluacionSection.html('<div class="alert alert-info">No hay evaluación registrada</div>');
    }
}

// Función auxiliar para formatear la ponderación
function formatPonderacion(ponderacion) {
    const formatos = {
        'buenobueno': 'Bueno - Bueno',
        'buenomedio': 'Bueno - Medio',
        'buenomalo': 'Bueno - Malo',
        'mediomedio': 'Medio - Medio',
        'mediomalo': 'Medio - Malo',
        'malomalo': 'Malo - Malo'
    };
    return formatos[ponderacion] || ponderacion;
}

//--------------------------------LLenarDETALLE
function obtenerDetallePorId(idTarea) {
  $.ajax({
    url: "Tareas/Detalle",
    type: "POST",
    data: {
      id: idTarea, // Solo envía el ID para consulta
    },
    success: function (response) {
      if (response.success) {
        console.log("Datos de la tarea y evaluación:", response.data);
        mostrarModalDetalle(response.data);
      } else {
        mostrarError(response.message || "Error al obtener los datos");
      }
    },
    error: function (xhr) {
      mostrarError("Error del servidor al obtener los datos");
      console.error(xhr.responseText);
    },
  });
}

//--------------------------------Comienza docuemtno -----------------

$(document).ready(function () {


 
  


  //--------------------------------------------------MODAL DETALLES-_----------------------------------

  $(document).on("show.bs.modal", "#modal-detalles", function (e) {
    const modal = $(this); // <<<<<< IMPORTANTE
    const button = $(e.relatedTarget);
    url = button.data("bs-url");
    const valorId = button.data("valor");
    console.log(valorId);
    console.log(url);

    if (typeof url === "undefined") {
      // Cargar el contenido del modal
    } else {
      $.ajax({
        url: url,
        method: "GET",
        success: function (data) {
          console.log(valorId);
          modal.find(".modal-content").html(data);
          obtenerDetallePorId(valorId)
         

       
        },
        error: function () {},
      });
    }
    /* if (!url || url.indexOf('/') === -1) {
                url = '/DSG-Appweb/Tareas/Orden';
                return;
            } */
  });
 
    
});
