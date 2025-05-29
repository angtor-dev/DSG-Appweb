function cancelarTarea(id, element) {
  if (!confirm("¿Estás seguro de cancelar esta tarea?")) return;

  const icon = $(element).find("i");
  icon.removeClass("fa-ban").addClass("fa-spinner fa-spin");
  //----------------------------------- Cancelar tarea -----------------------
  $.ajax({
    url: "Tareas/Cancelar", // Asegúrate que esta ruta coincida con tu enrutamiento
    type: "POST",
    data: { id: id },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        $(".datatable").DataTable().ajax.reload();
        toastr.success(response.message);
      } else {
        toastr.error(response.message);
      }
    },
    error: function (xhr) {
      toastr.error("Error al contactar el servidor");
    },
    complete: function () {
      icon.removeClass("fa-spinner fa-spin").addClass("fa-ban");
    },
  });
}
function renderButtons(row) {
  let buttons = '<div class="d-flex gap-2">';

  // Botones específicos por estado
  switch (row.estado.toLowerCase()) {
    case "activo":
      buttons += `
                    

                        <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Ver Detalles">
                            <div data-bs-toggle="modal" data-bs-target="#modal-orden"
                                data-bs-url="Tareas/Orden?id=${row.id}" data-valor="${row.id}">
                                <i class="fa-solid fa-fw fa-eye"></i>
                            </div>
                        </div>
                       

                    
                         <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Cancelar" 
                            onclick="cancelarTarea(${row.id}, this)">
                            <i class="fa-solid fa-ban"></i>
                        </div>
                    
                    
                    
                `;
      break;

    case "vencida":
      buttons += `
                     <?php if (tienePermiso(Modulo::TAREAS, Permiso::ACTUALIZAR)): ?>
                        <button class="btn btn-sm btn-outline-success" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modal-evaluar"
                            data-bs-url="Tareas/Evaluar?id=${row.id}" data-valor="${row.id}">
                            <i class="fas fa-check"></i> Evaluar
                        </button>
                    <?php endif; ?>
                `;
      break;

    case "cancelado":
      buttons += `
                    <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Ver Detalles">
                            <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                                data-bs-url="Tareas/Detalle?id=${row.id}">
                                <i class="fa-solid fa-fw fa-eye"></i>
                            </div>
                        </div>
                `;
      break;

    case "evaluada":
      buttons += `
                    <?php if (tienePermiso(Modulo::TAREAS, Permiso::CONSULTAR)): ?>
                        <button class="btn btn-sm btn-outline-secondary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modal-detalles"
                            data-bs-url="Tareas/Detalle?id=${row.id}" data-valor="${row.id}">
                            <i class="fas fa-eye"></i> Detalle
                        </button>
                    <?php endif; ?>
                `;
      break;

    case "comun":
      buttons += `
                    <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Configurar">
                        <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                            data-bs-url="Tareas/Configuracion?id=${row.id}">
                            <i class="fa-solid fa-fw fa-gear"></i>
                        </div>
                    </div>
                     <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar" 
                            onclick="eliminarTarea(${row.id}, this)">
                            <i class="fa-solid fa-trash"></i>
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

  if (tareaData.tarea.materiales && tareaData.tarea.materiales.length > 0) {
    tareaData.tarea.materiales.forEach((material, index) => {
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

  // Cargar plantilla base primero
  modal.find(".modal-content").load("Tareas/Orden", function () {
    // Fecha y hora
    const fechaHora = tareaData.data.tarea.fechaCreacion; // "2025-05-28 18:35:40"
    const [fecha, hora] = fechaHora.split(" ");

    // Insertar datos básicos
    $("#orden-fecha").text(fecha);
    $("#orden-hora").text(hora);
    $("#orden-departamento").text(tareaData.data.tarea.departamento_nombre);
    $("#orden-area").text(tareaData.data.tarea.area_nombre);
    $("#orden-descripcion").text(tareaData.data.tarea.descripcion);
    $("#orden-observaciones").val(tareaData.data.tarea.observaciones || "");

    // Personal asignado
    let personalHtml = "";
    if (
      tareaData.data.tarea.personal &&
      tareaData.data.tarea.personal.length > 0
    ) {
      tareaData.data.tarea.personal.forEach((persona, index) => {
        personalHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${persona.nombre} ${persona.apellido}</td>
                                <td>${persona.departamento}</td>
                                <td class="firma-placeholder" style="height: 30px;"></td>
                            </tr>
                        `;
      });
    }
    $("#personal-lista").html(personalHtml);

    // Materiales y descripción - llenar tabla tareas-lista
    let tareasHtml = "";
    // Como la descripción es única, la colocamos primero con índice 1
    tareasHtml += `
                    <tr>
                        <td>1</td>
                        <td>${tareaData.data.tarea.descripcion}</td>
                        <td>
                            <ul class="list-unstyled mb-0">
                `;

    if (
      tareaData.data.tarea.materiales &&
      tareaData.data.tarea.materiales.length > 0
    ) {
      tareaData.data.tarea.materiales.forEach((material) => {
        tareasHtml += `<li>${material.cantidad} x ${material.nombre}</li>`;
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

    // Supervisor (puede ser array con 1 elemento)
    if (
      tareaData.data.tarea.supervisor &&
      tareaData.data.tarea.supervisor.length > 0
    ) {
      const sup = tareaData.data.tarea.supervisor[0];
      // Actualiza el texto en el área de supervisor (por ejemplo, con id supervisor-nombre)
      $(".firma-placeholder")
        .next("p.mb-0")
        .text(` ${sup.nombre} ${sup.apellido}`);
    } else {
      $(".firma-placeholder").next("p.mb-0").text("Supervisor no asignado");
    }

    // Mostrar el modal
    modal.modal("show");
  });
}

