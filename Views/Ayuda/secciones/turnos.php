<section class="ayuda-seccion">
    <div class="ayuda-header" id="gestionar-turnos">Gestionar Turnos</div>
    <div class="ayuda-texto">
        <p>El <a href="<?= LOCAL_DIR ?>/Turnos">módulo de Turnos</a> le permite gestionar los turnos de los trabajadores de su sistema, incluyendo el registro de nuevos turnos, la actualización de su información y la eliminación de registros.</p>
    </div>
    <div class="ayuda-sub-header" id="registrar-turno">Registrar turno</div>
    <div class="ayuda-texto">
        <p>Para registrar un nuevo turno en el sistema, siga los pasos a continuación:</p>
        <ol>
            <li>
                <b>Acceso a la función de registro:</b>
                <ul>
                    <li>Ingrese al módulo de Turnos.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-aside.png" alt=""></li>

                    
                    <li>Si tiene los permisos necesarios, verá un botón "Nuevo Turno" en la esquina superior derecha de la
                        pantalla. Si no lo ve, no tiene los permisos para realizar esta acción.</li>
                    <li class="list-no-decor">
                        <img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-boton-nuevo.png" alt="">
                    </li>
                    <li>Haga clic en el botón "Nuevo Turno".</li>
                </ul>
            </li>
            <li>
                <b>Formulario de registro:</b>
                <ul>
                    <li>Aparecerá una ventana emergente (modal) con el formulario de registro.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-nuevo-turno-modal.png" alt=""></li>
                    <li>Ingrese el nombre descriptivo del turno (ej. "Mañana", "Tarde", "Noche") en el campo
                        correspondiente.</li>
                    <li>Seleccione la hora de entrada y la hora de salida del turno (ej. 8:00 y 12:00).</li>
                    <li>Marque los días de la semana en los que este turno es laborable utilizando los checkboxes provistos.
                    </li>
                </ul>
            </li>
            <li>
                <b>Confirmación y finalización del registro:</b>
                <ul>

                    <li>Una vez que haya llenado el formulario, haga clic en el botón "Registrar" ubicado en la parte
                        inferior derecha del formulario.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-nuevo-boton-registrar.png" alt=""></li>
                    <li>El sistema mostrará un indicador de carga mientras guarda la información.</li>
                    <li>Al finalizar, verá un mensaje de "Registro exitoso" si la operación se completó correctamente, o un
                        mensaje de error si hubo algún problema.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-registro-exitoso.png" alt=""></li>
                    <li>La tabla de turnos se actualizará automáticamente para mostrar el nuevo registro.</li>
                </ul>
            </li>
        </ol>
    </div>
    <div class="ayuda-sub-header" id="modificar-turno">Modificar turno</div>
    <div class="ayuda-texto">
        <p>Para modificar la información de un turno existente:</p>
        <ol>
            <li>
                <b>Acceso a la función de actualización:</b>
                <ul>
                    <li>Al ingresar al módulo de Turnos, verá una tabla que muestra una lista de los turnos registrados en el sistema.</li>
                    <li>Si tiene los permisos de modificación, notará un botón con el icono de un lápiz en la última columna de cada fila de la tabla.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-editar-boton.png" alt=""></li>
                    <li>Haga clic en el botón del lápiz correspondiente al turno cuya información desea actualizar.</li>
                </ul>
            </li>
            <li>
                <b>Formulario de actualización:</b>
                <ul>
                    <li>Se abrirá una ventana emergente (modal) con un formulario similar al de registro. Este formulario ya estará precargado con los datos actuales del turno seleccionado.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-editar-modal.png" alt=""></li>
                    <li>Realice las modificaciones necesarias en los campos deseados.</li>
                </ul>
            </li>
            <li>
                <b>Guardar cambios:</b>
                <ul>
                    <li>Una vez que haya realizado los cambios, haga clic en el botón "Modificar" en la parte inferior derecha del formulario.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-editar-boton-modificar.png" alt=""></li>
                    <li>El sistema mostrará un indicador de carga mientras se guardan las modificaciones.</li>
                    <li>Verá un mensaje de éxito si los cambios se guardaron correctamente, o un mensaje de error si hubo algún problema.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-editar-exitoso.png" alt=""></li>
                    <li>La tabla de turnos se actualizará para reflejar la información modificada.</li>
                </ul>
            </li>
        </ol>
    </div>
    <div class="ayuda-sub-header" id="eliminar-turno">Eliminar turno</div>
    <div class="ayuda-texto">
        <p>Para eliminar un registro de turno del sistema:</p>
        <ol>
            <li>
                <b>Acceso a la función de eliminación:</b>
                <ul>
                    <li>En la tabla del módulo de Turnos, si tiene los permisos necesarios, verá un botón con el icono de una papelera en la última columna de cada fila, junto al icono de lápiz.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-eliminar-boton.png" alt=""></li>
                    <li>Haga clic en el botón de la papelera correspondiente al turno que desea eliminar.</li>
                </ul>
            </li>
            <li>
                <b>Confirmación de eliminación:</b>
                <ul>
                    <li>El sistema mostrará una ventana de confirmación para asegurarse de que desea realizar esta acción. Esto es para evitar eliminaciones accidentales.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-eliminar-modal.png" alt=""></li>
                    <li>Si está seguro de que desea eliminar el registro, confirme la acción.</li>
                </ul>
            </li>
            <li>
                <b>Proceso de eliminación:</b>
                <ul>
                    <li>Una vez confirmada la eliminación, el sistema mostrará un indicador de carga mientras intenta eliminar el registro del turno.</li>
                    <li>Finalmente, recibirá un mensaje de éxito si el turno fue eliminado correctamente, o un mensaje de error si la operación no pudo completarse.</li>
                    <li class="list-no-decor"><img src="<?= LOCAL_DIR ?>/public/img/ayuda/turnos-eliminar-exitoso.png" alt=""></li>
                    <li>La tabla de turnos se actualizará automáticamente para mostrar el nuevo registro.</li>
                </ul>
            </li>
        </ol>
    </div>


</section> 