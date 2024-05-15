function searchCities() {
    if ($('#representativecomissionsearch-consumer_representative_id').val() != '') {
       $('#representativecomissionsearch-city_id').html('');
       $.getJSON('/sales-representative-cities/get-cities?id=' + $('#representativecomissionsearch-consumer_representative_id').val() , function (dados) {

           var totalCidades = Object.keys(dados).length;

           if (totalCidades > 1) {

               if (citySelected == '') {
                  $('#representativecomissionsearch-city_id').append('<option selected="selected" value="ALL">' + textoTodasCidades + '</option>');
               } else {
                  $('#representativecomissionsearch-city_id').append('<option value="ALL">' + textoTodasCidades + '</option>');
               }

               $.each(dados, function(index, value) {
                  if (citySelected == index) {
                      $('#representativecomissionsearch-city_id').append('<option selected="selected" value="' + index + '">' + value + '</option>');
                  } else {
                      $('#representativecomissionsearch-city_id').append('<option value="' + index + '">' + value + '</option>');
                  }
               });

           } else {
               $.each(dados, function(index, value) {
                  $('#representativecomissionsearch-city_id').append('<option selected="selected" value="' + index + '">' + value + '</option>');
               });
           }
       });
    }
}

$(document).ready(function() {
    searchCities();
});

$('#representativecomissionsearch-consumer_representative_id').change(function() {
    searchCities();
});
