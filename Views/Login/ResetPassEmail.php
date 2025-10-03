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
        <div class="card-body d-flex flex-column align-items-center gap-3" id="card-reset">
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
                    
                    <svg  xmlns="http://www.w3.org/2000/svg"  height="40" width="28"  viewBox="0 0 24 24"  fill="#ffffff"  class="icon icon-tabler icons-tabler-filled icon-tabler-mail"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M22 7.535v9.465a3 3 0 0 1 -2.824 2.995l-.176 .005h-14a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-9.465l9.445 6.297l.116 .066a1 1 0 0 0 .878 0l.116 -.066l9.445 -6.297z" /><path d="M19 4c1.08 0 2.027 .57 2.555 1.427l-9.555 6.37l-9.555 -6.37a2.999 2.999 0 0 1 2.354 -1.42l.201 -.007h14z" /></svg>
                </div>
            </div>
            <h1 class="fs-4 fw-medium text-primary">Restablecer Clave</h1>
            <form method="post" class="d-flex flex-column gap-3 w-100 px-3 px-sm-5" id="form-reset" onsubmit="return false">
                <input required type="email" name="correo" class="form-control" placeholder="Correo">
                <span class="text-danger" id="error"></span>
                <button type="submit" class="btn btn-primary fw-medium btn-submit">
                    Restablecer
                </button>
            </form>
        </div>
    </div>
</div>
<script src="<?= LOCAL_DIR  ?>/public/js/utilities.js"></script>
<script>const LOCAL_DIR = '<?= LOCAL_DIR ?>';</script>
<script src="<?= LOCAL_DIR  ?>/public/js/resetPassEmail.js">
    

</script>