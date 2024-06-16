$(document).ready(function () {
  // Botón para añadir mascota
  var btnañadirMasco = $('#btnañadirMasco');
  
  // Inicialización del datepicker para la fecha de nacimiento de la mascota
  $('#fechaNacimientoMascota').datepicker({
    dateFormat: 'dd-mm-yy',
    maxDate: 0, // No permitir fechas futuras
    changeYear: true // Permitir cambio de año
  });

  // Mostrar el modal para añadir una mascota al hacer clic en el botón
  btnañadirMasco.click(function () {
    $('#addPetModal').modal('show');
  });

  // Ocultar el modal para añadir una mascota al hacer clic en el botón de cancelar o en el icono de cerrar
  $('#addPetModal .btn-secondary, #addPetModal .close').click(function () {
    $('#addPetModal').modal('hide');
  });

  // Mostrar el indicador de carga
  $('#cargando').show();
  // Obtener las mascotas del usuario
  $.ajax({
    url: '/api/mascotas/' + idUser,
    type: 'GET',
    success: function (data) {
      $('#cargando').hide();
      if (data.length === 0) {
        // Si no hay mascotas, mostrar un mensaje
        $('#mascotas-container').html('<p>No hay mascotas.</p>');
      } else {
        // Si hay mascotas, construir las tarjetas de mascotas
        var row = $('<div />', { class: 'row' });
        $.each(data, function (index, mascota) {
          var img = $('<img />', {
            src: mascota.foto,
            alt: mascota.nombre,
            class: 'card-img-top custom-img-size'
          });
          var nombre = $('<h5 />', { class: 'card-title' }).text(mascota.nombre);
          var fechaNacimiento = $('<span />', { class: 'card-subtitle mb-2 text-muted' }).text(mascota.fechaNacimiento);
          var raza = $('<p />', { class: 'card-text' }).html('<strong>Raza:</strong> ' + mascota.raza);
          var especie = $('<p />', { class: 'card-text' }).html('<strong>Especie:</strong> ' + mascota.especie);

          var razaColumn = $('<div />', { class: 'col-md-6' }).append(raza);
          var especieColumn = $('<div />', { class: 'col-md-6' }).append(especie);
          var infoRow = $('<div />', { class: 'row' }).append(razaColumn, especieColumn);

          // Crear el botón de eliminar
          var deleteButton = $('<button />', {
            text: 'Eliminar',
            class: 'btn btn-danger',
            click: function () {
              // Aquí puedes añadir la lógica para eliminar la mascota
              console.log('Eliminar mascota con id ' + mascota.id);
              Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas eliminar esta mascota?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
              }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                    url: '/api/mascotas/delete/' + mascota.id,
                    type: 'DELETE',
                    success: function () {
                      // Verificar si la tarjeta es la última en su fila
                      if (cardCol.nextAll().length % 3 === 0) {
                        // Si es la última, insertar un elemento vacío para mantener el diseño de la cuadrícula
                        cardCol.after($('<div />', { class: 'col-md-4' }));
                      }
                      // Eliminar la mascota del DOM
                      cardCol.remove();
                      // Mostrar mensaje de éxito con SweetAlert
                      Swal.fire({
                        icon: 'success',
                        title: 'Mascota eliminada correctamente',
                        showConfirmButton: false,
                        timer: 1500
                      });
                    },
                    error: function () {
                      // Mostrar mensaje de error con SweetAlert
                      Swal.fire({
                        icon: 'error',
                        title: 'Error al eliminar la mascota',
                        text: 'Por favor, inténtalo de nuevo más tarde.',
                        showConfirmButton: true
                      });
                    }
                  });
                }
              });
            }
          });

          var cardBody = $('<div />', { class: 'card-body' }).append(nombre, fechaNacimiento, infoRow, deleteButton);
          var card = $('<div />', {
            class: 'card mb-4',  // Añadir un margen inferior
            css: {               // Añadir estilos CSS
              width: '300px',    // Ajusta este valor según tus necesidades
              height: '400px'    // Ajusta este valor según tus necesidades
            }
          }).append(img, cardBody);
          var cardCol = $('<div />', {
            class: 'col-md-4'
          }).append(card);
          row.append(cardCol);
        });
        $('#mascotas-container').append(row);
      }
    },
    error: function () {
      console.log('No se pudieron cargar las mascotas');
    }
  });

  // Manejador de eventos para el botón "Guardar cambios"
  $('#guardarCambios').click(function () {
    // Obtener los datos del formulario
    var nombre = $('#nombreMascota').val();
    var especie = $('#especieMascota').val();
    var raza = $('#razaMascota').val();
    var fechaNacimiento = $('#fechaNacimientoMascota').val();
    var foto = $('#fotoMascota')[0].files[0];

    // Comprobar si todos los campos están rellenos
    if (!nombre || !especie || !raza || !fechaNacimiento || !foto) {
      // Mostrar mensaje de error con SweetAlert
      Swal.fire({
        icon: 'error',
        title: 'Todos los campos son obligatorios',
        text: 'Por favor, rellena todos los campos antes de guardar.',
        showConfirmButton: true
      });
      return;  // Detener la ejecución de la función
    }

    // Mostrar confirmación al usuario
    Swal.fire({
      title: '¿Estás seguro?',
      text: '¿Deseas añadir esta mascota?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, añadir',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Crear un objeto FormData
        var formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('especie', especie);
        formData.append('raza', raza);
        formData.append('fechaNacimiento', fechaNacimiento);
        formData.append('foto', foto);
        formData.append('propietario', idUser);

        // Enviar los datos al servidor mediante AJAX
        $.ajax({
          url: '/api/mascotas/create/' + idUser,
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: function (mascota) {
            // Mostrar mensaje de éxito con SweetAlert
            Swal.fire({
              icon: 'success',
              title: 'Mascota creada correctamente',
              showConfirmButton: false,
              timer: 1500
            });
            // Cerrar el modal
            $('#addPetModal').modal('hide');
            // Recargar la página
            location.reload();
          },
          error: function (error) {
            // Mostrar mensaje de error con SweetAlert
            Swal.fire({
              icon: 'error',
              title: 'Error al crear la mascota',
              text: 'Por favor, inténtalo de nuevo más tarde.',
              showConfirmButton: true
            });
            console.error('Error al crear la mascota:', error);
          }
        });
      }
    });
  });
});
