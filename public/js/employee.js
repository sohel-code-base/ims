function preview(thisObj, previewArea){
    let file = thisObj.get(0).files[0];

    if (file){
        if (file.size > 102400){
            previewArea.html('<small class="form-text text-danger">Please upload less than 100KB!</small>');
        }else {
            let reader = new FileReader();
            reader.onload = function(){
                previewArea.html('<img src="'+ reader.result +'" alt="">');
            }

            reader.readAsDataURL(file);
        }
    }
}

$('#uploadPhoto').on('change', function (e) {
    let previewArea = $('.preview-user-photo');
    preview($(this), previewArea);
})

$('#uploadSignature').on('change', function (e) {
    let previewArea = $('.preview-user-signature');
    preview($(this), previewArea);
})