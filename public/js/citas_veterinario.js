$(document).ready(function () {
    // Obtiene el elemento HTML donde se renderizará el calendario
    var calendarEl = $('#calendario')[0];
    var eventoActual;

    // Inicializa el objeto FullCalendar con varias opciones de configuración
    var calendar = new FullCalendar.Calendar(calendarEl, {
        // Establece la vista inicial para mostrar la cuadrícula de tiempo semanal
        initialView: 'timeGridWeek',
        // Configura la barra de herramientas del encabezado con opciones de navegación y vista
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        // Establece el idioma a español
        locale: 'es',
        // Ajusta la altura del contenido para adaptarse al espacio disponible
        contentHeight: 'auto',
        // Define la hora de inicio y fin para las franjas horarias del calendario
        slotMinTime: '08:00:00',
        slotMaxTime: '21:00:00',
        // Establece la duración de cada franja horaria a una hora
        slotDuration: '01:00:00',
        // Evita la superposición de eventos en el mismo espacio de tiempo
        slotEventOverlap: false,
        // Oculta la franja de "todo el día"
        allDaySlot: false,
        // Define la fuente de eventos desde una API
        events: '/api/citas/',
        // Formatea la etiqueta de las franjas horarias
        slotLabelFormat: {
            hour: 'numeric',
            minute: '2-digit',
            omitZeroMinute: false,
        },
        // Personaliza el contenido del evento
        eventContent: function (arg) {
            var html = `
            <div class="event-title">${arg.event.title}</div>
            `;
            return { html: html };
        },
        // Define el rango válido de fechas que se pueden mostrar en el calendario
        validRange: function () {
            var today = new Date().toISOString().slice(0, 10);
            return {
                start: today
            };
        },
        // Maneja el evento de clic en un evento del calendario
        eventClick: function (info) {
            // Muestra el modal de detalles de la cita
            $('#modal-cita').modal('show');

            // Llena los campos del modal con la información del evento
            $('#cliente').text(info.event.extendedProps.cliente); // Muestra el nombre del cliente
            $('#mascota').text(info.event.title); // Muestra el nombre de la mascota
            $('#Motivo').text(info.event.extendedProps.motivo); // Muestra el motivo de la cita

            // Asocia el evento actual al botón de cancelar cita
            $('#cancelar-cita').data('event', info.event);

            // Obtiene la fecha y hora de inicio del evento
            var fechaInicio = new Date(info.event.start);
            // Calcula la fecha y hora de fin del evento añadiendo 1 hora a la hora de inicio
            var fechaFin = new Date(info.event.start.getTime() + 60 * 60 * 1000);

            // Formatea la fecha a un formato legible (YYYY-MM-DD)
            var fechaFormato = fechaInicio.toISOString().substring(0, 10);
            // Obtiene el tiempo en milisegundos desde el 1 de enero de 1970 para la hora de inicio y fin
            var horaInicio = fechaInicio.getTime();
            var horaFin = fechaFin.getTime();

            // Muestra la fecha y la hora en los campos correspondientes del modal
            $('#fecha').text(fechaFormato); // Muestra la fecha
            $('#hora').text(fechaInicio.toLocaleTimeString()); // Muestra la hora de inicio en formato local

            // Guarda la información del evento en la variable global para su uso posterior
            eventoActual = info.event;

            // Obtiene la fecha y la hora actuales
            var ahora = new Date();
            var fechaActual = ahora.toISOString().substring(0, 10); // Fecha actual en formato YYYY-MM-DD
            var horaActual = ahora.getTime(); // Hora actual en milisegundos

            // Verifica si la fecha del evento es hoy y si la hora actual está dentro del rango del evento
            if (fechaFormato === fechaActual && horaActual >= horaInicio && horaActual <= horaFin) {
                // Verifica si la cita ya tiene una consulta asociada
                if (!info.event.extendedProps.consulta) {
                    $('#pasar-consulta').show(); // Muestra el botón para pasar a consulta si no tiene consulta
                } else {
                    $('#pasar-consulta').hide(); // Oculta el botón si la cita ya tiene una consulta
                }
            } else {
                $('#pasar-consulta').hide(); // Oculta el botón si la fecha y la hora no coinciden
            }
        }

    });

    // Renderiza el calendario en la página
    calendar.render();

    // Manejadores para cerrar los modales
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
            // Muestra una alerta de confirmación
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '¡Sí, cancelar cita!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Elimina la cita de la base de datos
                    $.ajax({
                        url: '/api/citas/delete/' + event.id,
                        method: 'DELETE',
                        success: function (data) {
                            // Recarga los eventos del calendario
                            calendar.refetchEvents();
                            Swal.fire(
                                '¡Cita cancelada!',
                                'La cita ha sido cancelada.',
                                'success'
                            );
                        },
                        error: function (error) {
                            Swal.fire(
                                '¡Error!',
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
        // Cierra el modal actual
        $('#modal-cita').modal('hide');
        // Abre el modal de consulta
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
                '¡Error!',
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
            confirmButtonText: '¡Sí, guardar!'
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
                        // Cierra el modal de consulta
                        $('#modal-consulta').modal('hide');
                        Swal.fire(
                            '¡Guardado!',
                            'La consulta ha sido guardada.',
                            'success'
                        );
                    },
                    error: function (error) {
                        Swal.fire(
                            '¡Error!',
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
            // Añade una opción de selección al inicio
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
