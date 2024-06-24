// SCRIPT BY ILSYA @ 2023
// WEBSITE: https://velixs.com
// GITHUB: https://github.com/ilsyaa

// 1.0.0

class velixs {
    constructor() {

    }

    datatables({
        target = ".datatables-basic",
        url = "",
        columns = [],
        url_delete = false,
        header = "List",
        btn = false,
        message_delete = "You won't be able to revert this!"
    }) {
        if (btn) {
            btn = btn;
        } else {
            if (url_delete) {
                btn = [{
                    text: '<i class="ti ti-trash me-sm-1"></i> <span class="d-none d-sm-inline-block">Delete</span>',
                    className: 'is-button-delete btn btn-danger me-2 btn-label-danger'
                }];
            } else {
                btn = [{
                    text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New</span>',
                    className: 'is-button-add btn btn-primary me-2 btn-primary'
                }];
            }
        }

        if (url_delete) {
            var table = $(target).DataTable({
                processing: true,
                serverSide: true,
                ajax: url,
                columns: columns,
                buttons: btn,
                dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                displayLength: 10,
                columnDefs: [{
                    className: 'control',
                    orderable: false,
                    responsivePriority: 2,
                    targets: 0
                }, {
                    targets: 1,
                    orderable: false,
                    responsivePriority: 3,
                    render: function (data, type, full, meta) {
                        return (
                            '<div class="form-check"> <input class="form-check-input dt-checkboxes is-checkbox-delete" type="checkbox" data-id="' + full.id + '" /><label class="form-check-label" for="checkbox' + full.id + '"></label></div>'
                        );
                    },
                    checkboxes: {
                        selectAllRender: '<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>'
                    }
                }, {
                    targets: 2,
                    responsivePriority: 2,
                }],
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                }
            });
            $('.is-button-delete').on('click', function () {
                var id = [];
                $('.is-checkbox-delete:checked').each(function (i) {
                    id[i] = $(this).data('id');
                });
                if (id.length === 0) {
                    Swal.fire({
                        text: 'Please select at least one checkbox',
                        icon: 'warning',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        showClass: {
                            popup: 'animate__animated animate__shakeX'
                        },
                        buttonsStyling: false
                    });
                } else {
                    Swal.fire({
                        text: message_delete,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-outline-danger ms-1'
                        },
                        buttonsStyling: false
                    }).then(function (result) {
                        if (result.value) {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                url: url_delete,
                                method: 'POST',
                                data: {
                                    id: id
                                },
                                beforeSend: function () {
                                    Swal.fire({
                                        html: '<div class="d-flex justify-content-center"><div class="sk-grid sk-secondary"><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div></div></div><br>Please wait a moment...',
                                        allowOutsideClick: false,
                                        buttonsStyling: false,
                                        showConfirmButton: false,
                                    });
                                },
                                success: function (data) {
                                    table.ajax.reload();
                                    Swal.fire({
                                        icon: 'success',
                                        text: data.message ?? 'Data has been deleted!',
                                        customClass: {
                                            confirmButton: 'btn btn-success'
                                        }
                                    });
                                    $('.is-checkbox-delete').trigger('click');
                                },
                                error: function (data) {
                                    if (data.responseJSON.message) {
                                        Swal.fire({
                                            icon: 'error',
                                            text: data.responseJSON.message,
                                            showClass: {
                                                popup: 'animate__animated animate__shakeX'
                                            },
                                            customClass: {
                                                confirmButton: 'btn btn-primary'
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            text: 'Something went wrong!',
                                            showClass: {
                                                popup: 'animate__animated animate__shakeX'
                                            },
                                            customClass: {
                                                confirmButton: 'btn btn-primary'
                                            }
                                        });
                                    }
                                }
                            });

                        }
                    });
                }
            });
        } else {
            var table = $(target).DataTable({
                processing: true,
                serverSide: true,
                ajax: url,
                columns: columns,
                buttons: btn,
                dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                displayLength: 10,
                columnDefs: [{
                    className: 'control',
                    orderable: false,
                    responsivePriority: 2,
                    targets: 0
                }, {
                    targets: 1,
                    responsivePriority: 2,
                }],
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                }
            });
        }
        $('div.head-label').html('<h5 class="card-title mb-0">' + header + '</h5>');
        return table;
    }


    ajax({
        url,
        data,
        type = 'POST',
        success,
        error,
        cache,
        processData = true,
        contentType,
        addons_success = () => { },
        addons_error = () => { },
        beforeSend,
        addons_beforeSend = () => { },
    }) {
        $.ajax({
            url: url,
            type: type,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: cache ?? true,
            processData: processData,
            contentType: contentType ? contentType : 'application/x-www-form-urlencoded; charset=UTF-8',
            data: data,
            beforeSend: function () {
                if (typeof beforeSend == 'function') {
                    beforeSend()
                } else {
                    Swal.fire({
                        html: '<div class="d-flex justify-content-center"><div class="sk-grid sk-secondary"><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div></div></div><br>Please wait a moment...',
                        allowOutsideClick: false,
                        buttonsStyling: false,
                        showConfirmButton: false,
                    });
                    addons_beforeSend();
                }
            },
            success: function (res) {
                if (typeof success == 'function') {
                    success(res)
                } else {
                    let swal = Swal.fire({
                        icon: 'success',
                        text: res.message,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false,
                        timer: 1200,
                    });
                    addons_success({ res, swal });
                }
            },
            error: function (res) {
                if (typeof error == 'function') {
                    error(data)
                } else {
                    if (res.responseJSON) {
                        Swal.fire({
                            icon: 'error',
                            text: res.responseJSON.message,
                            showClass: {
                                popup: 'animate__animated animate__shakeX'
                            },
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: 'Something went wrong!',
                            showClass: {
                                popup: 'animate__animated animate__shakeX'
                            },
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        })
                    }
                    addons_error(res);
                }
            }
        });
    }

    xhr_progess({ xhr, target }) {
        xhr.upload.addEventListener("progress", function (evt) {
            if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                percentComplete = parseInt(percentComplete * 100);
                $(target).html(percentComplete + '%');
            }
        }, false);
        return xhr;
    }


    swal_success({ message, timer }) {
        return Swal.fire({
            icon: 'success',
            text: message ?? 'Success',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false,
            timer: timer ?? 1200,
        });
    }

    swal_error({ message }) {
        return Swal.fire({
            icon: 'error',
            text: message ?? 'Something went wrong!',
            showClass: {
                popup: 'animate__animated animate__shakeX'
            },
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        })
    }

    swal_loading({ message }) {
        let msg = message ?? 'Please wait a moment...';
        return Swal.fire({
            html: '<div class="d-flex justify-content-center"><div class="sk-grid sk-secondary"><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div></div></div><br>' + msg,
            allowOutsideClick: false,
            buttonsStyling: false,
            showConfirmButton: false,
        });
    }
}
