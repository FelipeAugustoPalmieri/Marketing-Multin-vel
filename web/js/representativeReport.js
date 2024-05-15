function searchCities() {
    if ($('#representativereportsearch-consumer_representative_id').val() != '') {
       $('#representativereportsearch-city_id').html('');
       $.getJSON('/sales-representative-cities/get-cities?id=' + $('#representativereportsearch-consumer_representative_id').val() , function (dados) {

           var totalCidades = Object.keys(dados).length;

           if (totalCidades > 1) {

               if (citySelected == '') {
                  $('#representativereportsearch-city_id').append('<option selected="selected" value="ALL">' + textoTodasCidades + '</option>');
               } else {
                  $('#representativereportsearch-city_id').append('<option value="ALL">' + textoTodasCidades + '</option>');
               }

               $.each(dados, function(index, value) {
                  if (citySelected == index) {
                      $('#representativereportsearch-city_id').append('<option selected="selected" value="' + index + '">' + value + '</option>');
                  } else {
                      $('#representativereportsearch-city_id').append('<option value="' + index + '">' + value + '</option>');
                  }
               });

           } else {
               $.each(dados, function(index, value) {
                  $('#representativereportsearch-city_id').append('<option selected="selected" value="' + index + '">' + value + '</option>');
               });
           }
       });
    }
}

$(document).ready(function() {
    searchCities();
});

$('#representativereportsearch-consumer_representative_id').change(function() {
    searchCities();
});
