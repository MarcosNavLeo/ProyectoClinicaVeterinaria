$(document).ready(function () {
    var table = $('#tabla').DataTable({
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
    },
    "ajax": {
        "url": "/api/consultas/" + idUser,
        "dataSrc": ""
    },
    "columns": [
        { "data": "fecha_hora" },
        { 
            "data": null,
            "render": function(data, type, row) {
                var imageUrl = window.location.origin + '/' + row.foto_mascota;
                return '<img src="' + imageUrl + '" style="width: 80px; height: 50px; border-radius: 50%;" /> ' + row.mascota;
            }
        },
        { "data": "diagnostico" },
        { "data": "tratamientos_nombre" },
        { "data": "medicamento_nombre" },
        {
            "data": null,
            "render": function (data, type, row) {
                return '<button class="btn btn-primary ver-detalle" data-id="' + data.id + '">Ver Detalles</button>';
            }
        }
    ]
});

    $('#tabla tbody').on('click', '.ver-detalle', function () {
        var data = table.row($(this).parents('tr')).data();
        // Llenar la modal con los datos de la consulta
        $('#modal-consulta .fecha-hora').text(data.fecha_hora);
        $('#modal-consulta .diagnostico').text(data.diagnostico);
        $('#modal-consulta .mascota').text(data.mascota);
        $('#modal-consulta .tratamientos-nombre').text(data.tratamientos_nombre);
        $('#modal-consulta .tratamientos-duracion').text(data.tratamientos_duracion);
        $('#modal-consulta .tratamientos-costo').text(data.tratamientos_costo);
        $('#modal-consulta .medicamento-nombre').text(data.medicamento_nombre);
        $('#modal-consulta .medicamento-instrucciones').text(data.medicamento_instrucciones);
        $('#modal-consulta .medicamento-dosis').text(data.medicamento_dosis);
        $('#modal-consulta').modal('show');
    });

    $('#modal-consulta .btn-secondary, #modal-consulta .close').click(function () {
        $('#modal-consulta').modal('hide');
    });
});