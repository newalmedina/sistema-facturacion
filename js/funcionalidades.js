//solo numeros
function soloNumeros(e) {
    var key = window.Event ? e.which : e.keyCode
    return (key >= 48 && key <= 57)
}

function validarImagen(obj) {
    var uploadFile = obj.files[0];
    mostrarFoto.src = "";
    if (!window.FileReader) {
        alert('El navegador no soporta la lectura de archivos');
        return;
    }

    if (!(/\.(jpg|jpeg|png|gif)$/i).test(uploadFile.name)) {
        swal({
            title: "Error al intentar cargar la imagen",
            text: "El archivo seleccionado no corresponde a un formato imagen (solo se aceptan jpg, jpeg, gid, png)",
            icon: "error",
        });
        mostrarFoto.src = "";
        foto.value = "";
    } else {
        mostrarFoto.src = "";
        mostrarFoto.src = URL.createObjectURL(uploadFile);
    }
}
function Validar(idcampo, campoError, validador, tabladb) {
    var campo1 = $(idcampo);
    var parametros = {
        campo: campo1.val(),
        validar: validador,
        tabla: tabladb
    };
    $.ajax({
        data: parametros,
        url: 'funcionalidades.php',
        type: 'POST',
        beforeSend: function () {
            console.log("Procesando, espere por favor...");
        },

        success: function (response) {
            $(campoError).html(response);
            if (response != '') {
                //si existe vacia el campo y poner placeholder

                campo1.attr("placeholder", "Ingrese otro diferente");
                campo1.val("");
            }
        }
    });

}

