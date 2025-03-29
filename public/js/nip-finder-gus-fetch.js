jQuery(document).ready(function ($) {
    const nip_field = $('#billing_nip_field');

    if (nip_field.length) {
        nip_field.append('<a href="#" id="fetch-gus-data" class="button-link">Pobierz dane z GUS</a>');
    }

    $('#fetch-gus-data').on('click', function (e) {
        e.preventDefault();

        let nip = $('#billing_nip').val();

        if (!nip || !/^\d{10}$/.test(nip)) {
            alert('Proszę wprowadzić poprawny NIP (10 cyfr).');
            return;
        }

        $.ajax({
            url: nip_finder_gus_ajax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'nip_finder_fetch_gus_data',
                nip: nip,
                nonce: nip_finder_gus_ajax.nonce,
            },
            beforeSend: function () {
                $('#fetch-gus-data').text('Pobieranie...');
            },
            success: function (response) {
                $('#fetch-gus-data').text('Pobierz dane z GUS');
                if (response.success) {
                    $('#billing_first_name').val(response.data.first_name);
                    $('#billing_last_name').val(response.data.last_name);
                    $('#billing_company').val(response.data.company);
                    $('#billing_address_1').val(response.data.address);
                    $('#billing_city').val(response.data.city);
                    $('#billing_postcode').val(response.data.postcode);
                } else {
                    alert(response.data.message || 'Nie udało się pobrać danych.');
                }
            },
            error: function () {
                $('#fetch-gus-data').text('Pobierz dane z GUS');
                alert('Wystąpił błąd podczas pobierania danych.');
            }
        });
    });
});
