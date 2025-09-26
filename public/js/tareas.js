// Función para mostrar el modal de confirmación
function mostrarModalCancelar(id, element) {
    const modal = $('#modal-cancelar');
    
    // Configurar el modal
    modal.find('.modelo').text('la tarea');
    modal.find('.nombre').text(`ID: ${id}`);
    
    // Configurar el botón de aceptar
    modal.find('.eliminar').off('click').on('click', function(e) {
        e.preventDefault();
        modal.modal('hide');
        cancelarTarea(id, element);
    });
    
    // Mostrar el modal
    modal.modal('show');
}

function mostrarModalTerminar(id, element) {
    const modal = $('#modal-terminar');
    
    // Configurar el modal
    modal.find('.nombre').text(`ID: ${id}`);
    
    // Configurar el botón de confirmación
    modal.find('.confirmar').off('click').on('click', function(e) {
        e.preventDefault();
        modal.modal('hide');
        terminarTarea(id, element);
    });
    
    // Mostrar el modal
    modal.modal('show');
}


function terminarTarea(id, element) {
    const icon = $(element).find("i");
    icon.removeClass("fa-check-circle").addClass("fa-spinner fa-spin");
    
    $.ajax({
        url: "Tareas",
        type: "POST",
        data: { 
            id: id,
            action: 'terminar' 
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                Toastify({
                    text: response.message,
                    duration: 3000,
                    close: true,
                    gravity: "bottom",
                    position: "center",
                    backgroundColor: "#28a745",
                    stopOnFocus: true,
                    className: "toastify-success"
                }).showToast();
            } else {
                Toastify({
                    text: response.message,
                    duration: 3000,
                    close: true,
                    gravity: "bottom",
                    position: "center",
                    backgroundColor: "#dc3545",
                    stopOnFocus: true,
                    className: "toastify-error"
                }).showToast();
            }
        },
        error: function (xhr) {
            Toastify({
                text: "Error al contactar el servidor",
                duration: 3000,
                close: true,
                 gravity: "bottom",
                position: "center",
                backgroundColor: "#dc3545",
                stopOnFocus: true,
                className: "toastify-error"
            }).showToast();
        },
        complete: function () {
            icon.removeClass("fa-spinner fa-spin").addClass("fa-check-circle");
            $(".datatable").DataTable().ajax.reload();
        }
    });
}

function cancelarTarea(id, element) {
    const icon = $(element).find("i");
    icon.removeClass("fa-ban").addClass("fa-spinner fa-spin");
    
    $.ajax({
        url: "Tareas",
        type: "POST",
        data: { 
            id: id,
            action: 'cancelar' 
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                Toastify({
                    text: response.message,
                    duration: 3000,
                    close: true,
                    gravity: "bottom",
                    position: "center",
                    backgroundColor: "#28a745",
                    stopOnFocus: true
                }).showToast();
            } else {
                Toastify({
                    text: response.message,
                    duration: 3000,
                    close: true,
                    gravity: "bottom",
                    position: "center",
                    backgroundColor: "#dc3545",
                    stopOnFocus: true
                }).showToast();
            }
        },
        error: function (xhr) {
            Toastify({
                text: "Error al contactar el servidor",
                duration: 3000,
                close: true,
                gravity: "bottom",
                position: "right",
                backgroundColor: "#dc3545",
                stopOnFocus: true
            }).showToast();
        },
        complete: function () {
            icon.removeClass("fa-spinner fa-spin").addClass("fa-ban");
            $(".datatable").DataTable().ajax.reload();
        }
    });
}
function renderButtons(row) {
  let buttons = '<div class="d-flex gap-2">';

  // Botones específicos por estado
  switch (row.estado.toLowerCase()) {
    case "activo":
        buttons += `
            <div class="d-flex gap-2">
                <!-- Ver Detalles -->
                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Detalles">
                    <div data-bs-toggle="modal" data-bs-target="#modal-orden"
                        data-bs-url="Tareas/Orden?id=${row.id}" data-valor="${row.id}">
                        <i class="fa-solid fa-fw fa-eye"></i>
                    </div>
                </div>

              <!-- Terminar -->
                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Terminar Tarea" 
                    onclick="mostrarModalTerminar(${row.id}, this)">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
                
             <!-- Cancelar -->
                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancelar Tarea" 
                    onclick="mostrarModalCancelar(${row.id}, this)">
                    <i class="fa-solid fa-ban"></i>
                </div>
            </div>
        `;
        break;

    case "vencida":
        buttons += `
            <div class="d-flex gap-2">
                <?php if (tienePermiso(Modulo::TAREAS, Permiso::ACTUALIZAR)): ?>
                    <!-- Evaluar -->
                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Evaluar Tarea">
                        <button class="btn btn-sm btn-outline-success" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modal-evaluar"
                            data-bs-url="Tareas/Evaluar?id=${row.id}" data-valor="${row.id}">
                            <i class="fas fa-check"></i> Evaluar
                        </button>
                    </span>
                <?php endif; ?>
            </div>
        `;
        break;

    case "cancelado":
        buttons += `
            <div class="d-flex gap-2">
                <!-- Ver Detalles -->
                 <div class="accion pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Detalles">
                    <div data-bs-toggle="modal" data-bs-target="#modal-orden"
                        data-bs-url="Tareas/Orden?id=${row.id}" data-valor="${row.id}">
                        <i class="fa-solid fa-fw fa-eye"></i>
                    </div>
                </div>
            </div>
        `;
        break;

    case "evaluada":
        buttons += `
            <div class="d-flex gap-2">
                <?php if (tienePermiso(Modulo::TAREAS, Permiso::CONSULTAR)): ?>
                    <!-- Ver Detalles -->
                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Detalles de Evaluación">
                        <button class="btn btn-sm btn-outline-secondary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modal-detalles"
                            data-bs-url="Tareas/Detalle?id=${row.id}" data-valor="${row.id}">
                            <i class="fas fa-eye"></i> Detalle
                        </button>
                    </span>
                <?php endif; ?>
            </div>
        `;
        break;

    case "comun":
        buttons += `
            <div class="d-flex gap-2">
                <!-- Configurar -->
                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Configurar Tarea">
                    <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="Tareas/Configuracion?id=${row.id}">
                        <i class="fa-solid fa-fw fa-gear"></i>
                    </div>
                </div>
                
                <!-- Eliminar -->
                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Tarea" 
                    onclick="eliminarTarea(${row.id}, this)">
                    <i class="fa-solid fa-trash"></i>
                </div>
            </div>
        `;
        break;
}

buttons += "</div>";
return buttons;
}
//---------------------------------------- LLENAR EVALUACION---------------------------------------
function mostrarModalEvaluacion(tareaData) {
  const modal = $("#modal-evaluacion"); // Asegúrate que este ID coincide con tu modal de evaluación

  // Cargar plantilla base primero (si es necesario)
  // modal.find('.modal-content').load('Ruta/PlantillaEvaluacion', function() {

  // Insertar datos básicos de la tarea (opcional)
  console.log(tareaData.data); // This shows the full response
  console.log(tareaData); // This shows the wrapper object  // This accesses the tarea object
  console.log(tareaData.tarea.descripcion); // This might be undefined if descripcion is inside tarea

  // Corrected lines:
  $("#evaluacion-descripcion").text(tareaData.tarea.descripcion);
  $("#evaluacion-departamento").text(tareaData.tarea.departamento_nombre);
  $("#evaluacion-area").text(tareaData.tarea.area_nombre);

  // Llenar tabla de materiales utilizados
  const tbody = $("#tabla-materialesDevueltos tbody");
  tbody.empty(); // Limpiar tabla antes de llenar

  if (tareaData.tarea.materialestarea && tareaData.tarea.materialestarea.length > 0) {
    tareaData.tarea.materialestarea.forEach((material, index) => {
      const unidad = material.medida_nombre || "";
      const esDecimal = [
        "m",
        "metro",
        "kg",
        "kilogramo",
        "l",
        "litro",
      ].includes(unidad.toLowerCase());
      const step = esDecimal ? 'step="0.1"' : "";
      const max = parseFloat(material.cantidad);

      const row = `
        <tr data-id="${material.id}">
            <td>${material.nombre}</td>
            <td>${material.cantidad} ${unidad}</td>
            <td>
                <input type="hidden" name="materiales[${index}][id]" value="${material.id}">
                <input type="number" name="materiales[${index}][utilizado]" 
                       class="form-control form-control-sm material-usado" 
                       value="${material.cantidad}" 
                       min="0" max="${max}" ${step}>
            </td>
            <td>
                <input type="number" name="materiales[${index}][devuelto]" 
                       class="form-control form-control-sm material-devuelto" 
                       value="0" 
                       min="0" max="${max}" ${step}>
            </td>
        </tr>
    `;
      tbody.append(row);
    });
  } else {
    tbody.append(
      '<tr><td colspan="5" class="text-center">No se asignaron materiales</td></tr>'
    );
  }

  // Lógica de sincronización entre campos
  $(document).on("input", ".material-usado, .material-devuelto", function () {
    const row = $(this).closest("tr");
    const maxText = row.find("td:nth-child(2)").text().split(" ")[0];
    const max = parseFloat(maxText);
    const unidad = row.find("td:nth-child(2)").text().split(" ")[1] || "";
    const esDecimal = ["m", "metro", "kg", "kilogramo", "l", "litro"].includes(
      unidad.toLowerCase()
    );

    const usado = row.find(".material-usado");
    const devuelto = row.find(".material-devuelto");

    if ($(this).hasClass("material-usado")) {
      const val = Math.min(max, parseFloat($(this).val()) || 0);
      $(this).val(val.toFixed(esDecimal ? 1 : 0));
      devuelto.val((max - val).toFixed(esDecimal ? 1 : 0));
    } else {
      const val = Math.min(max, parseFloat($(this).val()) || 0);
      $(this).val(val.toFixed(esDecimal ? 1 : 0));
      usado.val((max - val).toFixed(esDecimal ? 1 : 0));
    }
  });

  // Mostrar el modal
  modal.modal("show");
  // });
}

