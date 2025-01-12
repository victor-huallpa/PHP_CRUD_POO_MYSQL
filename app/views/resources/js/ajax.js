const formulario_ajax = document.querySelectorAll('.FormularioAjax');


formulario_ajax.forEach(formularios => {
    formularios.addEventListener("submit", function(e){
        e.preventDefault();//ya no redirecciona al link del formulario

        //alerta de switalert
        Swal.fire({
            title: "Estas seguro?",
            text: "Quieres realizar la accion solicitada",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, realizar!",
            cancelmButtonText: "No, cancelar!"
        }).then((result) => {
            if (result.isConfirmed) {

                let data = new FormData(this);
                let method = this.getAttribute("method");
                let action = this.getAttribute("action");

                let encabezados = new Headers();

                let config = {
                    method: method,
                    headers: encabezados,
                    mode: 'cors',
                    cache: 'no_cache',
                    body: data,
                };

                fetch(action,config)
                .then(respuesta => respuesta.json())
                .then(respuesta => {
                    return alertas_ajax(respuesta);
                });
            }
        });

    });
});

function alertas_ajax(alerta){
    
}
