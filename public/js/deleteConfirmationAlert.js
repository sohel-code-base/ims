$('.delete-record').on('click',function (e) {
    e.preventDefault();
    const href = $(this).attr('href')

    Swal.fire({
        title: 'Are you sure want to delete this record?',
        // text: "Are you sure want to delete this record?",
        // icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3c8dbc',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.location.href = href;
        }
    })

})