function preview(thisObj, previewArea){
    let file = thisObj.get(0).files[0];

    if (file){
        if (file.size > 102400){
            previewArea.html('<small class="form-text text-danger">Please upload less than 100KB!</small>');
        }else {
            let reader = new FileReader();

            reader.onload = function(){
                previewArea.attr("src", reader.result);
                previewArea.removeClass('hide');
            }

            reader.readAsDataURL(file);
        }
    }
}

$('#uploadPhoto').on('change', function (e) {
    let previewArea = $('.preview-user-photo').find('img');
    preview($(this), previewArea);
})

$('#uploadSignature').on('change', function (e) {
    let previewArea = $('.preview-user-signature').find('img');
    preview($(this), previewArea);
})