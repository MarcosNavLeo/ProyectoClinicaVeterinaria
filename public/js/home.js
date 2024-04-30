$(document).ready(function () {
  var btnañadirMasco = $('#btnañadirMasco');
  $('#fechaNacimientoMascota').datepicker({
    dateFormat: 'dd-mm-yy',
    changeYear: true
  });
  btnañadirMasco.click(function () {
    $('#addPetModal').modal('show');
  });

  $('#addPetModal .btn-secondary, #addPetModal .close').click(function () {
    $('#addPetModal').modal('hide');
  });
  $.ajax({
    url: '/api/mascotas/' + idUser,
    type: 'GET',
    success: function (data) {
      var row = $('<div />', { class: 'row' });
      $.each(data, function (index, mascota) {
        var img = $('<img />', {
          src: mascota.foto,
          alt: mascota.nombre,
          class: 'card-img-top custom-img-size'
        });
        var nombre = $('<h5 />', { class: 'card-title' }).text(mascota.nombre);
        var raza = $('<p />', { class: 'card-text' }).html('<strong>Raza:</strong> ' + mascota.raza);
        var especie = $('<p />', { class: 'card-text' }).html('<strong>Especie:</strong> ' + mascota.especie);

        var razaColumn = $('<div />', { class: 'col-md-6' }).append(raza);
        var especieColumn = $('<div />', { class: 'col-md-6' }).append(especie);
        var infoRow = $('<div />', { class: 'row' }).append(razaColumn, especieColumn);

        var cardBody = $('<div />', { class: 'card-body' }).append(nombre, infoRow);
        var card = $('<div />', {
          class: 'card'
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

  
});