//---------------------------------------- LLENAR DETALLES-----------------------------------------
function mostrarModalDetalle(tareaData) {
    const tarea = tareaData.tarea;
    
    // 1. Mostrar información básica de la tarea
    $("#detalle-id").text(tarea.id);
    $("#detalle-area").text(tarea.area_nombre);
    $("#detalle-departamento").text(tarea.departamento_nombre);
    $("#detalle-descripcion").text(tarea.descripcion_tarea);
    $("#detalle-fecha").text(tarea.fecha_inicio_tarea);
   

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
    
    if (tarea.materialestarea && tarea.materialestarea.length > 0) {
        tarea.materialestarea.forEach(material => {
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
    
    if (tarea.evaluacion_tarea) {
        const evaluacionSupervisor = tarea.evaluacion_tarea.evaluacion_supervisor;
        const evaluacionDirector = tarea.evaluacion_tarea.evaluacion_director;
        const fecha = new Date(tarea.evaluacion_tarea.fecha_evaluacion_supervisor).toLocaleString();
        const observaciones = tarea.evaluacion_tarea.comentario_supervisor || 'Ninguno';
        
        let evaluacionHTML = `
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Evaluación del Supervisor</h6>
                        </div>
                        <div class="card-body">
                            <p>${formatPonderacion(evaluacionSupervisor)}</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Evaluación del Director</h6>
                        </div>
                        <div class="card-body">
                            <p>${evaluacionDirector ? formatPonderacion(evaluacionDirector) : 'Ninguna'}</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Información adicional</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Fecha:</strong> ${fecha}</p>
                            <p><strong>Observaciones:</strong> ${observaciones}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
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
//----------------------------------------LLENAR ORDEN-------------------------------- - - - -

function obtenerOrdenPorId(idTarea) {
  $.ajax({
    url: "Tareas/Orden", // Asegúrate que este sea el path correcto
    type: "POST",
    data: { id: idTarea },
    success: function (response) {
      if (response.success) {
        console.log("Datos de la orden:", response.data);
        // Aquí puedes llamar funciones para llenar los campos
        console.log(response);
        mostrarModalOrden(response);
      } else {
        mostrarError(response.message || "Error al obtener la orden");
      }
    },
    error: function (xhr) {
      mostrarError("Error del servidor al obtener la orden");
      console.error(xhr.responseText);
    },
  });
}

// ------------------------------------------- LLENAR DATOS EVALUACION .-.-------------------------------
function obtenerEvaluarPorId(idTarea) {
  $.ajax({
    url: "Tareas/Evaluar",
    type: "POST",
    data: {
      id: idTarea, // Solo envía el ID para consulta
    },
    success: function (response) {
      if (response.success) {
        console.log("Datos de la tarea y evaluación:", response.data);
        mostrarModalEvaluacion(response.data);
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

function mostrarModalOrden(tareaData) {
  const modal = $("#modal-orden");
  
  modal.find(".modal-content").load("Tareas/Orden", function () {
    // Verificar si los datos necesarios existen
    if (!tareaData.data || !tareaData.data.tarea) {
      console.error("Datos de tarea no encontrados");
      return;
    }

    const tarea = tareaData.data.tarea;

    // Fecha y hora - usar fecha actual si no viene en los datos
    const fechaHora = tarea.fechaCreacion || new Date().toISOString().replace('T', ' ').substring(0, 19);
    const [fecha, hora] = fechaHora.split(" ");

    // Insertar datos básicos
    $("#orden-fecha").text(fecha);
    $("#orden-hora").text(hora);
    $("#orden-inicio").text(tarea.fecha_inicio_tarea || "No especificado");
    $("#orden-departamento").text(tarea.departamento_nombre || "No especificado");
    $("#orden-area").text(tarea.area_nombre || "No especificado");
    $("#orden-descripcion").text(tarea.descripcion_tarea || "No hay descripción");
    $("#orden-observaciones").val(tarea.observaciones || "");

    // Personal asignado
    let personalHtml = "";
    if (tarea.personal && tarea.personal.length > 0) {
      tarea.personal.forEach((persona, index) => {
        personalHtml += `
          <tr>
            <td>${index + 1}</td>
            <td>${persona.nombre || ''} ${persona.apellido || ''}</td>
            <td>${persona.cargo || persona.departamento || ''}</td>
          </tr>
        `;
      });
    } else {
      personalHtml = `<tr><td colspan="3">No hay personal asignado</td></tr>`;
    }
    $("#personal-lista").html(personalHtml);

    // Tareas y materiales
    let tareasHtml = "";
    tareasHtml += `
      <tr>
        <td>1</td>
        <td>${tarea.descripcion_tarea || "No hay descripción de tarea"}</td>
        <td>
          <ul class="list-unstyled mb-0">
    `;

    if (tarea.materialestarea && tarea.materialestarea.length > 0) {
      tarea.materialestarea.forEach((material) => {
        tareasHtml += `<li>${material.cantidad || 0} x ${material.nombre || 'Material'}</li>`;
      });
    } else {
      tareasHtml += `<li>No hay materiales asignados.</li>`;
    }

    tareasHtml += `
          </ul>
        </td>
      </tr>
    `;
    $("#tareas-lista").html(tareasHtml);

    // Supervisor
    if (tarea.supervisor && tarea.supervisor.length > 0) {
      const sup = tarea.supervisor[0];
      $("#orden-responsable").text(`${sup.nombre || ''} ${sup.apellido || ''}`);
    } else {
      $("#orden-responsable").text("Supervisor no asignado");
    }

    modal.modal("show");
  });
}

//-------------------------------------- Barra de progreso -----------------------
/* // Función para actualizar la barra de progreso
function actualizarProgreso() {
  let progreso = 0;
  let mensaje = "";

  // Lógica para determinar el progreso
  if ($("#seccion-supervisor").find(".badge").text() === "Completado") {
    progreso = 50;
    mensaje = "Falta evaluación del director";
  } else if ($("#seccion-director").find(".badge").text() === "Completado") {
    progreso = 100;
    mensaje = "Evaluación completa";
  } else {
    mensaje = "Falta evaluación del supervisor";
  }

  $("#progreso-evaluacion")
    .css("width", progreso + "%")
    .text(progreso + "% completado");
  $("#estado-evaluacion").html(
    '<small class="text-muted">' + mensaje + "</small>"
  );

  // Cambiar color según progreso
  if (progreso < 50) {
    $("#progreso-evaluacion")
      .removeClass("bg-success bg-warning")
      .addClass("bg-danger");
  } else if (progreso < 100) {
    $("#progreso-evaluacion")
      .removeClass("bg-success bg-danger")
      .addClass("bg-warning");
  } else {
    $("#progreso-evaluacion")
      .removeClass("bg-warning bg-danger")
      .addClass("bg-success");
  }
} */

//--------------------------------Comienza docuemtno -----------------

$(document).ready(function () {

  actualizarContadoresTareas();
    
  
    setInterval(actualizarContadoresTareas, 2000);

  function actualizarContadoresTareas() {
    $.ajax({
        url: '?ajax=contadores',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Actualizar los elementos del DOM con los nuevos valores
            $('#tareas-activas').text(data.activo);
            $('#tareas-vencidas').text(data.vencida);
            $('#tareas-canceladas').text(data.cancelado);
        },
        error: function(xhr, status, error) {
            console.error('Error al actualizar contadores:', error);
        }
    });
}

 // Configuración base común
const commonConfig = {
  serverSide: false,
  searching: true,
  ordering: true,
  paging: true,
  language: {
    url: "public/lib/DataTables/datatables-spanish.json",
  },
  columns: [
    { 
      data: "id",
      visible: false  
    },
    { data: "area" },
    { data: "departamento" },
    { data: "descripcion" },
    { data: "fecha" },
    { data: "estado" },
    {
      data: null,
      render: function (data, type, row) {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip();
        return renderButtons(row);
      },
    },
  ],
};

  // Función para renderizar botones según el estado

  // Inicialización de las tablas
  const tablaActivas = $("#tabla-activas").DataTable({
    ...commonConfig,
    ajax: {
      url: "Tareas?ajax=1",
      dataSrc: "activo",
    },
  });

  const tablaVencidas = $("#tabla-vencidas").DataTable({
    ...commonConfig,
    ajax: {
      url: "Tareas?ajax=1",
      dataSrc: "vencida",
    },
  });

  console.log(`Estás en ${window.location.pathname}`);

  const tablaComunes = $("#tabla-cancelada").DataTable({
    ...commonConfig,
    ajax: {
      url: "Tareas?ajax=1",
      dataSrc: "cancelado",
    },
  });

  const tablaEvaluada = $("#tabla-evaluada").DataTable({
    ...commonConfig,
    ajax: {
      url: "Tareas?ajax=1",
      dataSrc: "evaluada",
    },
  });

  const tablaComun = $("#tabla-comun").DataTable({
    ...commonConfig,
    ajax: {
      url: "Tareas?ajax=1",
      dataSrc: "comun",
    },
  });

  // Recargar tablas al cambiar pestaña
  $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
    const target = $(e.target).attr("aria-controls");
    const tablas = {
      "home-tab-pane": tablaActivas,
      "profile-tab-pane": tablaVencidas,
      "contact-tab-pane": tablaComunes,
      "evaluada-tab-pane": tablaEvaluada,
      "comun-tab-pane": tablaComun,
    };

    if (tablas[target]) {
      tablas[target].ajax.reload();
    }
  });

  //---------------------------------------------MODAL GENERICO
  $(document).on("show.bs.modal", "#modal-generico", function (e) {
    const modal = $(this); // <<<<<< IMPORTANTE
    const button = $(e.relatedTarget);
    const url = button.data("bs-url");

    // Cargar el contenido del modal
    modal.find(".modal-content").load(url, function () {
      // pasa el modal como parámetro
    });
  });

  //--------------------------------------------------MODAL EVALUACION ----------------------------

  $(document).on("show.bs.modal", "#modal-evaluar", function (e) {
    const modal = $(this); // <<<<<< IMPORTANTE
    const button = $(e.relatedTarget);
    url = button.data("bs-url");
    const valorId = button.data("valor");

    if (typeof url === "undefined") {
      // Cargar el contenido del modal
    } else {
      $.ajax({
        url: url,
        method: "GET",
        success: function (data) {
          console.log(valorId);
          modal.find(".modal-content").html(data);

          obtenerEvaluarPorId(valorId);
          CargaModalComponentsGenerico(valorId);
        },
        error: function () {},
      });
    }
    /* if (!url || url.indexOf('/') === -1) {
                url = '/DSG-Appweb/Tareas/Orden';
                return;
            } */
  });


  // --------------------------------------------------MODAL CANCELAR------------------------------------
  $(document).on("show.bs.modal", "#modal-cancelar", function (e) {
   
  });

  //----------------------------------------------------MODAL ORDENES ----------------------------

   $(document).on("show.bs.modal", "#modal-ordenes", function (e) {
    const modal = $(this); // <<<<<< IMPORTANTE
    const button = $(e.relatedTarget);
    url = button.data("bs-url");
    const valorId = button.data("valor");

    //datatable 
    

    if (typeof url === "undefined") {
      // Cargar el contenido del modal
    } else {
      $.ajax({
        url: url,
        method: "GET",
        success: function (data) {
          console.log(valorId);
          modal.find(".modal-content").html(data);

          cargarDatosParaAgrupacion();
          
         // obtenerEvaluarPorId(valorId);
         // CargaModalComponentsGenerico(valorId);
        },
        error: function () {},
      });
    }
    /* if (!url || url.indexOf('/') === -1) {
                url = '/DSG-Appweb/Tareas/Orden';
                return;
            } */
  });

  function obtenerDatosTareasSeleccionadas(ids) {
    return new Promise((resolve, reject) => {
      console.log("asdas");
        $.ajax({
            url: "Tareas/Ordenes", // Ajusta esta ruta según tu controlador
            type: "POST",
            data: {
                ids: ids.join(',') // Enviamos los IDs como cadena separada por comas
            },
            success: function(response) {
                if (response.success) {
                    console.log("Datos de las tareas:", response.data);
                    resolve(response.data);
                } else {
                    mostrarError(response.message || "Error al obtener los datos de las tareas");
                    reject(response.message);
                }
            },
            error: function(xhr) {
                mostrarError("Error del servidor al obtener los datos");
                console.error(xhr.responseText);
                reject(xhr.responseText);
            }
        });
    });
}

// Función para obtener IDs seleccionados (ejemplo)
function obtenerIdsSeleccionados() {
    const selectedIds = [];
    $('.tarea-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });
    return selectedIds;
}

// Función para mostrar loading (debes implementarla según tu UI)
function mostrarLoading(mensaje) {
    console.log(mensaje);
    // Ejemplo con SweetAlert:
    // Swal.fire({
    //     title: mensaje,
    //     allowOutsideClick: false,
    //     didOpen: () => {
    //         Swal.showLoading();
    //     }
    // });
}

// Función para ocultar loading
function ocultarLoading() {
    // Ejemplo con SweetAlert:
    // Swal.close();
}

  function cargarDatosParaAgrupacion() {
    // Mostrar carga
  
    $('#tabla-tareas-agrupar tbody').html('<tr><td colspan="7" class="text-center">Cargando tareas...</td></tr>');    
    
    $(document).ready(function() {
    // Función auxiliar para clases de badge según estado
    function getEstadoBadgeClass(estado) {
        switch (estado.toLowerCase()) {
            case 'activo': 
            case 'activa': return 'bg-success';
            case 'vencida': return 'bg-danger';
            case 'evaluada': return 'bg-info';
            case 'cancelado': 
            case 'cancelada': return 'bg-secondary';
            default: return 'bg-warning';
        }
    }

    // Inicializar DataTable
    const table = $('#tabla-tareas-agrupar').DataTable({
        serverSide: false,
        searching: true,
        ordering: true,
        paging: true,
        language: {
            url: "public/lib/DataTables/datatables-spanish.json",
        },
        columns: [
            {
                data: null,
                orderable: false,
                className: 'select-checkbox',
                render: function(data, type, row) {
                    return '<input type="checkbox" class="form-check-input tarea-checkbox" value="' + row.id + '" style="transform: scale(1.5);">';
                }
            },
            { data: "id" },
            { 
                data: "personal_nombre",
                render: function(data, type, row) {
                    if ((!data || data === '') && row.personal && row.personal.length > 0) {
                        return row.personal.map(p => p.nombre_completo).join(', ');
                    }
                    return data || 'Sin asignar';
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    return (row.departamento || 'N/A') + '/' + (row.area || 'N/A');
                }
            },
            { data: "descripcion" },
            { data: "fecha" },
            { 
                data: "estado",
                render: function(data, type, row) {
                    return '<span class="badge ' + getEstadoBadgeClass(data) + '">' + data + '</span>';
                }
            }
        ],
        ajax: {
            url: "Tareas/Ordenes?ajax=1",
            dataSrc: "activo"
        },
        initComplete: function() {
            // Inicializar tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Inicializar eventos
            inicializarEventos();
        }
    });

    // Función para inicializar eventos
    function inicializarEventos() {
        // Seleccionar/deseleccionar todas las tareas
        $('#seleccionar-todo').change(function() {
            const isChecked = $(this).prop('checked');
            $('.tarea-checkbox').prop('checked', isChecked);
            actualizarBotones();
        });

        // Botón "Seleccionar todas"
        $('#btn-seleccionar-todas').click(function() {
            $('.tarea-checkbox, #seleccionar-todo').prop('checked', true);
            actualizarBotones();
        });

        // Botón "Limpiar selección"
        $('#btn-limpiar-seleccion').click(function() {
            $('.tarea-checkbox, #seleccionar-todo').prop('checked', false);
            actualizarBotones();
        });

        // Checkbox individuales
        $('#tabla-tareas-agrupar').on('change', '.tarea-checkbox', function() {
            // Actualizar el checkbox "Seleccionar todo"
            const allChecked = $('.tarea-checkbox:checked').length === $('.tarea-checkbox').length;
            $('#seleccionar-todo').prop('checked', allChecked);
            
            actualizarBotones();
        });

        // Botón "Generar vista previa"
        $('#btn-generar-preview').click(function() {
            const selectedIds = obtenerIdsSeleccionados();
            console.log("IDs seleccionados para vista previa:", selectedIds);
            
            // Aquí iría la lógica para generar la vista previa
            // Por ahora solo mostramos en consola
        });

        // Botón "Imprimir selección"
        $('#btn-imprimir-agrupadas').click(function() {
            const selectedIds = obtenerIdsSeleccionados();
            console.log(obtenerDatosTareasSeleccionadas(selectedIds));
            generarOrdenesDeTrabajo(selectedIds);
           
            console.log("IDs seleccionados para imprimir:", selectedIds);
            
            // Aquí iría la lógica para imprimir
            // Por ahora solo mostramos en consola
        });
    }

    // Función para obtener los IDs de las tareas seleccionadas
    function obtenerIdsSeleccionados() {
        const selectedIds = [];
        $('.tarea-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        return selectedIds;
    }

    // Función para actualizar el estado de los botones
    function actualizarBotones() {
        const hasSelection = $('.tarea-checkbox:checked').length > 0;
        $('#btn-generar-preview').prop('disabled', !hasSelection);
        
        // Mostrar/ocultar botón de imprimir según selección
        if (hasSelection) {
            $('#btn-imprimir-agrupadas').removeClass('d-none');
        } else {
            $('#btn-imprimir-agrupadas').addClass('d-none');
        }
    }
});


function generarOrdenesDeTrabajo(id_tarea) {
    const selectedIds = id_tarea;
    
    if (selectedIds.length === 0) {
        mostrarError("Por favor seleccione al menos una tarea");
        return;
    }
    
    // Mostrar indicador de carga
    mostrarLoading("Generando órdenes de trabajo...");
    
    obtenerDatosTareasSeleccionadas(selectedIds)
        .then(tareas => {
            generarVistaPreviaImpresion(tareas);
        })
        .catch(error => {
            console.error("Error:", error);
            mostrarError("Ocurrió un error al generar las órdenes");
        })
        .finally(() => {
            // Ocultar indicador de carga
            ocultarLoading();
        });
}
// Función para generar y mostrar la vista previa de impresión
function generarVistaPreviaImpresion(tareasSeleccionadas) {
    // 1. Primero, identificar todos los equipos únicos (combinaciones de personas)
    const equiposUnicos = {};

    tareasSeleccionadas.forEach(tarea => {
        // Crear una clave única para el equipo (orden alfabético de IDs para evitar duplicados)
        const equipoKey = tarea.personal
            .map(p => p.id)
            .sort((a, b) => a - b)
            .join('-');
        
        if (!equiposUnicos[equipoKey]) {
            equiposUnicos[equipoKey] = {
                personal: tarea.personal,
                tareas: []
            };
        }
        equiposUnicos[equipoKey].tareas.push(tarea);
    });

    // 2. Crear el HTML optimizado para impresión VERTICAL
    let htmlCompleto = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Órdenes de Trabajo Agrupadas por Equipos</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    padding: 10mm 15mm; 
                    font-size: 12px !important;
                    max-width: 210mm;
                    margin: 0 auto;
                }
                @page { 
                    size: A4 portrait; /* CAMBIADO A VERTICAL */
                    margin: 10mm;
                }
                .orden-trabajo { 
                    margin-bottom: 10mm;
                    padding: 5mm;
                    border: 1px solid #ddd;
                    page-break-inside: avoid;
                    position: relative;
                }
                .compact-table {
                    font-size: 11px !important; /* AUMENTADO */
                }
                .compact-table th, .compact-table td { 
                    padding: 4px 3px !important; /* AUMENTADO */
                    line-height: 1.3; /* MEJORADO */
                    border: 1px solid #ddd !important;
                }
                .no-print { display: none !important; }
                .no-print { 
                    display: block !important;
                    position: fixed;
                    top: 10px;
                    right: 10px;
                    z-index: 1000;
                }
                .info-box {
                    border: 1px solid #ddd;
                    padding: 4px 8px; /* AUMENTADO */
                    margin-right: 8px; /* AUMENTADO */
                    margin-bottom: 5px;
                    display: inline-block;
                    font-size: 11px; /* AUMENTADO */
                }
                
                /* MEMBRETE CON IMAGEN */
                .membrete {
                    text-align: center;
                    margin-bottom: 15px;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #000;
                }
                
                .membrete img {
                    max-width: 100%;
                    max-height: 80px;
                    object-fit: contain;
                }
                
                /* PIE DE PÁGINA */
                .pie-pagina {
                    text-align: center;
                    margin-top: 15px;
                    padding-top: 10px;
                    border-top: 1px solid #000;
                    font-weight: bold;
                    font-size: 12px;
                }
                
                /* TÍTULOS MÁS GRANDES */
                h4 {
                    font-size: 16px; /* AUMENTADO */
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                
                h5 {
                    font-size: 14px; /* AUMENTADO */
                    font-weight: bold;
                }
                
                /* MEJORAS DE ESPACIADO */
                .mb-1 {
                    margin-bottom: 8px !important;
                }
                
                .mb-2 {
                    margin-bottom: 12px !important;
                }
                
                .pt-1 {
                    padding-top: 8px !important;
                }
                
                .equipo-list {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 5px;
                    margin-top: 3px;
                }
                
                .miembro-equipo {
                    background-color: #f0f0f0;
                    padding: 3px 6px; /* AUMENTADO */
                    border-radius: 3px;
                    font-size: 10px; /* AUMENTADO */
                }
                
                .codigo-tarea {
                    position: absolute;
                    top: 8px; /* AJUSTADO */
                    right: 8px; /* AJUSTADO */
                    font-size: 9px; /* AUMENTADO */
                    color: #666;
                    background-color: rgb(255, 255, 255);
                    padding: 3px 6px; /* AUMENTADO */
                    border: 1px solid #ddd;
                    border-radius: 3px;
                }
                
                .tipo-orden {
                    position: absolute;
                    top: 8px; /* AJUSTADO */
                    left: 8px; /* AJUSTADO */
                    font-size: 9px; /* AUMENTADO */
                    color: #fff;
                    background-color: #6c757d;
                    padding: 3px 6px; /* AUMENTADO */
                    border-radius: 3px;
                }
                
                @media print {
                    body { 
                        padding: 10mm;
                        font-size: 11px !important;
                    }
                    .orden-trabajo {
                        border: none;
                        border-bottom: 1px dashed #ccc;
                        margin-bottom: 8mm;
                        padding-bottom: 5mm;
                    }
                    .no-print { display: none !important; }
                    .membrete img {
                        max-height: 70px;
                    }
                    .compact-table th, .compact-table td {
                        padding: 3px 2px;
                    }
                }
            </style>
        </head>
        <body>
            <button class="btn btn-primary no-print" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
    `;

    // 3. Generar las órdenes por equipo
    Object.values(equiposUnicos).forEach((equipo, index) => {
        // Generar ID único para la orden con formato: YYYYMMDD-IDTAREA
        const primeraTarea = equipo.tareas[0];
        const fechaFormateada = new Date().toISOString().split('T')[0].replace(/-/g, '');
        const ordenId = `ORD-${fechaFormateada}-${primeraTarea.id}`;
        
        // Obtener supervisor de la primera validación si existe
        const supervisor = primeraTarea.validaciones && primeraTarea.validaciones.length > 0 
            ? primeraTarea.validaciones[0].supervisor 
            : 'Sin supervisor asignado';
        
        htmlCompleto += ` 
            <div class="orden-trabajo" style="position: relative;">
                <!-- MEMBRETE CON IMAGEN -->
                <div class="membrete">
                    <img src="Imagen1.jpg" alt="Membrete Institucional">
                </div>
                
                <div class="codigo-tarea">ID: ${ordenId}</div>
                <div class="tipo-orden">${primeraTarea.area_nombre}</div>
                
                <!-- Encabezado mejorado -->
                <div class="text-center mb-2">
                    <h4 class="mb-0">DIRECCIÓN DE SERVICIOS GENERALES</h4>
                    <h5 class="mb-1">ORDEN DE TRABAJO</h5>
                    <div style="font-size: 11px;">
                        <span>Fecha: ${new Date().toLocaleDateString()}</span>
                        <span> | </span>
                        <span>Hora: ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                </div>

                <!-- Datos en cajas separadas - MEJORADO -->
                <div class="d-flex flex-wrap mb-2" style="font-size: 11px;">
                    <div class="info-box">
                        <strong>Equipo:</strong> 
                        <div class="equipo-list">
                            ${equipo.personal.map(p => 
                                `<span class="miembro-equipo">${p.nombre_completo} (${p.cargo} - ${p.departamento})</span>`
                            ).join('')}
                        </div>
                    </div>
                    <div class="info-box"><strong>Área:</strong> ${primeraTarea.area_nombre}</div>
                    <div class="info-box"><strong>División:</strong> ${primeraTarea.departamento_nombre}</div>
                    <div class="info-box"><strong>Turno:</strong> ${primeraTarea.turno || 'No especificado'}</div>
                </div>

                <!-- Tabla de tareas con columna de materiales - MEJORADA -->
                <div class="mb-2">
                    <h6 style="font-size: 12px; font-weight: bold; border-bottom: 1px solid #ddd; padding-bottom: 3px;">
                        TAREAS ASIGNADAS
                    </h6>
                    <table class="table compact-table mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th width="5%">#</th>
                                <th width="45%">Descripción</th>
                                <th width="20%">Materiales</th>
                                <th width="15%">Fecha/Hora</th>
                                <th width="15%">Firma</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${equipo.tareas.map((tarea, i) => `
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${tarea.descripcion}</td>
                                    <td>
                                        ${tarea.materiales ? tarea.materiales.map(m => 
                                            `${m.nombre} (${m.cantidad} ${m.medida})`).join(', ') : 'N/A'}
                                    </td>
                                    <td>${tarea.fecha_inicio_formateada}</td>
                                    <td style="border-bottom: 1px dashed #ccc;"></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <!-- Responsable y observaciones - MEJORADO -->
                <div class="d-flex justify-content-between border-top pt-2" style="font-size: 11px;">
                    <div style="width: 65%">
                        <strong>Observaciones:</strong>
                        <div style="border-bottom: 1px dashed #ccc; min-height: 30px; margin-top: 5px;"></div>
                    </div>
                    <div style="width: 30%">
                        <div class="text-center">
                            <strong>Responsable</strong>
                            <div style="border-bottom: 1px solid #000; width: 100%; margin: 8px 0;"></div>
                            <div><strong>Supervisor:</strong> ${supervisor}</div>
                        </div>
                    </div>
                </div>
                
                <!-- PIE DE PÁGINA -->
                <div class="pie-pagina">
                    Pertenencia Social y Participación Popular
                </div>
            </div>
            
            ${index < Object.keys(equiposUnicos).length - 1 ? '<div style="page-break-after: always;"></div>' : ''}
        `;
    });

    htmlCompleto += `
        </body>
        </html>
    `;

    // 4. Abrir ventana de vista previa (sin imprimir automáticamente)
    const ventanaImpresion = window.open('', '_blank');
    ventanaImpresion.document.write(htmlCompleto);
    ventanaImpresion.document.close();
}

// Ejemplo de uso con dos órdenes de trabajo
function mostrarEjemplo() {
    const tareasEjemplo = [
        // Orden 1: Un solo técnico con una sola tarea simple
        {
            id: 501,
            descripcion: "Cambio de bombillo en pasillo principal",
            departamento: "Electricidad",
            area: "Pasillo Central",
            personal: [
                {
                    id: 50,
                    nombre_completo: "Mario Silva",
                    cargo: "Electricista"
                }
            ]
        },
        
        // Orden 2: Un técnico con múltiples tareas en diferentes áreas
        {
            id: 502,
            descripcion: "Reparación de ventana en oficina de gerencia",
            departamento: "Carpintería",
            area: "Oficinas Gerenciales",
            personal: [
                {
                    id: 51,
                    nombre_completo: "Carlos Andrade",
                    cargo: "Carpintero"
                }
            ]
        },
        {
            id: 503,
            descripcion: "Instalación de estante en almacén",
            departamento: "Carpintería",
            area: "Almacén 2",
            personal: [
                {
                    id: 51,
                    nombre_completo: "Carlos Andrade",
                    cargo: "Carpintero"
                }
            ]
        },
        
        // Orden 3: Equipo de trabajo (2 personas) en una tarea compleja
        {
            id: 504,
            descripcion: "Mantenimiento mayor a compresor de aire",
            departamento: "Mantenimiento",
            area: "Sala de Máquinas",
            personal: [
                {
                    id: 52,
                    nombre_completo: "Luisa Fernández",
                    cargo: "Técnico Mecánico"
                },
                {
                    id: 53,
                    nombre_completo: "Jorge Rojas",
                    cargo: "Ayudante Especializado"
                }
            ]
        },
        
        // Orden 4: Técnico con tarea que requiere materiales especiales
        {
            id: 505,
            descripcion: "Aplicación de pintura anti-corrosiva en estructura",
            departamento: "Pintura",
            area: "Área de Producción",
            materiales: "Pintura epóxica, brochas, thinner",
            personal: [
                {
                    id: 54,
                    nombre_completo: "Ana Martínez",
                    cargo: "Pintor Industrial"
                }
            ]
        },
        
        // Orden 5: Técnico compartido entre departamentos
        {
            id: 506,
            descripcion: "Reparación de puerta eléctrica",
            departamento: "Electricidad",
            area: "Entrada Principal",
            personal: [
                {
                    id: 55,
                    nombre_completo: "Pedro Vásquez",
                    cargo: "Técnico Multifuncional"
                }
            ]
        },
        {
            id: 507,
            descripcion: "Ajuste de cerradura mecánica",
            departamento: "Herrería",
            area: "Oficina de Recursos Humanos",
            personal: [
                {
                    id: 55,
                    nombre_completo: "Pedro Vásquez",
                    cargo: "Técnico Multifuncional"
                }
            ]
        },
        
        // Orden 6: Equipo completo para proyecto grande
        {
            id: 508,
            descripcion: "Instalación de nuevo sistema de ventilación",
            departamento: "Mantenimiento",
            area: "Planta Completa",
            personal: [
                {
                    id: 56,
                    nombre_completo: "Ricardo Mora",
                    cargo: "Supervisor de Mantenimiento"
                },
                {
                    id: 57,
                    nombre_completo: "Sofía Jiménez",
                    cargo: "Técnico en HVAC"
                },
                {
                    id: 58,
                    nombre_completo: "Diego Cordero",
                    cargo: "Ayudante"
                }
            ]
        }
    ];

    generarVistaPreviaImpresion(tareasEjemplo);
}

// En tu modal, llamarías esto al hacer clic en "Vista Previa":
$('#btn-generar-preview').click(function() {
  alert("Generando vista previa...");
   /*  const selectedIds = obtenerIdsSeleccionados(); // Tu función para obtener IDs
    const tareasSeleccionadas = obtenerTareasPorIds(selectedIds); // Obtener objetos completos
    
    if (tareasSeleccionadas.length === 0) {
        alert("Selecciona al menos una tarea");
        return;
    }
    
    generarVistaPreviaImpresion(tareasSeleccionadas); */
    mostrarEjemplo();
});
    
}

  //------------------------------- PERIODICIDAD ---------------------------------------
  /*    $('#periodicidad').change(function() {
                const periodicidad = $(this).val();
                
                // Ocultar todos los contenedores primero
                $('#dias-semana-container, #dia-mes-container').hide();
                
                // Mostrar los contenedores relevantes
                if (periodicidad === 'semanal') {
                    $('#dias-semana-container').show();
                } else if (['mensual', 'trimestral', 'anual'].includes(periodicidad)) {
                    $('#dia-mes-container').show();
                }
            });
                
            // Validación del formulario
            $('#form-config-tarea').submit(function(e) {
                e.preventDefault();
                
                // Validación básica
                if (!$('#periodicidad').val()) {
                    $('#periodicidad').addClass('is-invalid');
                    return false;
                }
                
                // Enviar el formulario
                $.post($(this).attr('action'), $(this).serialize(), function(response) {
                    if (response.success) {
                        // Cerrar el modal y recargar la tabla
                        $('#modal-generico').modal('hide');
                        // Aquí podrías mostrar un mensaje de éxito o recargar la tabla
                        alert('Configuración guardada correctamente');
                        // location.reload(); // O recargar solo la tabla de tareas comunes
                    } else {
                        alert('Error: ' + response.message);
                    }
                }).fail(function() {
                    alert('Error al guardar la configuración');
                });
            }); */

  //---------------------------------------------------- FIN PERIODICIDAD -------------------------------------

  function CargaModalComponentsGenerico(modalId) {
    console.log("Modal evaluacion se muestra");

    //----------------------------------DATOS DE EVALUACION---------------------------

    //--------------------------------------------- INICIO EVALUACIÓN --------------------------------------------//

      // Habilitar/deshabilitar sección de director según aprobación del supervisor
      $("#aprobacion-supervisor").change(function () {
        if ($(this).is(":checked")) {
          // Mostrar el formulario del director y ocultar el mensaje
          $("#contenido-director").show();
          $("#mensaje-director").hide();

          // Actualizar el badge
          $("#seccion-director .badge")
            .removeClass("bg-secondary")
            .addClass("bg-info")
            .text("Pendiente");
        } else {
          // Ocultar el formulario del director y mostrar el mensaje
          $("#contenido-director").hide();
          $("#mensaje-director").show();

          // Resetear los valores del formulario del director
          $("#ponderacion-director").val("");
          $("#comentarios-director").val("");
          $("#aprobacion-director").prop("checked", false);

          // Actualizar el badge
          $("#seccion-director .badge")
            .removeClass("bg-info")
            .addClass("bg-secondary")
            .text("No disponible");
        }
        actualizarProgreso();
      });

      // Guardar evaluación
// Guardar evaluación
$("#btn-guardar-evaluacion").click(function () {
  console.log("Guardar evaluación");

 /*  // Validación de confirmación
  if (!$("#confirmacion-evaluacion").is(":checked")) {
    mostrarError("Debe confirmar que la información es correcta");
    return;
  }

  // Validar evaluación del supervisor si está pendiente
  if ($("#seccion-supervisor .badge").text() === "Pendiente") {
    if (!$("#ponderacion-supervisor").val()) {
      mostrarError("Seleccione una ponderación para la evaluación del supervisor");
      return;
    }

    if (!$("#aprobacion-supervisor").is(":checked")) {
      if (!confirm("La tarea no será aprobada. ¿Desea continuar?")) {
        return;
      }
    }
  }

  // Eliminamos la validación obligatoria para el director
  // Solo validamos si la sección existe y está pendiente, pero no es obligatorio
  if (
    $("#seccion-director").length &&
    $("#seccion-director .badge").text() === "Pendiente"
  ) {
    if (!$("#ponderacion-director").val()) {
      if (!confirm("No ha seleccionado ponderación para el director. ¿Desea continuar sin esta evaluación?")) {
        return;
      }
    } else if (!$("#aprobacion-director").is(":checked")) {
      if (!confirm("La tarea no será aprobada definitivamente. ¿Desea continuar?")) {
        return;
      }
    }
  } */

  // Si llegamos aquí, todas las validaciones pasaron
  enviarEvaluacion();
});

      // Función para enviar la evaluación
      function enviarEvaluacion() {
        const formData = new FormData($("#form-evaluacion")[0]);

        // Recolectar materiales dinámicamente (si existen)
        const materiales = [];
        if ($("#tabla-materialesDevueltos").length) {
          $("#tabla-materialesDevueltos tbody tr").each(function () {
            const row = $(this);
            materiales.push({
              id: row.data("id"),
              utilizado: row.find("input").eq(1).val(),
              devuelto: row.find("input").eq(2).val(),
            });
          });
        }

        console.log("Materiales a enviar:", materiales);

        // Agregar datos adicionales al FormData
        formData.append("materiales", JSON.stringify(materiales));
        formData.append("idTarea", modalId);
        console.log("ID de tarea:", modalId);

        // Enviar la evaluación
        $.ajax({
          url: "Tareas/Evaluar",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
            if (response.success) {
              mostrarExito(response.message);
              $("#modal-evaluar-tarea").modal("hide");
              if (typeof tablaActivas !== "undefined") {
                tablaActivas.ajax.reload();
              $("#modal-evaluar").modal("hide");
              }
            } else {
              if (response.errors) {
                response.errors.forEach(mostrarError);
              } else {
                mostrarError("Ocurrió un error al procesar la evaluación");
              }
            }
          },
          error: function (xhr, status, error) {
            mostrarError("Error al enviar la evaluación");
            console.error("Error en AJAX:", error);
            console.log("Respuesta del servidor:", xhr.responseText);
          },
        });
      }

      // Cargar datos al abrir el modal
      //----------------------------------Revisar por si esto esta dando un problema
      //cargarDatosTarea();

    //--------------------------------------------------------- FIN EVALUACIÓN --------------------------------------------//
  }


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
  //-------------------------------------------------Modal Orden-------------------------------

  $(document).on("show.bs.modal", "#modal-orden", function (e) {
    const modal = $(this); // <<<<<< IMPORTANTE
    const button = $(e.relatedTarget);
    url = button.data("bs-url");
    const valorId = button.data("valor");
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

          obtenerOrdenPorId(valorId);
        },
        error: function () {},
      });
    }
    /* if (!url || url.indexOf('/') === -1) {
                url = '/DSG-Appweb/Tareas/Orden';
                return;
            } */
  });

  //--------------------------------------------------- Modal Tareas ----------------------------
  $(document).on("show.bs.modal", "#modal-tareas", function (e) {
    const modal = $(this); // <<<<<< IMPORTANTE
    const button = $(e.relatedTarget);
    const url = button.data("bs-url");

    // Cargar el contenido del modal
    modal.find(".modal-content").load(url, function () {
      initModalComponents(modal); // pasa el modal como parámetro
    });
  });

  function initModalComponents(modal) {
    console.log("Modal se muestra");

    // Limpieza al cerrar
    modal.on("hidden.bs.modal", function () {
      console.log("Modal cerrado");
      modal.removeData("bs.modal").find(".modal-content").empty();
    });

    // Inicializa select2
    modal.find("#area").select2({
      placeholder: "Seleccione un área",
      allowClear: false,
      width: "100%",
      dropdownParent: modal.find("#area").parent(),
    });

    modal.find("#personal").select2({
      placeholder: "Busque y seleccione personal",
      allowClear: true,
      width: "100%",
      closeOnSelect: false,
      dropdownParent: modal.find("#personal").parent(),
    });

    modal.find("#supervisor").select2({
      placeholder: "Busque y seleccione personal",
      allowClear: true,
      width: "100%",
      closeOnSelect: false,
      dropdownParent: modal.find("#supervisor").parent(),
    });

    // Botones siguiente y anterior
    modal
      .find(".siguiente")
      .off("click")
      .on("click", function () {
        const nextTab = $(this).data("next");
        const targetTabButton = modal.find(
          `#tabs-tarea .nav-link[href="#${nextTab}"]`
        );
        targetTabButton.tab("show");
      });

    modal
      .find(".anterior")
      .off("click")
      .on("click", function () {
        const prevTab = $(this).data("prev");
        const targetTabButton = modal.find(
          `#tabs-tarea .nav-link[href="#${prevTab}"]`
        );
        targetTabButton.tab("show");
      });

    // Formulario
    const formTarea = modal.find("#form-tarea");
    if (formTarea.length) {
      formTarea.off("submit").on("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const personal = modal
          .find("#personal")
          .select2("data")
          .map((item) => item.id);

        formData.delete("personal[]");
        personal.forEach((id) => formData.append("personal[]", id));

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn
          .prop("disabled", true)
          .html('<i class="fa fa-spinner fa-spin"></i> Procesando...');

        $.ajax({
          url: "Tareas/Registrar",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
            if (response.success) {
              mostrarExito(response.message);
              tablaActivas.ajax.reload();

              obtenerOrdenPorId(response.data.id);

              $("#modal-tareas").modal("hide");
            } else {
              if (response.errors?.length) {
                response.errors.forEach(mostrarError);
              } else {
                mostrarError(response.message);
              }
              submitBtn
                .prop("disabled", false)
                .html('<i class="fa-solid fa-check me-2"></i> Guardar Tarea');
            }
          },
          error: function (xhr, status, error) {
            console.error("Error en la petición:", error);
            mostrarError("Error al comunicarse con el servidor");
            submitBtn
              .prop("disabled", false)
              .html('<i class="fa-solid fa-check me-2"></i> Guardar Tarea');
          },
        });
      });
    } else {
      console.warn("Formulario con ID 'form-tarea' no encontrado en el modal.");
    }

    //-------------------------------------- FILTRAR EN PERSONAL----------------------------------
    // Escuchar cambios en el select de departamento
    /* $('#departamento').change(function() {
        var idDepartamento = $(this).val();
        
        // Filtrar el select de personal (multiple)
        $('#personal option').each(function() {
            var optionDeptoId = $(this).data('departamento');
            if (optionDeptoId == idDepartamento) {
                $(this).show();
            } else {
                $(this).hide();
                $(this).prop('selected', false); // Deseleccionar si estaba seleccionado
            }
        });
        $('#personal').trigger('change.select2');
        // Filtrar el select de supervisor
        $('#supervisor option').each(function() {
            var optionDeptoId = $(this).data('departamento');
            if (optionDeptoId == idDepartamento) {
                $(this).show();
            } else {
                $(this).hide();
                $(this).prop('selected', false); // Deseleccionar si estaba seleccionado
            }
        });
        
        // Actualizar los select2 para reflejar los cambios
        $('#personal').trigger('change.select2');
        $('#supervisor').trigger('change.select2');
    });
    
    // Disparar el evento change al cargar si ya hay un departamento seleccionado
    if ($('#departamento').val()) {
        $('#departamento').trigger('change');
    } */

    // Guardar copias originales
    const originalPersonalOptions = $("#personal").html();
    const originalSupervisorOptions = $("#supervisor").html();

    $("#departamento").change(function () {
      const idDepartamento = $(this).val();

      // Restaurar opciones originales
      $("#personal").html(originalPersonalOptions);
      $("#supervisor").html(originalSupervisorOptions);

      // Filtrar PERSONAL (NO SUPERVISORES) por departamento
      $("#personal option").each(function() {
        const $option = $(this);
        const deptMatch = $option.data('departamento') == idDepartamento;
        const cargo = ($option.data('cargo') || '').toLowerCase();
        const esSupervisor = cargo.includes('supervisor');
        
        // Eliminar si: no coincide con departamento O es supervisor
        if (!deptMatch || esSupervisor) {
          $option.remove();
        }
      });

      // Filtrar SUPERVISORES por departamento
      $("#supervisor option").each(function() {
        const $option = $(this);
        const deptMatch = $option.data('departamento') == idDepartamento;
        const cargo = ($option.data('cargo') || '').toLowerCase();
        const esSupervisor = cargo.includes('supervisor');
        
        // Eliminar si: no coincide con departamento O NO es supervisor
        if (!deptMatch || !esSupervisor) {
          $option.remove();
        }
      });

      // Actualizar Select2
      $("#personal, #supervisor").trigger("change.select2");
    });
    //------------------------------------------------------- MATERIALES ----------------------------------------
    // Variable para almacenar los materiales seleccionados
    let materialesSeleccionados = [];
    console.log("Estoy en tareas");
    // Inicializar DataTable para materiales disponibles
    const tablaMateriales = $("#tabla-materiales").DataTable({
      dom: '<"top"f>rt<"bottom"lip><"clear">',
      ajax: {
        url: "Tareas/Materiales",
        type: "GET",
        dataSrc: function (json) {
          // Aquí puedes procesar los datos antes que DataTables los use
          setTimeout(actualizarCategorias, 0); // Usamos setTimeout para asegurar que DataTables ya procesó los datos
          return json;
        },
      },
      columns: [
        { data: "id" },
        { data: "nombre" },
        { data: "categoria" },
        { data: "unidad" },
        { data: "disponible" },
        {
          data: null,
          render: function (data, type, row) {
            return `<input type="number" class="form-control form-control-sm cantidad-material" 
                                    max="${row.disponible}" step="1" value="0"
                                    onfocus="this.value = this.value.replace(/^0+/, '')">`;
          },
        },
        {
          data: null,
          render: function (data, type, row) {
            $('[data-bs-toggle="tooltip"]').tooltip();
            return `
                <button type="button" 
                        class="btn btn-sm btn-primary agregar-material" 
                        data-id="${row.id}"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Agregar material">
                    <i class="fa-solid fa-plus"></i>
                </button>`;
          },
        },
      ],
      language: {
        //   url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
      },
      responsive: true,
    });

    function actualizarCategorias() {
      // Vaciar el select primero
      $("#categoria").empty();

      // Opción por defecto (opcional)
      $("#categoria").append('<option value="">Todas las categorías</option>');

      // Obtener categorías únicas de la tabla
      const datos = tablaMateriales.data().toArray();
      const categoriasUnicas = [
        ...new Set(datos.map((item) => item.categoria)),
      ];

      // Llenar el select con las categorías
      categoriasUnicas.forEach((categoria) => {
        $("#categoria").append(
          `<option value="${categoria}">${categoria}</option>`
        );
      });
    }

    // Llamar la función cuando se cargue la tabla por primera vez

    // actualizarCategorias();

    console.log(tablaMateriales);
    // Filtrar materiales
    $("#buscar-material").on("keyup", function () {
      tablaMateriales.search(this.value).draw();
    });

    $("#categoria").on("change", function () {
      if (this.value === "") {
        tablaMateriales.columns(2).search("").draw();
      } else {
        tablaMateriales.columns(2).search(this.value).draw();
      }
    });

    // Agregar material a la selección
    $("#tabla-materiales tbody").on("click", ".agregar-material", function (e) {
      e.preventDefault();
      const rowData = tablaMateriales.row($(this).closest("tr")).data();
      const cantidad = parseFloat(
        $(this).closest("tr").find(".cantidad-material").val()
      );

      if (isNaN(cantidad) || cantidad <= 0) {
        mostrarError("Ingrese una cantidad válida");
        return;
      }

      const materialExistente = materialesSeleccionados.find(
        (m) => m.id == rowData.id
      );

      const totalCantidad = materialExistente
        ? materialExistente.cantidad + cantidad
        : cantidad;

      if (totalCantidad > rowData.disponible) {
        mostrarError(
          `No hay suficiente stock. Disponible: ${rowData.disponible} ${rowData.unidad}`
        );
        return;
      }

      if (materialExistente) {
        materialExistente.cantidad = totalCantidad;
        if (totalCantidad > 0) {
          mostrarExito("Material agregado con éxito");
        } else {
          mostrarError("Introduzca una cantidad válida");
        }
      } else {
        materialesSeleccionados.push({
          id: rowData.id,
          nombre: rowData.nombre,
          cantidad: cantidad,
          unidad: rowData.unidad,
        });
        mostrarExito("Material agregado con éxito");
      }

      actualizarTablaSeleccionados();
    });

    // Quitar material de la selección
    $("#tabla-seleccionados").on("click", ".quitar-material", function () {
      const id = $(this).data("id");
      materialesSeleccionados = materialesSeleccionados.filter(
        (m) => m.id != id
      );
      actualizarTablaSeleccionados();
    });

    // Función para actualizar la tabla de seleccionados

    function actualizarTablaSeleccionados() {
      const tbody = $("#tabla-seleccionados tbody");
      tbody.empty();

      if (materialesSeleccionados.length === 0) {
        tbody.append(
          '<tr><td colspan="4" class="text-center text-muted py-3">No hay materiales seleccionados</td></tr>'
        );
        return;
      }

      materialesSeleccionados.forEach((material) => {
        const row = `
                        <tr data-id="${material.id}">
                            <td>${material.nombre}</td>
                            <td>${material.cantidad}</td>
                            <td>${material.unidad}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger quitar-material" data-id="${material.id}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
        tbody.append(row);
      });

      // Actualizar campo oculto del formulario con los materiales seleccionados
      $("#materiales-seleccionados").val(
        JSON.stringify(materialesSeleccionados)
      );
    }

    // Inicializar tabla de seleccionados vacía
    actualizarTablaSeleccionados();

    // Agregar campo oculto al formulario para enviar los materiales
    $("#form-tarea").append(
      '<input type="hidden" name="materiales" id="materiales-seleccionados" value="">'
    );

    //--------------------------------------------------------- FIN MATERIALES --------------------------------------------//
  }
});
