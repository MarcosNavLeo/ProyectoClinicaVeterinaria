$(document).ready(function () {
    // Inicialización de la tabla usando DataTables
    var table = $('#tabla').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json" // Configuración del idioma a español
        },
        "ajax": {
            "url": "/api/consultas/" + idUser, // URL para cargar los datos de las consultas del usuario
            "dataSrc": "" // Fuente de datos para DataTables
        },
        "columns": [
            { "data": "fecha_hora" }, // Columna para la fecha y hora de la consulta
            { 
                "data": null,
                "render": function(data, type, row) {
                    var imageUrl = window.location.origin + '/' + row.foto_mascota;
                    return '<img src="' + imageUrl + '" style="width: 80px; height: 50px; border-radius: 50%;" /> ' + row.mascota;
                } // Columna para la foto de la mascota y su nombre
            },
            { "data": "diagnostico" }, // Columna para el diagnóstico
            { "data": "tratamientos_nombre" }, // Columna para el nombre del tratamiento
            { "data": "medicamento_nombre" }, // Columna para el nombre del medicamento
            {
                "data": null,
                "render": function (data, type, row) {
                    return '<button class="btn btn-primary ver-detalle" data-id="' + data.id + '">Ver Detalles</button>';
                } // Columna con un botón para ver detalles de la consulta
            }
        ]
    });

    // Manejar el clic en el botón "Ver Detalles"
    $('#tabla tbody').on('click', '.ver-detalle', function () {
        var data = table.row($(this).parents('tr')).data();
        // Llenar el modal con los datos de la consulta
        $('#modal-consulta .fecha-hora').text(data.fecha_hora);
        $('#modal-consulta .diagnostico').text(data.diagnostico);
        $('#modal-consulta .mascota').text(data.mascota);
        $('#modal-consulta .tratamientos-nombre').text(data.tratamientos_nombre);
        $('#modal-consulta .tratamientos-duracion').text(data.tratamientos_duracion);
        $('#modal-consulta .tratamientos-costo').text(data.tratamientos_costo);
        $('#modal-consulta .medicamento-nombre').text(data.medicamento_nombre);
        $('#modal-consulta .medicamento-instrucciones').text(data.medicamento_instrucciones);
        $('#modal-consulta .medicamento-dosis').text(data.medicamento_dosis);
        $('#modal-consulta').modal('show'); // Mostrar el modal
    });

    // Cerrar el modal al hacer clic en el botón de cancelar o en el icono de cerrar
    $('#modal-consulta .btn-secondary, #modal-consulta .close').click(function () {
        $('#modal-consulta').modal('hide'); // Ocultar el modal
    });
});
