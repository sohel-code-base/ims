$(document).ready(function () {
    $('.show-sale-details').on('click',function (event) {

        event.preventDefault();

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
                let productDetails = '';
                let customerDetails = '';
                // let totalAmount = 0;
                let productTotalAmount = '';
                let date = new Date(oDate);
                let formattedDate = ('0' + date.getDate()).slice(-2) + "-" + ('0' + (date.getMonth()+1)).slice(-2) + "-" + date.getFullYear(); // add '0' on single digit date and remove '0' from double digit date like '011' with slice(-2)
                detailsModal.find('.modal-body tbody').empty();
                detailsModal.find('.modal-body thead.customerDetails').empty();

                // console.log(response[0].orderDate);

                customerDetails = "<tr>" +
                    "<td colspan= 3 style='text-align: right'>" + "<strong>Date: </strong>" + formattedDate + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<td>" + "<strong>Name: </strong>" + response[0].customerName + "</td>" +
                    "<td>" + "<strong>Address: </strong>" + response[0].customerAddress + "</td>" +
                    "<td>" + "<strong>Phone: </strong>" + response[0].customerPhone + "</td>" +
                    "</tr>";

                $.each(response, function(key, value) {
                    if(!$.trim(response[key].watt)){
                        response[key].watt = '';
                    }else {
                        response[key].watt = "(" + response[key].watt + " watt)";
                    }

                    // totalAmount += Number(response[key].totalPrice);

                    productDetails = "<tr>" +
                        "<td>" + (key+1) + "</td>" +
                        "<td>" + response[key].productName + response[key].watt + "</td>" +
                        "<td align='center'>" + response[key].quantity + " pcs" + "</td>" +
                        "<td align='center'>" + response[key].perPcsPrice + " tk" + "</td>" +
                        "<td align='right'>" + response[key].price + " tk" + "</td>" +
                        "</tr>";
                    detailsModal.find('.modal-body tbody.productDetails').append(productDetails);
                    // console.log(response[key]);

                })
                productTotalAmount = "<tr>" +
                    "<td colspan=4 style='font-weight: bold' align='center'> Total </td>" +
                    "<td style='font-weight: bold' align='right'>" + response[0].totalPrice + " tk" + "</td>" +
                    "</tr>";
                detailsModal.find('.modal-body tbody.productDetails').append(productTotalAmount);
                detailsModal.find('.modal-body tbody.customerDetails').append(customerDetails);

                detailsModal.modal('show');
            }
        })
    })
});