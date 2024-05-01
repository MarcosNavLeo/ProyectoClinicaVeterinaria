$(document).ready(function () {
  var btnañadirMasco = $('#btnañadirMasco');
  $('#fechaNacimientoMascota').datepicker({
    dateFormat: 'dd-mm-yy',
    maxDate: 0,
    changeYear: true
  });

  btnañadirMasco.click(function () {
    $('#addPetModal').modal('show');
  });

  $('#addPetModal .btn-secondary, #addPetModal .close').click(function () {
    $('#addPetModal').modal('hide');
  });
  $('#cargando').show();
  $.ajax({
    url: '/api/mascotas/' + idUser,
    type: 'GET',
    success: function (data) {
      $('#cargando').hide();
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
          class: 'card mb-4'  // Añadir un margen inferior
        }).append(img, cardBody);
        var cardCol = $('<div />', {
          class: 'col-md-4'
        }).append(card);
        row.append(cardCol);
      });
      $('#mascotas-container').append(row);
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
            // Agregar la nueva mascota al contenedor
            var img = $('<img />', {
              src: mascota.foto,
              alt: mascota.nombre,
              class: 'card-img-top custom-img-size'
            });
            var nombreElement = $('<h5 />', { class: 'card-title' }).text(mascota.nombre);
            var razaElement = $('<p />', { class: 'card-text' }).html('<strong>Raza:</strong> ' + mascota.raza);
            var especieElement = $('<p />', { class: 'card-text' }).html('<strong>Especie:</strong> ' + mascota.especie);
            var razaColumn = $('<div />', { class: 'col-md-6' }).append(razaElement);
            var especieColumn = $('<div />', { class: 'col-md-6' }).append(especieElement);
            var infoRow = $('<div />', { class: 'row' }).append(razaColumn, especieColumn);
            var cardBody = $('<div />', { class: 'card-body' }).append(nombreElement, infoRow);
            var card = $('<div />', {
              class: 'card'
            }).append(img, cardBody);
            var cardCol = $('<div />', {
              class: 'col-md-4'
            }).append(card);
            $('#mascotas-container').prepend(cardCol); // Agregar la mascota al principio del contenedor
            // Mostrar mensaje de éxito con SweetAlert
            Swal.fire({
              icon: 'success',
              title: 'Mascota creada correctamente',
              showConfirmButton: false,
              timer: 1500
            });
            // Cerrar el modal
            $('#addPetModal').modal('hide');
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



