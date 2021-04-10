//Show customer Details

$(document).on('change','#customer', function (event) {
    $('#customerPhone').val('');
    $('#customerAddress').val('');

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


//Show product details
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
    data['product'] = $('#newSaleSelectProduct').val();
    data['quantity'] = $('#quantity').val();
    data['perPicePrice'] = $('#salePrice').val();
    data['watt'] = $('#watt').val();
    // data['status'] = $('#status').val();

    if(data['customerId'] ==='' || data['product'] ==='' || data['quantity'] ==='' || data['perPicePrice'] ===''){
        Swal.fire({
            title: 'Please enter required field.',
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
                if (response === 'success'){
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