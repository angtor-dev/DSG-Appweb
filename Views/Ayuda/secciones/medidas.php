<section class="ayuda-seccion">

    <div class="ayuda-header" id="gestionar-medidas">Gestionar Medidas</div>
    <div class="ayuda-texto">
        <p>El <a href="<?= LOCAL_DIR ?>/Medidas">módulo de Medidas</a> le permite definir y administrar las diferentes unidades de medida utilizadas en su sistema, incluyendo el registro de nuevas medidas, la actualización de su información y la eliminación de registros.</p>
    </div>
    
    <div class="ayuda-sub-header" id="registrar-medida">Registrar Medida</div>
    <div class="ayuda-texto">
        <p>Para registrar una nueva unidad de medida en el sistema, siga los pasos a continuación:</p>
        <ol>
            <li>
                <b>Acceso a la función de registro:</b>
                <ul>
                    <li>Ingrese al módulo de Medidas.</li>
                    <li>Si tiene los permisos necesarios, verá un botón "Nueva Medida" en la esquina superior derecha de la pantalla. Si no lo ve, no tiene los permisos para realizar esta acción.</li>
                    <li>Haga clic en el botón "Nueva Medida".</li>
                </ul>
            </li>
            <li>
                <b>Formulario de registro:</b>
                <ul>
                    <li>Aparecerá una ventana emergente (modal) con el formulario de registro.</li>
                    <li>Ingrese el nombre de la unidad de medida (ej. "Kilogramos", "Litros", "Unidades") en el campo correspondiente.</li>
                    <li>Ingrese las siglas de la unidad de medida (ej. "kg", "L", "ud.") en el campo correspondiente.</li>
                </ul>
            </li>
            <li>
                <b>Confirmación y finalización del registro:</b>
                <ul>
                    <li>Una vez que haya llenado el formulario, haga clic en el botón "Registrar" ubicado en la parte inferior derecha del formulario.</li>
                    <li>El sistema mostrará un indicador de carga mientras guarda la información.</li>
                    <li>Al finalizar, verá un mensaje de "Registro exitoso" si la operación se completó correctamente, o un mensaje de error si hubo algún problema.</li>
                    <li>La tabla de medidas se actualizará automáticamente para mostrar el nuevo registro.</li>
                </ul>
            </li>
        </ol>
    </div>
    
    <div class="ayuda-sub-header" id="modificar-medida">Modificar Medida</div>
    <div class="ayuda-texto">
        <p>Para modificar la información de una unidad de medida existente:</p>
        <ol>
            <li>
                <b>Acceso a la función de actualización:</b>
                <ul>
                    <li>Al ingresar al módulo de Medidas, verá una tabla que muestra una lista de las medidas registradas en el sistema.</li>
                    <li>Si tiene los permisos de modificación, notará un botón con el icono de un lápiz en la última columna de cada fila de la tabla.</li>
                    <li>Haga clic en el botón del lápiz correspondiente a la medida cuya información desea actualizar.</li>
                </ul>
            </li>
            <li>
                <b>Formulario de actualización:</b>
                <ul>
                    <li>Se abrirá una ventana emergente (modal) con un formulario similar al de registro. Este formulario ya estará pre-cargado con los datos actuales de la medida seleccionada.</li>
                    <li>Realice las modificaciones necesarias en el nombre o las siglas de la unidad.</li>
                </ul>
            </li>
            <li>
                <b>Guardar cambios:</b>
                <ul>
                    <li>Una vez que haya realizado los cambios, haga clic en el botón "Modificar" en la parte inferior derecha del formulario.</li>
                    <li>El sistema mostrará un indicador de carga mientras se guardan las modificaciones.</li>
                    <li>Verá un mensaje de éxito si los cambios se guardaron correctamente, o un mensaje de error si hubo algún problema.</li>
                    <li>La tabla de medidas se actualizará para reflejar la información modificada.</li>
                </ul>
            </li>
        </ol>
    </div>
    
    <div class="ayuda-sub-header" id="eliminar-medida">Eliminar Medida</div>
    <div class="ayuda-texto">
        <p>Para eliminar un registro de unidad de medida del sistema:</p>
        <ol>
            <li>
                <b>Acceso a la función de eliminación:</b>
                <ul>
                    <li>En la tabla del módulo de Medidas, si tiene los permisos necesarios, verá un botón con el icono de una papelera en la última columna de cada fila, junto al icono de lápiz.</li>
                    <li>Haga clic en el botón de la papelera correspondiente a la medida que desea eliminar.</li>
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
                    <li>Una vez confirmada la eliminación, el sistema mostrará un indicador de carga mientras intenta eliminar el registro de la medida.</li>
                    <li>Finalmente, recibirá un mensaje de éxito si la medida fue eliminada correctamente, o un mensaje de error si la operación no pudo completarse.</li>
                </ul>
            </li>
        </ol>
    </div>

</section>