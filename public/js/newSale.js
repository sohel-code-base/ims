//Show customer Details
$(document).on('change', '#customer', function (event) {
    $('#customerPhone').val('');
    $('#customerAddress').val('');
    $(this).prop('disabled', true); // Deactivate customer dropdown
    $('.saleDate').removeAttr('disabled'); //Active Date field

    const customerId = $(this).val();
    let route = Routing.generate('new_sale_customer_details', {'id': customerId});
    if (customerId.length !== 0) {
        $.ajax({
            url: route,
            type: 'GET',
            async: true,
            success: function (response) {
                $('#customerPhone').val(response.phone);
                $('#customerAddress').val(response.address);
            }
        })
    }
})


//show products in list after changing customer and sale date
$(document).on('change', '.saleDate', function () {
    let customerId = $('#customer').val();
    let saleDate = $('.saleDate').val();
    let productSaleList = $('#productSaleList');


    // Added Receipt button data value
    productSaleList.find('.show-sale-details').attr('data-customer-id', customerId);
    productSaleList.find('.show-sale-details').attr('data-order-date', saleDate);

    let route = Routing.generate('collect_product_customer_and_sale_date');

    let itemTr = '';

    productSaleList.find('tbody').empty();

    $.ajax({
        url: route,
        type: 'GET',
        data: {customerId: customerId, saleDate: saleDate},
        success: function (response) {
            if (response.length !== 0) {

                // Show receipt button
                productSaleList.find('.show-sale-details').removeClass('hide');

                productSaleList.find('#totalPrice span').text(response[0].totalPrice);

                productSaleList.find('tfoot').remove();

                $.each(response, function (key, value) {
                    if (!$.trim(response[key].watt)) {
                        response[key].watt = 'N/A';
                    }
                    itemTr = "<tr>" +
                        "<td>" + response[key].productName + "</td>" +
                        "<td>" + response[key].quantity + " pcs" + "</td>" +
                        "<td>" + response[key].perPcsPrice + " tk" + "</td>" +
                        "<td>" + response[key].watt + "</td>" +
                        "<td>" + response[key].price + " tk" + "</td>" +
                        // "<td class='removeProductFromSaleList' data-customer-id=" + customerId + " data-product-id=" + response[key].productPurchaseId + " data-sale-date=" + saleDate + " style='cursor: pointer'><i class='fa fa-remove'></i></td>" +
                        "<td></td>" +
                        "</tr>";
                    productSaleList.find('tbody').append(itemTr);
                    productSaleList.find('#dueAmount span').text(response[0].dueAmount);
                    productSaleList.find('.pay-amount').attr('data-sale-id', response[0].saleId);

                })
            } else {
                // Hide receipt button
                productSaleList.find('.show-sale-details').addClass('hide');
            }
        }
    })
})


// Fill up product details after selecting product
$(document).on('change', '#newSaleSelectProduct', function (event) {
    let id = $(this).val();
    let route = Routing.generate('new_sale_product_details', {id: id});

    $('#purchasePrice').val('');
    $('#quantity').val('');
    $('#salePrice').val('');
    $('#watt').val('');

    $.ajax({
        url: route,
        type: 'GET',
        async: true,
        success: function (response) {
            $('#purchasePrice').val(response.purchasePrice)
            $('#quantity').val(response.quantity);
            $('#salePrice').val(response.salePrice);
            $('#watt').val(response.watt);
        }
    })
})


