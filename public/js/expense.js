$(document).ready(function () {
    let $expenseForm = $('form#expenseForm');
    $expenseForm.on('submit', function (e) {
        e.preventDefault();
        let url = $(this).attr('action');
        let type = $(this).attr('method');
        let data = {};

        $(this).find('[name]').each(function (index, value) {
            let name = $(this).attr('name');
            data[name] = $(this).val();

        });

        $.ajax({
            url: url,
            type: type,
            data: data,
            success: function (response) {
                if (response.status === 200){
                    $('form#expenseForm')[0].reset();
                    $('#expenseModal').modal('hide');
                }
            }
        });
    });
});