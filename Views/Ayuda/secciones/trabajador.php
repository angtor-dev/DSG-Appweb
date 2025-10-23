<!-- seccion de ayda para la gestion de trabajadores -->
<section class="ayuda-seccion">
    <div class="ayuda-header" id="gestionar-trabajadores">Gestionar Trabajadores</div>
    <div class="ayuda-texto">
        <p>El <a href="<?= LOCAL_DIR ?>/Trabajadores">módulo de Trabajadores</a> le permite gestionar el personal de su sistema, incluyendo el registro de nuevos empleados, la actualización de su información y la eliminación de registros.</p>
    </div>
    <div class="ayuda-sub-header" id="registrar-trabajador">Registrar trabajador</div>
    <div class="ayuda-texto">
        <p>Para registrar un nuevo trabajador en el sistema, siga los pasos a continuación:</p>
        <ol>
            <li>
                <b>Acceso a la función de registro</b>
                <ul>
                    <li>Ingrese al módulo de Trabajadores.</li>
                    <li>Si tiene los permisos necesarios, verá un botón "Nuevo Trabajador" en la esquina superior derecha de la pantalla. Si no lo ve, no tiene los permisos para realizar esta acción.</li>
                    <li>Haga clic en el botón "Nuevo Trabajador".</li>
                </ul>
            </li>
            <li>
                <b>Formulario de registro:</b>
                <ul>
                    <li>Primero, ingrese la cédula del trabajador en el campo correspondiente.</li>
                    <li>Si la cédula es válida y no está registrada previamente, los demás campos del formulario (nombre, apellido, teléfono, división, cargo, turno y fecha de ingreso) se habilitarán para que pueda llenarlos.</li>
                    <li>Si la cédula es inválida o ya se encuentra registrada en el sistema, aparecerá un mensaje de error en la parte inferior del campo de cédula. Deberá corregir la cédula o verificar si el trabajador ya está registrado.</li>
                    <li>Complete todos los campos requeridos con la información del trabajador.</li>
                </ul>
            </li>
            <li>
                <b>Confirmación y finalización del registro:</b>
                <ul>
                    <li>Una vez que haya llenado el formulario, haga clic en el botón "Registrar" ubicado en la parte inferior derecha del formulario.</li>
                    <li>El sistema mostrará un indicador de carga mientras guarda la información.</li>
                    <li>Al finalizar, verá un mensaje de "Registro exitoso" si la operación se completó correctamente, o un mensaje de error si hubo algún problema.</li>
                    <li>La tabla de trabajadores se actualizará automáticamente para mostrar el nuevo registro.</li>
                </ul>
            </li>
            
        </ol>
    </div>
    <div class="ayuda-sub-header" id="modificar-trabajador">Modificar trabajador</div>
    <div class="ayuda-texto">
        <p>Para modificar la información de un trabajador existente:</p>
        <ol>
            <li>
                <b>Acceso a la función de actualización:</b>
                <ul>
                    <li>Al ingresar al módulo de Trabajadores, verá una tabla que muestra una lista de los trabajadores registrados y activos en el sistema.</li>
                    <li>Si tiene los permisos de modificación, notará un botón con el icono de un lápiz en la última columna de cada fila de la tabla.</li>
                    <li>Haga clic en el botón del lápiz correspondiente al trabajador cuya información desea actualizar.</li>
                </ul>
            </li>
            <li>
                <b>Formulario de actualización:</b>
                <ul>
                    <li>Se abrirá una ventana emergente (modal) con un formulario similar al de registro. Este formulario ya estará pre-cargado con los datos actuales del trabajador seleccionado.</li>
                    <li>Realice las modificaciones necesarias en los campos deseados.</li>
                </ul>
            </li>
            <li>
                <b>Guardar cambios:</b>
                <ul>
                    <li>Una vez que haya realizado los cambios, haga clic en el botón "Modificar" en la parte inferior derecha del formulario.</li>
                    <li>El sistema mostrará un indicador de carga mientras se guardan las modificaciones.</li>
                    <li>Verá un mensaje de éxito si los cambios se guardaron correctamente, o un mensaje de error si hubo algún problema.</li>
                    <li>La tabla de trabajadores se actualizará para reflejar la información modificada.</li>
                </ul>
            </li>
        </ol>
    </div>
    <div class="ayuda-sub-header" id="eliminar-trabajador">Eliminar trabajador</div>
    <div class="ayuda-texto">
        <p>Para eliminar un registro de trabajador del sistema:</p>
        <ol>
            <li>
                <b>Acceso a la función de eliminación:</b>
                <ul>
                    <li>En la tabla del módulo de Trabajadores, si tiene los permisos necesarios, verá un botón con el icono de una papelera en la última columna de cada fila, junto al icono de lápiz.</li>
                    <li>Haga clic en el botón de la papelera correspondiente al trabajador que desea eliminar.</li>
                </ul>
            </li>
            <li>
                <b>Confirmación de eliminación:</b>
                <ul>
                    <li>El sistema mostrará una ventana de confirmación para asegurarse de que desea realizar esta acción. Esto es para evitar eliminaciones accidentales.</li>
                    <li>Si está seguro de que desea eliminar el registro, confirme la acción.</li>
                </ul>
            </li>
            <li>
                <b>Proceso de eliminación:</b>
                <ul>
                    <li>Una vez confirmada la eliminación, el sistema mostrará un indicador de carga mientras intenta eliminar el registro del trabajador.</li>
                    <li>Finalmente, recibirá un mensaje de éxito si el trabajador fue eliminado correctamente, o un mensaje de error si la operación no pudo completarse.</li>
                </ul>
            </li>
        </ol>
    </div>

</section>


