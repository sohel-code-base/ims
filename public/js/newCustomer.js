$(document).ready(function () {
    $('#newCustomerForm').on('submit', function (e) {
        e.preventDefault();
        let url = $(this).attr('action');
        let type = $(this).attr('method');
        let data = {};

        $(this).find('[name]').each(function (index, value) {
            let name = $(this).attr('name');
            let fieldValue = $(this).val();

            data[name] = fieldValue;

        });

        $.ajax({
            url: url,
            type: type,
            data: data,
            success: function (response) {
                if (response.status === 200){
                    localStorage.setItem('status', response.status);
                    location.reload(true);
                }
            }
        });
    });


    if(localStorage.getItem("status")) {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        toastr.success("New Customer Added!");

        localStorage.clear();
    }
});