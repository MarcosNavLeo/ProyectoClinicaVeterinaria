// Función para limpiar los campos del modal
function limpiarCamposModal() {
    $('#motivoCita').val(''); // Limpiar el campo de motivo
}

// Función para agregar las opciones del select de mascotas
function agregarOpcionesMascotas(mascotas) {
    // Limpiar el contenido del select de mascotas antes de agregar nuevas opciones
    $('#mascotaSelect').empty();
    // Agregar las nuevas opciones al select
    mascotas.forEach(function (mascota) {
        $('#mascotaSelect').append($('<option />', {
            value: mascota.id,
            text: mascota.nombre
        }));
    });
}

$(document).ready(function () {
    // Obtener el ID del usuario de la URL
    let path = window.location.pathname;
    let parts = path.split('/');
    let iduser = parts[parts.length - 1];

    // Configuración del calendario
    var calendarEl = $('#calendar')[0];
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek', // Vista inicial del calendario
        headerToolbar: {
            left: 'prev,next today', // Botones de navegación izquierda
            center: 'title', // Título del calendario
            right: 'timeGridWeek,timeGridDay' // Botones de navegación derecha
        },
        locale: 'es', // Configuración regional del calendario
        contentHeight: 'auto', // Altura del contenido del calendario
        slotMinTime: '08:00:00', // Hora mínima en el eje vertical
        slotMaxTime: '21:00:00', // Hora máxima en el eje vertical
        slotDuration: '01:00:00', // Duración de cada ranura de tiempo
        slotEventOverlap: false, // No permitir superposición de eventos en las ranuras
        allDaySlot: false, // No mostrar la ranura de todo el día
        events: '/api/citas/' + iduser, // URL para cargar los eventos desde el backend
        slotLabelFormat: {
            hour: 'numeric', // Formato de la hora (números)
            minute: '2-digit', // Formato de los minutos (dos dígitos)
            omitZeroMinute: false, // No omitir los ceros en los minutos
        },
        // Función para personalizar el contenido de los eventos en el calendario
        eventContent: function (arg) {
            var html = `
            <div class="event-title">${arg.event.title}</div>
        `;
            return { html: html };
        },
        // Función que se ejecuta al hacer clic en un evento en el calendario
        eventClick: function (info) {
            var event = info.event;
            var now = new Date();
            if (event.start < now) {
                // No se puede seleccionar una cita pasada
                Swal.fire({
                    title: 'Error',
                    text: 'No puedes seleccionar una cita pasada.',
                    icon: 'error'
                });
                return;
            }
            // Mostrar detalles de la cita en un modal
            var modal = $('#mostrarCitaModal');
            modal.find('#nombreMascota').text('Nombre de la mascota: ' + event.title);
            modal.find('#motivoCita').text('Motivo de la cita: ' + event.extendedProps.motivo);
            modal.modal('show');
            // Manejar la cancelación de la cita desde el modal
            $('#eliminarCita').click(function () {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Deseas cancelar esta cita?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, cancelar cita',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/citas/delete/' + event.id,
                            type: 'DELETE',
                            success: function () {
                                // Cita cancelada con éxito
                                Swal.fire({
                                    title: 'Cita cancelada',
                                    text: 'La cita se ha cancelado correctamente',
                                    icon: 'success'
                                });
                                calendar.refetchEvents();
                                modal.modal('hide');
                            },
                            error: function () {
                                // Error al cancelar la cita
                                Swal.fire({
                                    title: 'Error',
                                    text: 'No se pudo cancelar la cita',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        },
        // Función que se ejecuta al hacer clic en una fecha en el calendario
        dateClick: function (info) {
            var clickedDate = new Date(info.dateStr);
            var now = new Date();
            if (clickedDate < now) {
                // No se puede seleccionar una fecha pasada
                Swal.fire({
                    title: 'Error',
                    text: 'No puedes seleccionar una fecha pasada.',
                    icon: 'error'
                });
                return;
            }
            // Verificar si el usuario tiene mascotas
            $.ajax({
                url: '/api/mascotas/' + iduser,
                type: 'GET',
                success: function (mascotas) {
                    // Si el usuario no tiene mascotas, mostrar un mensaje de error
                    if (mascotas.length === 0) {
                        Swal.fire({
                            title: 'No se pueden programar citas',
                            text: 'Debes tener al menos una mascota registrada para programar una cita',
                            icon: 'error'
                        });
                        return;
                    }
                    // Agregar las mascotas al select
                    agregarOpcionesMascotas(mascotas);
                    // Continuar con la lógica para programar la cita
                    $.ajax({
                        url: '/api/citas',
                        type: 'GET',
                        success: function (allEvents) {
                            // Verificar si hay algún evento programado para la fecha y hora seleccionadas
                            var hayEventoProgramado = allEvents.some(function (evento) {
                                // Convierte las fechas de inicio y fin del evento en objetos Date
                                var inicioEvento = new Date(evento.start);
                                var finEvento = new Date(evento.end);

                                // Comprueba si la fecha seleccionada está dentro del rango de tiempo del evento
                                return clickedDate >= inicioEvento && clickedDate < finEvento;
                            });


                            if (!hayEventoProgramado) {
                                var modal = $('#citaModal');
                                var modalHora = $('#horaCita');
                                var modalFecha = $('#fechaCita');
                                var modalTitle = $('#citaModalLabel');
                                var fechaFormateada = clickedDate.toLocaleDateString('en-US');
                                var horaFormateada = clickedDate.toTimeString().substring(0, 5);
                                modalHora.text(horaFormateada);
                                modalFecha.text(fechaFormateada);
                                modalTitle.text('Nueva cita');
                                modal.modal('show');
                                // Manejar la creación de la cita desde el modal
                                $('#guardarCita').off('click').on('click', function () {
                                    var motivo = $('#motivoCita').val();
                                    var mascota = $('#mascotaSelect').val();
                                    // Validar que se haya seleccionado una mascota y escrito un motivo
                                    if (!mascota || motivo.trim() === '') {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'Debes seleccionar una mascota y escribir un motivo para programar la cita.',
                                            icon: 'error'
                                        });
                                        return; // Detener la ejecución si no se cumple la validación
                                    }
                                    Swal.fire({
                                        title: '¿Estás seguro?',
                                        text: '¿Deseas pedir esta cita?',
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Sí, pedir cita',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            var formData = new FormData();
                                            formData.append('fecha', fechaFormateada);
                                            formData.append('hora', horaFormateada);
                                            formData.append('motivo', motivo);
                                            formData.append('mascota', mascota);
                                            formData.append('idUser', iduser);

                                            $.ajax({
                                                url: '/api/crear/citas',
                                                type: 'POST',
                                                data: formData,
                                                contentType: false,
                                                processData: false, 
                                                success: function (response) {
                                                    // Cita creada con éxito
                                                    Swal.fire({
                                                        title: 'Cita creada',
                                                        text: 'La cita se ha creado correctamente',
                                                        icon: 'success'
                                                    });
                                                    calendar.refetchEvents();
                                                    modal.modal('hide');
                                                },
                                                error: function () {
                                                    // Error al crear la cita
                                                    Swal.fire({
                                                        title: 'Error',
                                                        text: 'No se pudo crear la cita',
                                                        icon: 'error'
                                                    });
                                                }
                                            });
                                        }
                                    });
                                });
                            } else {
                                // Horario ocupado
                                Swal.fire({
                                    title: 'Horario ocupado',
                                    text: 'El horario seleccionado ya tiene una cita programada',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function () {
                            // Error al cargar las citas
                            Swal.fire({
                                title: 'Error',
                                text: 'No se pudieron cargar las citas',
                                icon: 'error'
                            });
                        }
                    });
                },
                error: function () {
                    // Error al cargar las mascotas
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudieron cargar las mascotas',
                        icon: 'error'
                    });
                }
            });
        },
    });

    // Renderizar el calendario
    calendar.render();

    // Cerrar el modal al hacer clic en el botón de cancelar o en el icono de cerrar
    $('#citaModal .btn-secondary, #citaModal .close').click(function () {
        $('#citaModal').modal('hide');
        limpiarCamposModal(); // Limpiar campos del modal al cerrarlo
    });

    $('#mostrarCitaModal .btn-secondary, #mostrarCitaModal .close').click(function () {
        $('#mostrarCitaModal').modal('hide');
    });
});
