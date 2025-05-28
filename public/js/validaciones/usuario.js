// TODO pasar formulario a ajax


// expresiones regulares
const regAlfanumerico = /^[A-Za-zá-úÁ-ÚñÑ0-9., ]*$/
const regCedula = /^[0-9]{7,8}$/
const regCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


// validaciones

function validarClave(allowEmpty = false) {
    const iClave = document.getElementById('clave')
    let valor = iClave.value.trim()


    if(!(allowEmpty && valor.length == 0)){
        
        if (valor.length <= 0) {
            iClave.setValidStatus(false, "Este campo es obligatorio")
            return false
        }
        if (!regClave.test(valor)) {
            iClave.setValidStatus(false, "La clave debe contener al menos una letra y un numero, y ser 6 caracteres de longitud")
            return false;
        }

    }
    iClave.setValidStatus(true);
    return true
}

function validarCedula(noValidar = false) {
    const iCedula = document.getElementById('cedula');
    const cedula = iCedula.value || "";
    let ok = true;
    if (noValidar) {
        return ok;
    }
    else if (!regCedula.test(cedula)) {
        iCedula.setValidStatus(false, "La cedula debe ser de 7 u 8 digitos")
        ok = false
    }
    iCedula.setValidStatus(true);
    return ok
}

function validarCorreo(){
    const iCorreo = document.getElementById('correo')
    const correo = iCorreo.value
    if(!regCorreo.test(correo)){
        iCorreo.setValidStatus(false, "El correo no es valido")
        return false
    }
    iCorreo.setValidStatus(true);
    return true
}




function agregarValidaciones() {

    

    // formulario
    const formulario = document.getElementById('form-usuario')
    // campos
    const iNombre = document.getElementById('nombre')
    const iDepartamento = document.getElementById('departamento')
    const iCorreo = document.getElementById('correo')
    const iIdRol = document.getElementById('idRol')
    const iClave = document.getElementById('clave')
    const iCedula = document.getElementById('cedula')
    const isubmit = document.getElementById('submit-modal')
    const iGenerador = document.getElementById('generarClave-btn')
    const boolActualizando = /Actualizar/.test(formulario.action)

    // validar al desenfocar campo o al enviar formulario
    iClave.addEventListener('blur', ()=>{

        validarClave((/Actualizar/.test(formulario.action)))

    });


    if(!boolActualizando){
        iCedula.onkeyup= async function(e){


            if(iCedula.abortController) iCedula.abortController.abort("nueva peticion");
            const abortHolder = new AbortController();
            iCedula.abortController = abortHolder;
            [iCorreo, iIdRol, iClave].forEach(element => {
                element.disabled = true
                element.setValidStatus();
                element.value = ""
            })
            iNombre.textContent = ""
            iDepartamento.textContent = ""
            this.setValidStatus();
            isubmit.disabled = true
            iGenerador.disabled = true



            if(regCedula.test(this.value)){
                
                fetchObj = {useLoader:"#modal-generico .modal-content", signal:abortHolder.signal}
                let data = await peticion(`/Usuarios/Registrar?cedula=${this.value}`,fetchObj)
                if(abortHolder.signal.aborted) {
                    return;
                }
                data = JSON.parse(data)
                if(data.cedula && !data.usuario){

                    iCedula.setValidStatus(true)

                    iNombre.textContent = data.nombre
                    iDepartamento.textContent = data.departamento

                    iCorreo.disabled = false
                    iIdRol.disabled = false
                    iClave.disabled = false
                    isubmit.disabled = false
                    iGenerador.disabled = false


                    
                }
                else{
                    iCedula.setValidStatus(false,(data.usuario? "El usuario ya existe" : "El trabajador no existe"))
                    // document.getElementById("btn-submit-registrar").disabled = false;
                }
            }
            else{
                iCedula.setValidStatus(false,"La cedula debe ser de 7 u 8 digitos")
            }
            iCedula.abortController = null;
        }
    }

   

    
    formulario.addEventListener('submit', event => {
        if (
            !validarClave( boolActualizando ) ||
            !validarCedula( boolActualizando ) ||
            !validarCorreo()
        ) {
            event.preventDefault()
            event.stopPropagation()
        }
        else { // TODO quitar esto
            mostrarLoader('body');
            
        }
    })
}