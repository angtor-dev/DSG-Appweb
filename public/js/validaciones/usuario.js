// TODO pasar formulario a ajax


// expresiones regulares
const regAlfanumerico = /^[A-Za-zá-úÁ-ÚñÑ0-9., ]*$/
const regAlfabetico = /^[A-Za-zá-úÁ-ÚñÑ\s]*$/
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
    if(!iCorreo.isValid()) return false
    const correo = iCorreo.value
    if(!regCorreo.test(correo)){
        iCorreo.setValidStatus(false, "El correo no es valido")
        return false
    }
    iCorreo.setValidStatus(true);
    return true
}

function validarNombre(id){
    const iNombre = document.getElementById(id)
    if(!iNombre) return false
    if(!iNombre.isValid()) return false
    let valor = iNombre.value.trim()
    if (valor.length <= 0) {
        iNombre.setValidStatus(false, "Este campo es obligatorio")
        return false
    }
    if (!regAlfabetico.test(valor)) {
        iNombre.setValidStatus(false, "Solo puede contener letras y espacios")
        return false
    }
    iNombre.setValidStatus(true);
    return true
}




function agregarValidaciones() {

    const loadFormFromLocalStorage = false;

    

    

    // formulario
    const formulario = document.getElementById('form-usuario')
    
    if(!formulario) {
        console.error("El formulario no cargo adecuadamente");
        return;
    }
    // campos
    const iNombre = document.getElementById('nombre')
    const iApellido = document.getElementById('apellido')
    const iCorreo = document.getElementById('correo')
    const iIdRol = document.getElementById('idRol')
    const iClave = document.getElementById('clave')
    const iCedula = document.getElementById('cedula')
    const isubmit = document.getElementById('submit-modal')
    const iGenerador = document.getElementById('generarClave-btn')
    const boolActualizando = /Actualizar/.test(formulario.action)
    const iId = document.getElementById('id-user');

    [iCorreo, iIdRol, iClave, iNombre, iApellido].forEach(element => {
        element.setValidStatus();
    });
    iGenerador.disabled = true

    // validar al desenfocar campo o al enviar formulario
    iClave.addEventListener('blur', ()=>{

        validarClave((/Actualizar/.test(formulario.action)))

    });


        iCedula.onkeyup= async function(e){


            if(iCedula.abortController) iCedula.abortController.abort("nueva peticion");
            const abortHolder = new AbortController();
            iCedula.abortController = abortHolder;
            if(!boolActualizando){

                [iCorreo, iIdRol, iClave, iNombre, iApellido].forEach(element => {
                    element.disabled = true
                    element.setValidStatus();
                    element.value = ""
                })
                this.setValidStatus();
                iGenerador.disabled = true
            }
            
            isubmit.disabled = true


            if(regCedula.test(this.value)){
                
                fetchObj = {useLoader:"#modal-generico .modal-content", signal:abortHolder.signal}
                let url = `/Usuarios/Registrar?cedula=${this.value}`
                if(boolActualizando){
                    url += `&id=${iId.value}`
                }
                let data = await peticion(url,fetchObj)
                if(abortHolder.signal.aborted) {
                    return;
                }
                data = JSON.parse(data)
                if (!data.userFound){

                    iCedula.setValidStatus(true)

                    if(!boolActualizando){
                        [iCorreo, iIdRol, iClave, iNombre, iApellido, iGenerador].forEach(element => {
                            element.disabled = false
                        })
                    }
                    isubmit.disabled = false


                    
                }
                else{
                    iCedula.setValidStatus(false, "El usuario ya se encuentra registrado")
                    // document.getElementById("btn-submit-registrar").disabled = false;
                }
            }
            else{
                iCedula.setValidStatus(false,"La cedula debe ser de 7 u 8 digitos")
            }
            iCedula.abortController = null;
        };


        [iCorreo, iIdRol, iClave, iNombre, iApellido].forEach(element => {
            element.addEventListener("input", () => {
                element.setValidStatus();
            });
        });

   

    
    formulario.addEventListener('submit', event => {
        
        if (
            !validarClave( boolActualizando ) ||
            !validarCedula( boolActualizando ) ||
            !validarCorreo() ||
            !validarNombre('nombre') ||
            !validarNombre('apellido')
        ) {
            event.preventDefault()
            event.stopPropagation()
        }
        else { // TODO quitar esto
            mostrarLoader('body');
            
        }
    })
}