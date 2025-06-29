<?php /** @var Usuario $perfil */ ?>

<style>
    .perfil-row{
        --border-color:red;
        --icon-color:var(--bs-gray-500);
    }
    .perfil-icon>i{
        color:var(--icon-color);
    }
    .perfil-icon,.perfil-datos{
        border:1px solid var(--border-color);
        padding:1.25rem;
    }
    .perfil-icon{
        border-radius: 10px 0 0 10px;
    }
    .perfil-datos{
        border-radius: 0 10px 10px 0;
        border-left: none;
    }
    .control-pass.invalid{
        color: red;
    }
    .control-pass.valid{
        color: green;
    }
    
</style>

<div class="panel-header" style="background-color: red;">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Perfil</h3>
                <span class="opacity-75 mb-2">Bienvenido </span>
            </div>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-auto">

                        <ul class="nav flex-column nav-pills" id="tabs-perfil" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tab-perfil" draggable="false" class="nav-link active" data-bs-toggle="pill" aria-selected="true" role="tab" tabindex="-1">
                                    <i class="fa-solid fa-user me-2"></i>
                                    Perfil
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tab-editar-perfil" draggable="false" class="nav-link" data-bs-toggle="pill" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="fa-solid fa-user me-2"></i>
                                    Editar Perfil
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tab-seguridad" draggable="false" class="nav-link" data-bs-toggle="pill" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="fa-solid fa-lock me-2"></i>
                                    Seguridad
                                </a>
                            </li>
                        </ul>

                    </div>
                    <div class="col-md">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="tab-perfil" role="tabpanel">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <span class="h3">Perfil</span>
                                        <hr>

                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-info-field" data-info="Cedula">
                                                            <?= $perfil->getCedula() ?>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-info-field" data-info="Rol">
                                                            <?= $perfil->rol->getNombre() ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-info-field" data-info="Nombre">
                                                            <?= $perfil->getNombre() ?>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-info-field" data-info="Apellido">
                                                            <?= $perfil->getApellido() ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-info-field" data-info="Correo">
                                                            <?= $perfil->getCorreo() ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab-editar-perfil" role="tabpanel" aria-labelledby="pill-editar-perfil">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <span class="h3">Editar Perfil</span>
                                        <hr>
                                        <form action="" id="form-editar_perfil" onsubmit="return false">
                                            <input type="hidden" id="perfil-id" name="perfil-id" value="<?= $perfil->id ?>">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col">
                                                        <label for="perfil-cedula" class="form-label">Cedula</label>
                                                        <input required  type="text" class="form-control" id="perfil-cedula" name="perfil-cedula" data-formText="form-text-perfil-cedula" value="<?= $perfil->getCedula() ?>">
                                                        <div id="form-text-perfil-cedula" class="form-text invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <label for="perfil-nombre" class="form-label">Nombre </label>
                                                        <input required type="text" class="form-control" id="perfil-nombre" name="perfil-nombre" data-formText="form-text-perfil-nombre" value="<?= $perfil->getNombre() ?>">
                                                        <div id="form-text-perfil-nombre" class="form-text invalid-feedback"></div>
                                                    </div>
                                                    <div class="col">
                                                        <label for="perfil-apellido" class="form-label">Apellido</label>
                                                        <input required type="text" class="form-control" id="perfil-apellido" name="perfil-apellido" data-formText="form-text-perfil-apellido" value="<?= $perfil->getApellido() ?>">
                                                        <div id="form-text-perfil-apellido" class="form-text invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <label for="perfil-correo" class="form-label">Correo</label>
                                                        <input required type="email" class="form-control" id="perfil-correo" name="perfil-correo" data-formText="form-text-perfil-correo" value="<?= $perfil->getCorreo() ?>">
                                                        <div id="form-text-perfil-correo" class="form-text invalid-feedback"></div>  
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row justify-content-end">
                                                    <div class="col-auto">
                                                        <button type="submit" class="btn btn-primary">Editar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="tab-seguridad" role="tabpanel">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <span class="h3">Modificar contraseña</span>
                                        <hr>
                                        <form action="" onsubmit="return false;">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="perfil-clave_actual" class="form-label">Clave Actual </label>
                                                            <input required type="password" class="form-control" id="perfil-clave_actual" name="perfil-clave_actual" data-formText="form-text-perfil-clave_actual">
                                                            <div id="form-text-perfil-clave_actual" class="form-text invalid-feedback"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="perfil-nueva_clave" class="form-label">Clave Nueva </label>
                                                            <input required type="password" class="form-control" id="perfil-nueva_clave" name="perfil-nueva_clave" data-formText="form-text-perfil-nueva_clave">
                                                            <div id="form-text-perfil-nueva_clave" class="form-text invalid-feedback"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="perfil-confirmar" class="form-label">Confirmar Clave</label>
                                                            <input required type="password" class="form-control" id="perfil-confirmar" name="perfil-confirmar" data-formText="form-text-perfil-confirmar">
                                                            <div id="form-text-perfil-confirmar" class="form-text invalid-feedback"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    La contraseña no debe tener mas de <strong id="maxClaveCaractersCont"></strong> caracteres y debe cumplir con los siguientes requerimientos
                                                    <hr>
                                                    <div>
                                                        <div class="control-pass invalid" id="control-letra-mayuscula">* Debe contener al menos una letra mayúscula</div>
                                                        <div class="control-pass invalid" id="control-letra-minuscula">* debe contener al menos una letra minúscula</div>
                                                        <div class="control-pass invalid" id="control-numero">* debe contener al menos un numero</div>
                                                    </div>

                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const clave_actual = document.getElementById('perfil-clave_actual')
    const nueva_clave = document.getElementById('perfil-nueva_clave')
    const confirmar  = document.getElementById('perfil-confirmar')
    const maxClaveCaracters = 20;

    document.getElementById('maxClaveCaractersCont').textContent = maxClaveCaracters;


    

    nueva_clave.addEventListener("keyup",function(){
        const regexClave = /(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])/;
        const regexMayus = /(?=.*[A-Z])/;
        const regexMinus = /(?=.*[a-z])/;
        const regexNum = /(?=.*[0-9])/;

        const replaceAtribute = (elem,atribute1,atribute2)=>{
            elem.classList.remove(atribute1);
            elem.classList.add(atribute2);
        }

        const control_letra_mayuscula = document.getElementById('control-letra-mayuscula');
        const control_letra_minuscula = document.getElementById('control-letra-minuscula');
        const control_numero = document.getElementById('control-numero');

        if(!regexClave.test(this.value) || this.value.length>maxClaveCaracters) 
            this.setValidStatus(false,"La contraseña no es valida");
        else this.setValidStatus(true);

        if(!regexMayus.test(this.value)) 
            replaceAtribute(control_letra_mayuscula,"valid","invalid");
        else
            replaceAtribute(control_letra_mayuscula,"invalid","valid");


        if(!regexMinus.test(this.value)) 
            replaceAtribute(control_letra_minuscula,"valid","invalid");
        else
            replaceAtribute(control_letra_minuscula,"invalid","valid");

        if(!regexNum.test(this.value)) 
            replaceAtribute(control_numero,"valid","invalid");
        else
            replaceAtribute(control_numero,"invalid","valid");



    })

    confirmar.addEventListener("keyup",function(){
        nueva_clave.value = nueva_clave.value.trim()
        if(nueva_clave.value == ""){
            this.setValidStatus(false,"El campo de nueva clave no puede estar vacío");
        }
        else{
            if(nueva_clave.value != this.value){
                this.setValidStatus(false,"Las calves no coinciden");
            }
            else{
                this.setValidStatus(true);
            }
        }

    })


</script>

<?php renderComponent('ModalGenerico') ?>

<?php //agregarScript("perfil.js") ?>







