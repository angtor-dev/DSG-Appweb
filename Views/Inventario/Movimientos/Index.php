<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Movimientos</h3>
                <span class="opacity-75 mb-2">Consulta los movimientos de invetario realizados</span>
            </div>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <div class="d-flex gap-2 pb-2 align-items-end">
                <div class="p-2 d-inline" style="color: #8d9498">
                    <i class="fa-solid fa-filter"></i>
                </div>
                <select class="select2 flex-grow-1" name="articulo" id="articulo">
                    <option value="entrada">Artículo</option>
                    <option value="entrada">Destornillador Plano 6mm x 100mm</option>
                    <option value="salida">Articulo 2</option>
                    <option value="salida">Articulo 3</option>
                    <option value="salida">Articulo 4</option>
                    <option value="salida">Articulo 5</option>
                    <option value="salida">Articulo 6</option>
                </select>
                <select class="select2 flex-grow-1" name="tipo" id="tipo">
                    <option value="entrada">Tipo</option>
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                </select>
                <input type="date" class="form-control flex-grow-1" style="width: unset;" name="desde" id="desde">
                <input type="date" class="form-control flex-grow-1" style="width: unset;" name="hasta" id="hasta">
            </div>
            <div class="table-responsive table-dsg">
                <table class="datatable table table-striped table-hover" id="tabla-medidas">
                    <thead>
                        <tr>
                            <th>Articulo</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Antes</th>
                            <th>Despues</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Destornillador Plano 6mm x 100mm</td>
                            <td>Entrada</td>
                            <td>5</td>
                            <td>0</td>
                            <td>5</td>
                            <td>01/04/2025</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php renderComponent('ModalEliminar') ?>
<?php renderComponent('ModalGenerico') ?>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        tablaMedidas = new DataTable('#tabla-medidas', {
            pagingType: 'simple_numbers',
            language: {
                url: '<?= LOCAL_DIR ?>/public/lib/DataTables/datatables-spanish.json'
            },
            layout: {
                bottom1Start: {
                    pageLength: true
                },
                bottom2End: {
                    buttons: ['excel', 'pdf', 'print']
                },
            }
        })
    })
</script>


<?php // agregarScript("medida.js") ?>
<?php // agregarScript("validaciones/medida.js") ?>
