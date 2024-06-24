
$(document).on('click', ".filemanagerilsya", function () {
    $("#modal-files").modal('show')
    files.init({
        body: "#modal-files .modal-body",
    })
});

$(document).on('click', '.getfiles-ilsya', function () {
    var src = $(this).data('url');
    $("input[name='media']").val(src)
    $("#modal-files").modal('hide')
})

$("select[name='message_type']").on('change', function () {
    var value = $(this).val();
    let html = '<div class="row">';
    switch (value) {
        case "text":
            html += '<div class="col-12"><div class="mb-3"><label class="form-label">Message</label><textarea name="message" rows="6" class="form-control" required></textarea></div></div>';
            break;
        case "media":
            html += '<div class="col-12 col-xl-6 col-lg-6"><div class="mb-3"><label class="form-label">Media</label><div class="input-group"><input type="text" class="form-control" name="media" required><button class="btn btn-primary waves-effect filemanagerilsya" type="button">Upload</button></div></div></div>';
            html += '<div class="col-12 col-xl-6 col-lg-6"><div class="mb-3"><label class="form-label">Media Mime</label><select name="media_type" required class="form-select"><option value="">-- Select One --</option><option value="image">Image</option><option value="video">Video</option><option value="audio">Audio</option><option value="file">File</option></select></div></div>';
            html += '<div class="col-12"><div class="mb-3"><label class="form-label">Message</label><textarea name="message" rows="6" class="form-control"></textarea></div></div>';
            break;
        case "button":
            html += '<div class="col-12"><div class="mb-3"><label class="form-label">Message</label><textarea name="message" rows="6" class="form-control" required></textarea></div></div>';
            html += '<div class="col-12"><div class="mb-3"><label class="form-label">Footer</label><input name="footer" class="form-control" required autocomplete="off"></div></div>';
            tbody = '<tr><td><input type="text" name="btn_display[]" class="form-control" placeholder="Ex: Menu Click Me" required=""></td><td><input type="text" name="btn_id[]" placeholder="Ex: !menu" class="form-control" required=""></td><td><button class="btn btn-label-danger is-delete" type="button"><i class="ti ti-trash-x"></i></button></td></tr>';
            html += '<div class="col-12"><div class="table_button_message table-btn"><div class="text-end"><button type="button" class="btn btn-label-primary is-add">Add Button</button></div><div class="table-responsive text-nowrap"><table class="table table-striped"><thead><tr><th>Display Text</th><th>Id</th><th>Action</th></tr></thead><tbody class="table-border-bottom-0 is-content">' + tbody + '</tbody></table></div></div></div>';
            break;
        case "list":
            html += '<div class="col-12"><div class="mb-3"><label class="form-label">Title</label><input name="title" class="form-control" autocomplete="off"></div></div>';
            html += '<div class="col-12"><div class="mb-3"><label class="form-label">Message</label><textarea name="message" rows="6" class="form-control" required></textarea></div></div>';
            html += '<div class="col-12"><div class="mb-3"><label class="form-label">Footer</label><input name="footer" class="form-control" required autocomplete="off"></div></div>';
            html += '<div class="col-12"><div class="mb-3"><label class="form-label">Button Text</label><input name="button_text" class="form-control" required placeholder="Click Here" autocomplete="off"></div></div>';
            tbody = '<tr><td><select name="type[]" class="form-select" required=""><option value="option">Option</option><option value="section">Section</option></select></td><td><input type="text" name="btn_display[]" class="form-control" placeholder="Ex: Menu Click Me" required=""></td><td data-input-btn-id><input type="text" name="btn_id[]" placeholder="Ex: !menu" class="form-control" required=""></td><td><button class="btn btn-label-danger is-delete" type="button"><i class="ti ti-trash-x"></i></button></td></tr>';
            html += '<div class="col-12"><div class="table_button_message table-list-btn"><div class="text-end"><button type="button" class="btn btn-label-primary is-add">Add Button</button></div><div class="table-responsive text-nowrap"><table class="table table-striped"><thead><tr><th>Type</th><th>Display Text</th><th>Id</th><th>Action</th></tr></thead><tbody class="table-border-bottom-0 is-content">' + tbody + '</tbody></table></div></div></div>';
            break;
    }
    html += '</div>';

    $("#message-content").html(html)
});

// if name type[] change
$(document).on('change', '.table-list-btn select[name="type[]"]', function () {
    // if value section disable btn_id
    if ($(this).val() == "section") {
        // hidden data-input-btn-id
        $(this).parent().parent().find('td[data-input-btn-id] input').hide()
        $(this).parent().parent().find('td[data-input-btn-id]').append('<div class="text-danger">-</div>')
        $(this).parent().parent().find('input[name="btn_id[]"]').val('Disabled')
    } else {
        // show
        $(this).parent().parent().find('td[data-input-btn-id] input').show()
        $(this).parent().parent().find('td[data-input-btn-id] div').remove()
        $(this).parent().parent().find('input[name="btn_id[]"]').val('')
    }
})

$(document).on('click', '.table-list-btn .is-add', function () {
    var html = '<tr>';
    html += '<td><select name="type[]" class="form-select" required=""><option value="option">Option</option><option value="section">Section</option></select></td>'
    html += '<td><input type="text" name="btn_display[]" class="form-control" required></td>'
    html += '<td data-input-btn-id><input type="text" name="btn_id[]" class="form-control" required></td>'
    html += '<td><button class="btn btn-label-danger is-delete" type="button"><i class="ti ti-trash-x"></i></button></td>'
    html += '</tr>';
    $(".table-list-btn .is-content").append(html)
});


$(document).on('click', '.table-btn .is-add', function () {
    var html = '<tr>';
    html += '<td><input type="text" name="btn_display[]" class="form-control" required></td>'
    html += '<td><input type="text" name="btn_id[]" class="form-control" required></td>'
    html += '<td><button class="btn btn-label-danger is-delete" type="button"><i class="ti ti-trash-x"></i></button></td>'
    html += '</tr>';
    // max 3 button
    if ($(".table-btn .is-content tr").length == 3) {
        return Swal.fire({
            icon: 'info',
            text: 'You can only have 3 buttons.',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        })
    }
    $(".table-btn .is-content").append(html)
});

$(document).on('click', '.table_button_message .is-delete', function () {
    if ($(".table_button_message .is-content tr").length == 1) {
        return Swal.fire({
            icon: 'info',
            text: 'You must have at least one button.',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        })
    }
    $(this).parents('tr').remove();
});