//-------------------------------------- Barra de progreso -----------------------
// Función para actualizar la barra de progreso
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
}

//--------------------------------Comienza docuemtno -----------------

$(document).ready(function () {
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
      { data: "id" },
      { data: "area" },
      { data: "departamento" },
      { data: "descripcion" },
      { data: "fecha" },
      { data: "estado" },
      {
        data: null,
        render: function (data, type, row) {
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
    $("#btn-guardar-evaluacion").click(function () {
      console.log("Guardar evaluación");

      // Validación de confirmación
      if (!$("#confirmacion-evaluacion").is(":checked")) {
        alert("Debe confirmar que la información es correcta");
        return;
      }

      // Validar evaluación del supervisor si está pendiente
      if ($("#seccion-supervisor .badge").text() === "Pendiente") {
        if (!$("#ponderacion-supervisor").val()) {
          alert("Seleccione una ponderación para la evaluación del supervisor");
          return;
        }

        if (!$("#aprobacion-supervisor").is(":checked")) {
          if (!confirm("La tarea no será aprobada. ¿Desea continuar?")) {
            return;
          }
        }
      }

      // Validar evaluación del director si está habilitada
      if (
        $("#seccion-director").length &&
        $("#seccion-director .badge").text() === "Pendiente"
      ) {
        if (!$("#ponderacion-director").val()) {
          alert("Seleccione una ponderación para la evaluación del director");
          return;
        }

        if (!$("#aprobacion-director").is(":checked")) {
          if (
            !confirm(
              "La tarea no será aprobada definitivamente. ¿Desea continuar?"
            )
          ) {
            return;
          }
        }
      }

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

      // Filtrar eliminando
      $("#personal option")
        .not('[data-departamento="' + idDepartamento + '"]')
        .remove();
      $("#supervisor option")
        .not('[data-departamento="' + idDepartamento + '"]')
        .remove();

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
                                    max="${row.disponible}" step="${
              row.unidad === "Pieza" ? "1" : "0.1"
            }" value="0">`;
          },
        },
        {
          data: null,
          render: function (data, type, row) {
            return `<button type="button" class="btn btn-sm btn-primary agregar-material" data-id="${row.id}">
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
        alert("Ingrese una cantidad válida");
        return;
      }

      if (cantidad > rowData.disponible) {
        alert(
          `No hay suficiente stock. Disponible: ${rowData.disponible} ${rowData.unidad}`
        );
        return;
      }

      const materialExistente = materialesSeleccionados.find(
        (m) => m.id == rowData.id
      );

      if (materialExistente) {
        materialExistente.cantidad += cantidad;
      } else {
        materialesSeleccionados.push({
          id: rowData.id,
          nombre: rowData.nombre,
          cantidad: cantidad,
          unidad: rowData.unidad,
        });
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
