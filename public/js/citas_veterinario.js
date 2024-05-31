$(document).ready(function () {
    var calendarEl = $('#calendario')[0];
    var eventoActual;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        locale: 'es',
        contentHeight: 'auto',
        slotMinTime: '08:00:00',
        slotMaxTime: '21:00:00',
        slotDuration: '01:00:00',
        slotEventOverlap: false,
        allDaySlot: false,
        events: '/api/citas/',
        slotLabelFormat: {
            hour: 'numeric',
            minute: '2-digit',
            omitZeroMinute: false,
        },
        eventContent: function (arg) {
            var html = `
            <div class="event-title">${arg.event.title}</div>
            `;
            return { html: html };
        },
        validRange: function () {
            var today = new Date().toISOString().slice(0, 10);
            return {
                start: today
            };
        },
        eventClick: function (info) {
            $('#modal-cita').modal('show');
            $('#cliente').text(info.event.extendedProps.cliente);
            $('#mascota').text(info.event.title);
            $('#Motivo').text(info.event.extendedProps.motivo);
            $('#cancelar-cita').data('event', info.event);

            // hora y fecha
            var fechaInicio = new Date(info.event.start);
            var fechaFin = new Date(info.event.start.getTime() + 60 * 60 * 1000); // Añade 1 hora a la hora de inicio
            var fechaFormato = fechaInicio.toISOString().substring(0, 10);
            var horaInicio = fechaInicio.getTime();
            var horaFin = fechaFin.getTime();

            $('#fecha').text(fechaFormato);
            $('#hora').text(fechaInicio.toLocaleTimeString());

            // Guarda la información del evento en la variable global
            eventoActual = info.event;

            // Verifica si la fecha y la hora del evento coinciden con la fecha y la hora actuales
            var ahora = new Date();
            var fechaActual = ahora.toISOString().substring(0, 10);
            var horaActual = ahora.getTime();

            if (fechaFormato === fechaActual && horaActual >= horaInicio && horaActual <= horaFin) {
                // Verifica si la cita ya tiene una consulta
                if (!info.event.extendedProps.consulta) {
                    $('#pasar-consulta').show(); // Muestra el botón si no tiene consulta
                } else {
                    $('#pasar-consulta').hide(); // Oculta el botón si la cita ya tiene una consulta
                }
            } else {
                $('#pasar-consulta').hide(); // Oculta el botón si la fecha y la hora no coinciden
            }
        }
    });

    calendar.render();

    $('#modal-cita .btn-secondary, #modal-cita .close').click(function () {
        $('#modal-cita').modal('hide');
    });

    $('#modal-consulta .btn-secondary, #modal-consulta .close').click(function () {
        $('#modal-consulta').modal('hide');
    });

    // Manejador para el botón de cancelar cita
    $('#cancelar-cita').click(function () {
        var event = $(this).data('event');
        if (event) {
            event.remove();
            $('#modal-cita').modal('hide');
            //sweet alert
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cancelar cita!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Elimina la cita de la base de datos
                    $.ajax({
                        url: '/api/citas/delete/' + event.id,
                        method: 'DELETE',
                        success: function (data) {
                            //regarla la pagina
                            calendar.refetchEvents();
                            Swal.fire(
                                'Cita cancelada!',
                                'La cita ha sido cancelada.',
                                'success'
                            );
                        },
                        error: function (error) {
                            Swal.fire(
                                'Error!',
                                'Ha ocurrido un error al cancelar la cita.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    });

    // Manejador para el botón de pasar consulta
    $('#pasar-consulta').click(function () {
        // Cierra la modal actual
        $('#modal-cita').modal('hide');
        // Abre la modal de consulta
        $('#modal-consulta').modal('show');
    });

    // Manejador para el botón de guardar consulta
    $('#guardar-consulta').click(function () {
        // Recoge los datos del formulario
        var data = {
            citas_id: eventoActual.id,
            fecha_hora: eventoActual.start.toISOString(),
            diagnostico: $('#diagnostico').val(),
            tratamientos_id: $('#tratamientos').val()
        };

        // Verifica si los campos de diagnóstico y tratamiento están vacíos
        if (!data.diagnostico || !data.tratamientos_id) {
            Swal.fire(
                'Error!',
                'Por favor, rellene todos los campos antes de guardar la consulta.',
                'error'
            );
            return;
        }

        // Pregunta al usuario si quiere guardar la cita
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Quieres guardar esta consulta?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, guardar!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, llama a la API de consulta
                $.ajax({
                    url: '/api/consultas',
                    method: 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    success: function (response) {
                        // Recarga los eventos del calendario
                        calendar.refetchEvents();
                        // Cierra la modal de consulta
                        $('#modal-consulta').modal('hide');
                        Swal.fire(
                            'Guardado!',
                            'La consulta ha sido guardada.',
                            'success'
                        );
                    },
                    error: function (error) {
                        Swal.fire(
                            'Error!',
                            'Ha ocurrido un error al guardar la consulta.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Añadir tratamientos al select
    $.ajax({
        url: '/api/tratamientos',
        method: 'GET',
        success: function (data) {
            $('#tratamientos').append('<option value="">Seleccione un tratamiento</option>');
            data.forEach(tratamiento => {
                $('#tratamientos').append(`<option value="${tratamiento.id}">${tratamiento.descripcion}</option>`);
            });
        },
        error: function (error) {
            console.log(error);
        }
    });

});