$(document).ready(function () {
    $('.show-sale-details').on('click',function (event) {

        const route = $(this).attr('data-action');
        const cId = $(this).attr('data-customer-id');
        const oDate = $(this).attr('data-order-date');

        $.ajax({
            url: route,
            type: 'GET',
            async: true,
            data: {'customerId':cId, 'orderDate':oDate},
            success: function (response) {
                let detailsModal = $('#saleDetails');
                let trHtml = '';
                let totalAmount = 0;
                let lastTr = '';
                detailsModal.find('.modal-body tbody').empty();

                $.each(response, function(key, value) {
                    if(!$.trim(response[key].watt)){
                        response[key].watt = '';
                    }else {
                        response[key].watt = "(" + response[key].watt + " watt)";
                    }

                    totalAmount = totalAmount + response[key].totalPrice;
                    trHtml = "<tr>" +
                        "<td>" + response[key].productName + response[key].watt + "</td>" +
                        "<td>" + response[key].quantity + " pcs" + "</td>" +
                        "<td>" + response[key].perPcsPrice + " tk" + "</td>" +
                        "<td>" + response[key].totalPrice + " tk" + "</td>" +
                        "</tr>";
                    detailsModal.find('.modal-body tbody').append(trHtml);
                    // console.log(totalAmount);

                })
                lastTr = "<tr>" +
                    "<td colspan=3 style='font-weight: bold'> Total </td>" +
                    "<td style='font-weight: bold'>" + totalAmount + " tk" + "</td>" +
                    "</tr>";
                detailsModal.find('.modal-body tbody').append(lastTr);

                detailsModal.modal('show');
            }
        })
    })
});