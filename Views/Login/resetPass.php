<?php
$_layout = "Login";
?>
<style>
    .loader{
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background-color: rgb(199 199 199 / 50%);
    z-index: 9999;
    opacity: 1;
}
.loader::after{
    --size:5rem;
    --size-border:.5rem;
    content: "";
    display: inline-block;
    border: var(--size-border) solid #0b60df;
    border-bottom: var(--size-border) solid #0000;
    border-radius: 100%;
    width: var(--size);
    height: var(--size);
    animation: spin 0.6s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loader.loader-body{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 99999;
    background-color: rgb(199 199 199 / 50%);
    height: 100vh;
    width: 100vw;
}
</style>
<div class="container d-flex align-items-center" style="height: 100vh;" id="main">
    <div class="card border-0 shadow mx-auto
        col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
        <div class="card-body d-flex flex-column align-items-center gap-3">
            <div class="mt-3">
                <style>
                    .logo-circle{
                        overflow: hidden;
                    }
                    .logo-circle svg{
                        transform: scale(2.2);
                        animation: fade-in 1s;
                        animation-timing-function: linear;
                    }
                    @keyframes fade-in {
                        0% {
                            transform: scale(7);
                        }
                        50% {
                            transform: scale(1.9);
                        }
                        55% {
                            transform: scale(2.5);
                        }
                        60% {
                            transform: scale(2.2);
                        }
                        90% {
                            transform: scale(2.2);
                        }
                        100% {
                            transform: scale(2.2);
                        }
                        
                    }
                </style>
                <div class="logo-circle">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="#ffffff"  class="icon icon-tabler icons-tabler-filled icon-tabler-key"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14.52 2c1.029 0 2.015 .409 2.742 1.136l3.602 3.602a3.877 3.877 0 0 1 0 5.483l-2.643 2.643a3.88 3.88 0 0 1 -4.941 .452l-.105 -.078l-5.882 5.883a3 3 0 0 1 -1.68 .843l-.22 .027l-.221 .009h-1.172c-1.014 0 -1.867 -.759 -1.991 -1.823l-.009 -.177v-1.172c0 -.704 .248 -1.386 .73 -1.96l.149 -.161l.414 -.414a1 1 0 0 1 .707 -.293h1v-1a1 1 0 0 1 .883 -.993l.117 -.007h1v-1a1 1 0 0 1 .206 -.608l.087 -.1l1.468 -1.469l-.076 -.103a3.9 3.9 0 0 1 -.678 -1.963l-.007 -.236c0 -1.029 .409 -2.015 1.136 -2.742l2.643 -2.643a3.88 3.88 0 0 1 2.741 -1.136m.495 5h-.02a2 2 0 1 0 0 4h.02a2 2 0 1 0 0 -4" /></svg>
                </div>
            </div>
            <h1 class="fs-4 fw-medium text-primary">Restablecer Clave</h1>
            <form method="post" class="d-flex flex-column gap-3 w-100 px-3 px-sm-5" id="form-reset" onsubmit="return false">
                <input required type="text" name="code" id="code" class="form-control" placeholder="Codigo">
                <!-- inputs de contraseña -->
                 <input required type="password" name="clave" class="form-control" id="clave" placeholder="Contraseña">
                 <input required type="password" name="clave_comp" class="form-control" id="clave_comp" placeholder="Confirmar contraseña">

                <span class="text-danger" id="error"></span>

                <button type="submit" class="btn btn-primary fw-medium btn-submit">
                    Restablecer
                </button>
            </form>
        </div>
    </div>
</div>
<script>
    const LOCAL_DIR = '<?= LOCAL_DIR ?>';
</script>
<script src="<?= LOCAL_DIR  ?>/public/js/utilities.js"></script>
<script src="<?= LOCAL_DIR  ?>/public/js/resetPass.js"></script>