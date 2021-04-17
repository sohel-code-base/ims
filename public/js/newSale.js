//Show customer Details
$(document).on('change','#customer', function (event) {
    $('#customerPhone').val('');
    $('#customerAddress').val('');
    $(this).attr('disabled','disabled'); // Deactivate customer dropdown
    $('.saleDate').removeAttr('disabled'); //Active Date field

    const customerId = $(this).val();
    let route = Routing.generate('new_sale_customer_details',{'id':customerId});
    if (customerId.length !== 0){
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








//show product on change customer and sale date in list
$(document).on('change','.saleDate',function () {
    let customerId = $('#customer').val();
    let saleDate = $('.saleDate').val();

    // Added print button data value
    $('.show-sale-details').attr('data-customer-id', customerId);
    $('.show-sale-details').attr('data-order-date', saleDate);

    let route = Routing.generate('collect_product_customer_and_sale_date');

    let productSaleList = $('#productSaleList');
    let itemTr = '';

    productSaleList.find('tbody').empty();

    $.ajax({
        url: route,
        type: 'GET',
        data: {customerId: customerId, saleDate: saleDate},
        success: function (response) {
            if (response.length !== 0){

                // Activate receipt button
                $('.show-sale-details').removeClass('disabled');

                productSaleList.find('tfoot').remove();

                $.each(response,function (key, value) {
                    itemTr = "<tr>" +
                        "<td>" + response[key].productName + "</td>" +
                        "<td>" + response[key].quantity + " pcs" + "</td>" +
                        "<td>" + response[key].perPcsPrice + " tk" + "</td>" +
                        "<td>" + response[key].watt + "</td>" +
                        "<td>" + response[key].totalPrice + " tk" + "</td>" +
                        "<td class='removeProductFromSaleList' data-customer-id=" + customerId + " data-product-id=" + response[key].productPurchaseId + " data-sale-date=" + saleDate + " style='cursor: pointer'><i class='fa fa-remove'></i></td>" +
                        "</tr>";
                    productSaleList.find('tbody').append(itemTr);
                })
            }else {
                productSaleList.find('tfoot tr').remove();
                itemTr = "<tr>" +
                    "<td colspan='10' align='center'>Item not added yet!</td>" +
                    "</tr>";
                productSaleList.find('tfoot').append(itemTr);
            }
        }
    })
})









//Remove product from sale list
$(document).on('click','.removeProductFromSaleList',function (event) {
  let route = Routing.generate('remove_product_from_sale_list');
  let customerId = $(this).attr('data-customer-id');
  let productPurchaseId = $(this).attr('data-product-id');
  let saleDate = $(this).attr('data-sale-date');

  $.ajax({
      url: route,
      type: 'POST',
      data: {customerId: customerId, productPurchaseId: productPurchaseId, saleDate: saleDate},
      success: function (response) {
          if (response === 'success'){
              $("#productSaleList table tbody").find("[data-product-id='" + productPurchaseId + "']").closest('tr').hide();

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








// Fill up product details after selecting product
$(document).on('change', '#newSaleSelectProduct', function (event) {
    let id = $(this).val();
    let route = Routing.generate('new_sale_product_details', {id:id});

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
$(document).on('click','#addProduct',function (event) {
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
    data['watt'] = $('#watt').val();
    // data['status'] = $('#status').val();

    alert(data['watt']);

    if(data['customerId'] ==='' || data['product'] ==='' || data['quantity'] ==='' || data['perPiecePrice'] ===''|| data['saleDate'] ===''){
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
    }else {
        $.ajax({
            url : route,
            type: 'POST',
            async: true,
            data: data,
            success: function (response) {
                if (response.status === 'success'){
                    let productSaleList = $('#productSaleList');
                    let itemTr = '';

                    // console.log(response);

                    itemTr = "<tr>" +
                        "<td>" + response.productName + "</td>" +
                        "<td>" + response.quantity + " pcs" + "</td>" +
                        "<td>" + response.perPiecePrice + " tk" + "</td>" +
                        "<td>" + response.watt + "</td>" +
                        "<td>" + response.totalPrice + " tk" + "</td>" +
                        "<td class='removeProductFromSaleList' data-customer-id=" + data['customerId'] + " data-product-id=" + data['productPurchaseId'] + " data-sale-date=" + data['saleDate'] + " style='cursor: pointer'><i class='fa fa-remove'></i></td>" +
                        "</tr>"
                    productSaleList.find('tbody').append(itemTr);
                    productSaleList.find('tfoot').remove();


                    // Activate receipt button
                    $('.show-sale-details').removeClass('disabled');

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