<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        .cover-ilsya {
            height: 120px;
            width: 100%;
            object-fit: cover;
            object-position: center;
        }

        .judul-ilsya {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: center;
        }

        /* zoom hover*/
        .getfiles-ilsya {
            transition: all 0.3s ease-in-out;
        }

        .getfiles-ilsya:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="card">
        <nav class="navbar navbar-expand-lg bg-navbar-theme">
            <div class="container-fluid">
                <a class="navbar-brand" href="javascript:void(0)">File Manager</a>
                <a class="text-dark d-block d-lg-none" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbar-ex-5">
                    <i class="ti ti-menu-2 ti-md"></i>
                </a>

                <div class="collapse navbar-collapse" id="navbar-ex-5">
                    <ul class="navbar-nav ms-lg-auto">
                        <li class="nav-item">
                            <label class="btn btn-primary">
                                <i class="tf-icons navbar-icon ti ti-cloud-upload ti-xs me-1" style="margin-top: -2px;"></i> Uplaod Files
                                <form id="form-upload-media-ilsya" enctype="multipart/form-data">
                                    <input type="hidden" value="{{ $subfolder }}" name="subfolder">
                                    <input type="file" name="file" id="upload_ilsya_files" style="display: none;">
                                </form>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="card-body bg-lighter" id="app-ilsya-files-content" style="height: calc(100vh - 12rem) !important; overflow: auto;">
            <div class="row row-cols-2 row-cols-xl-5 row-cols-md-4 row-cols-sm-2 g-2">
                @foreach ($files as $file)
                    <div class="col">
                        <div class="card mb-2 getfiles-ilsya" data-url="{!! route('storage', 'url=' . $file['path']) !!}" data-path="{!! $file['path'] !!}" data-mime="{{ $file['mime'] }}" style="box-shadow: none; cursor: pointer;">
                            @if ($file['mime'] == 'image')
                                <img class="cover-ilsya rounded" src="{!! route('storage', 'url=' . $file['path']) !!}" alt="">
                            @else
                                <div class="cover-ilsya rounded d-flex align-items-center justify-content-center">
                                    <span class=" text-muted">{{ Str::upper($file['ext']) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="small judul-ilsya text-muted">{{ $file['filename'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-detail-ilsya-files" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" data-path="" class="btn btn-label-danger is-btn-delete-ilsya">Delete</button>
                </div>
            </div>
        </div>
    </div>

    @if ($ismain)
        <script>
            $(document).on('click', '.getfiles-ilsya', function() {
                // modal-detail-ilsya-files
                $("#modal-detail-ilsya-files").modal("show");
                var path = $(this).data('path');
                var url = "{{ route('storage') }}?url=" + path;
                var mime = $(this).data('mime');
                if (mime == 'image') {
                    var html = `
                    <div class="text-center">
                        <img src="${url}" alt="" class="img-fluid">
                    </div>
                `;
                } else if (mime == 'audio') {
                    var html = `
                    <div class="text-center">
                        <audio controls>
                            <source src="${url}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                `;
                } else if (mime == 'video') {
                    var html = `
                    <div class="text-center">
                        <video style="width: 100%;" controls>
                            <source  src="${url}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                `;
                } else {
                    var html = `
                    <div class="text-center">
                        <a href="${url}" class="btn btn-primary">Download</a>
                    </div>
                `;
                }
                $('#modal-detail-ilsya-files .modal-body').html(html);
                $('#modal-detail-ilsya-files .is-btn-delete-ilsya').data('path', path);
                $('#modal-detail-ilsya-files').on('hidden.bs.modal', function() {
                    $('#modal-detail-ilsya-files .modal-body').html('');
                })
            })


            $(document).on('click', '.is-btn-delete-ilsya', function() {
                var path = $(this).data('path');
                $.ajax({
                    url: "{{ route('ilsya.files.delete') }}",
                    type: "POST",
                    data: {
                        file: path,
                    },
                    beforeSend: function() {
                        $(".is-btn-delete-ilsya").html('Loading...');
                        $(".is-btn-delete-ilsya").attr('disabled', true);
                    },
                    success: function(data) {
                        $(".is-btn-delete-ilsya").html('Delete');
                        $(".is-btn-delete-ilsya").attr('disabled', false);
                        $(".getfiles-ilsya[data-path='" + path + "']").parent().remove();
                        $("#modal-detail-ilsya-files").modal('hide');
                    },
                    error: function(data) {
                        $(".is-btn-delete-ilsya").html('Delete');
                        $(".is-btn-delete-ilsya").attr('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            text: data.responseJSON.message ?? 'Something went wrong!',
                            showClass: {
                                popup: 'animate__animated animate__shakeX'
                            },
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                });
            });
        </script>
    @endif

    <script>
        $(document).on('change', '#upload_ilsya_files', function() {
            $("#form-upload-media-ilsya").submit();
        });

        $(document).on('submit', '#form-upload-media-ilsya', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('ilsya.files.upload') }}",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#upload_ilsya_files").attr('disabled', true);
                    loading_upload();
                    $(".is-loading-ilsya").show();
                },
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            $(".is-loading-ilsya-persen").html(percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(data) {
                    $("#upload_ilsya_files").attr('disabled', false);
                    $(".is-loading-ilsya").hide();
                    let content = '';
                    if (data.data.mime == 'image') {
                        content = '<img class="cover-ilsya rounded" src="{!! route('storage') !!}?url=' + data.data.path + '" alt="">'
                    } else {
                        content = '<div class="cover-ilsya rounded d-flex align-items-center justify-content-center"><span class=" text-muted">' + data.data.ext + '</span></div>'
                    }
                    var html = `
                    <div class="col">
                        <div class="card mb-2 getfiles-ilsya" data-url="{!! route('storage') !!}?url=${data.data.path}"  data-path="${data.data.path}" data-mime="${data.data.mime}" style="box-shadow: none; cursor: pointer;">
                            ${content}
                        </div>
                        <div class="small judul-ilsya text-muted">${data.data.filename}</div>
                    </div>
                    `;
                    $("#app-ilsya-files-content .row").prepend(html);
                    $("#upload_ilsya_files").val('');
                    loading_upload('hide');
                },
                error: function(data) {
                    $("#upload_ilsya_files").attr('disabled', false);
                    $(".is-loading-ilsya").hide();
                    Swal.fire({
                        icon: 'error',
                        text: data.responseJSON.message ?? 'Something went wrong!',
                        showClass: {
                            popup: 'animate__animated animate__shakeX'
                        },
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                    loading_upload('hide');
                }
            });
        });

        function loading_upload(status = 'show') {
            if (status == 'show') {
                $("#app-ilsya-files-content .row").prepend(`<div class="col is-loading-ilsya" style="display: none">
                    <div class="card mb-2" style="box-shadow: none; cursor: pointer; ">
                        <div class="cover-ilsya rounded d-flex align-items-center justify-content-center">
                            <span class=" text-muted is-loading-ilsya-persen">0%</span>
                        </div>
                    </div>
                    <div class="small judul-ilsya text-muted">Uploading....</div>
                </div>`)
            } else {
                $("#app-ilsya-files-content .row .is-loading-ilsya").remove();
            }
        }
    </script>
</body>

</html>
