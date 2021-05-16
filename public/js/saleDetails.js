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
                let employeeDetails = '';
                // let totalAmount = 0;
                let productTotalAmount = '';
                // let date = new Date(oDate);
                // let formattedDate = ('0' + date.getDate()).slice(-2) + "-" + ('0' + (date.getMonth()+1)).slice(-2) + "-" + date.getFullYear(); // add '0' on single digit date and remove '0' from double digit date like '011' with slice(-2)
                detailsModal.find('.modal-body tbody').empty();
                detailsModal.find('.modal-body thead.customerDetails').empty();

                // console.log(date);

                customerDetails = "<tr>" +
                    "<td>" + "<strong>Bill No: </strong>" + response[0].saleId + "</td>" +
                    "<td colspan= 2 style='text-align: right'>" + "<strong>Date: </strong>" + oDate + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<td width='30%'>" + "<strong>Name: </strong>" + response[0].customerName + "</td>" +
                    "<td>" + "<strong>Address: </strong>" + response[0].customerAddress + "</td>" +
                    "<td width='25%' align='right'>" + "<strong>Phone: </strong>" + response[0].customerPhone + "</td>" +
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
                productTotalAmount = "<tr><td colspan='5' border='0'></td></tr><tr>" +
                    "<td colspan='3' align='left'> Pay Amount:  <strong>" + (response[0].totalPrice - response[0].dueAmount) + " tk</strong>" + "&nbsp;&nbsp;" + "Due Amount: <strong>" + response[0].dueAmount + " tk" + "</strong></td>" +
                    "<td style='font-weight: bold' align='left'> Total Amount </td>" +
                    "<td style='font-weight: bold' align='right'>" + response[0].totalPrice + " tk" + "</td>" +
                    "</tr>";

                employeeDetails = '<div class="text-right seller-signature"><img src=/uploads/signature/' + response[0].employeeSignature + ' /><p><strong>' + response[0].employeeName + '</strong></p></div>';

                $('.signature-area').find('.seller-signature').remove();
                $('.signature-area').prepend(employeeDetails);
                detailsModal.find('.modal-body tbody.productDetails').append(productTotalAmount);
                detailsModal.find('.modal-body tbody.customerDetails').append(customerDetails);

                detailsModal.modal('show');
            }
        })
    })
});