//Add Product in list
$(document).on('click', '#addProduct', function (event) {
    $('form').submit(function (event) {
        event.preventDefault();
    })

    let data = {};
    let route = Routing.generate('store_new_sale_record');

    data['customerId'] = $('#customer').val();
    data['saleDate'] = $('.saleDate').val();
    data['productPurchaseId'] = $('#newSaleSelectProduct').val();
    data['quantity'] = $('#quantity').val();
    data['perPiecePrice'] = $('#salePrice').val();
    // data['watt'] = $('#watt').val();
    // data['status'] = $('#status').val();

    if (data['customerId'] === '' || data['product'] === '' || data['quantity'] === '' || data['perPiecePrice'] === '' || data['saleDate'] === '') {
        Swal.fire({
            title: 'Please fill all fields.',
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonColor: '#d33',
            cancelButtonText: 'Ok',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        })
        return false;
    } else {
        $.ajax({
            url: route,
            type: 'POST',
            async: true,
            data: data,
            success: function (response) {
                if (response.status === 'success') {
                    let itemTr = '';

                    let productSaleList = $('#productSaleList');

                    if (!$.trim(response.watt)) {
                        response.watt = 'N/A';
                    }

                    itemTr = "<tr>" +
                        "<td>" + response.productName + "</td>" +
                        "<td>" + response.quantity + " pcs" + "</td>" +
                        "<td>" + response.perPiecePrice + " tk" + "</td>" +
                        "<td>" + response.watt + "</td>" +
                        "<td>" + response.price + " tk" + "</td>" +
                        "<td class='removeProductFromSaleList' data-sale-id=" + response.saleId + " data-product-id=" + data['productPurchaseId'] + " data-sale-date=" + data['saleDate'] + " style='cursor: pointer'><i class='fa fa-remove'></i></td>" +
                        "</tr>";

                    productSaleList.find('.pay-amount').attr('data-sale-id', response.saleId);
                    productSaleList.find('#paymentBtn').removeClass('hide');
                    productSaleList.find('tfoot').remove();
                    productSaleList.find('tbody').append(itemTr);
                    productSaleList.find('#totalPrice span').text(response.totalPrice);
                    productSaleList.find('#dueAmount span').text(response.dueAmount);


                    // Show receipt button
                    // productSaleList.find('.show-sale-details').removeClass('hide');


                    //Item added notification
                    toastr["success"]("New Item added!")

                    toastr.options = {
                        "closeButton": false,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-top-center",
                        "preventDuplicates": true,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    }
                }
            }
        })
    }
})

//Remove product from sale list
$(document).on('click', '.removeProductFromSaleList', function (event) {
    let route = Routing.generate('remove_product_from_sale_list');
    let saleId = $(this).attr('data-sale-id');
    let productPurchaseId = $(this).attr('data-product-id');
    // let saleDate = $(this).attr('data-sale-date');

    $.ajax({
        url: route,
        type: 'POST',
        data: {saleId: saleId, productPurchaseId: productPurchaseId},
        success: function (response) {
            if (response.status === 200) {
                $('#productSaleList').find('#totalPrice span').text(response.totalPrice);
                $('#productSaleList').find('#dueAmount span').text(response.dueAmount);

                $("#productSaleList table tbody").find("[data-product-id='" + productPurchaseId + "']").closest('tr').remove();
                let productRows = $("#productSaleList table tbody tr").length;
                if (productRows === 0) {
                    $('#productSaleList').find('.show-sale-details').addClass('hide');
                }

/*                if (productRows === response.productCount){
                    $('#productSaleList').find('#paymentBtn').removeClass('hide');

                }*/

                //Item added notification
                toastr["error"]("Item Deleted!")

                toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-center",
                    "preventDuplicates": true,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }
            }

        }
    })
})

// Prevent Enter key on Sale Area
$(document).on('keypress', function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
    }
})

// Update Due amount
$(document).on('click', '#paymentTransfar', function (e) {
    let payAmount = $('.pay-amount').val();
    let url = $('.pay-amount').attr('data-action');
    let saleId = $('.pay-amount').attr('data-sale-id');
    if (!$.trim(payAmount)) {
        payAmount = 0;
    }

    $.ajax({
        url: url,
        type: 'POST',
        data: {saleId: saleId, payAmount: payAmount},
        success: function (response) {
            if (response.status == 200) {
                $('#productSaleList').find('#dueAmount span').text(response.dueAmount);
                $('#productSaleList').find('#paymentBtn').addClass('hide');
                $('#productSaleList').find('#paymentSection').addClass('hide');
                $('#productSaleList').find('#thanks').removeClass('hide');
                $('#productSaleList').find('.show-sale-details').removeClass('hide');
                $('#productSaleList').find('.removeProductFromSaleList').addClass('hide');
            }
        }
    })
})

$(document).on('click', '#paymentBtn', function (e) {
    $('#paymentSection').removeClass('hide');
    $('#productSaleForm').find('#addProduct').remove();

})
