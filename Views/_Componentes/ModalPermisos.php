<div class="modal fade" id="modal-permisos">
    <div class="modal-dialog modal-dialog-centered modal-lg"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        const modal = document.getElementById('modal-permisos')

        modal.addEventListener('show.bs.modal', async e => {
            const boton = event.relatedTarget

            const id = boton.getAttribute('data-bs-id')

            let response = await fetch("<?= LOCAL_DIR ?>/Seguridad/Roles/Permisos?id=" + id)
            let data = await response.text()

            modal.innerHTML = data
            eventoTodos();
        })
    });

    function eventoTodos(){
        console.log("evento");
        if( document.querySelector(".todosSelector") ){
            document.querySelectorAll(".todosSelector").forEach((item)=>{
                console.log("bucle 1");
                item.onclick=function(){
                    console.log("evento2");
                    let permiso = item.dataset.permiso;
                    let inputs = document.querySelectorAll(`[name*=${permiso}]`);
                    inputs.forEach((key)=>{
                        console.log("bucle 2");
                        key.checked = item.checked;
                    });
                }
            });
        }
    }
</script>