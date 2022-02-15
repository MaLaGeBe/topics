<!-- 封面图裁剪 -->
<div class="modal fade" id="modal" tabindex="-2" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">裁剪并上传</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-11">
                            <img src="" id="sample_image" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="bt btn-sm btn-secondary" data-dismiss="modal">取消</button>
                <button type="button" id="crop" class="btn btn-sm btn-success">保存</button>
            </div>
        </div>
    </div>
</div>
<style>
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    }

    .card .card-header {
        font-weight: 500;
    }

    .card:not([class*=bg-]) .card-header {
        color: #0061f2;
    }

    .card.bg-dark .card-header,
    .card.bg-dark .card-footer {
        border-color: rgba(255, 255, 255, 0.15);
    }

    .card .card-header .card-header-tabs .nav-link.active {
        background-color: #fff;
        border-bottom-color: #fff;
    }

    .card-header-actions .card-header {
        height: 3.5625rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 0.5625rem;
        padding-bottom: 0.5625rem;
    }

    .card-header-actions .card-header .dropdown-menu {
        margin-top: 0;
        top: 0.5625rem !important;
    }

    .card-scrollable .card-body {
        max-height: 30rem;
        overflow-y: auto;
    }
</style>
<script>
    $("#menu_category_ext").addClass('active');
    $("#menu_ext").addClass('show');
    $("#menu_topics").addClass('active');
    setTimeout(hideActived, 2600);

    $("#alias").keyup(function() {
        checkalias();
    });

    function selectOther(type, obj) {
        switch (type) {
            case 'sort':
                var other = "&sid=" + obj.value;
                break;
            case 'user':
                var other = "&uid=" + obj.value;
                break;
            case 'link':
                var other = obj;
                break;
            default:
                break;
        }
        $('#articleModal').find('.modal-body .row').load("plugin.php?plugin=topics&action=article_lib&token=<?= LoginAuth::genToken() ?>" + other);
    }

    function logact(act) {
        if (getChecked('ids') == false) {
            alert('请选择要操作的文章');
            return;
        }
        if (act == 'del' && !confirm('确定要从该专题删除所选文章吗？')) {
            return;
        }
        $("#form_log").submit();
    }

    function del_confirm(id, token) {
        if (confirm("你确定要删除该专题吗？")) {
            window.location = "plugin.php?plugin=topics&action=del&id=" + id + "&token=" + token;
        } else {
            return;
        }
    }

    $("#save_topics").click(function() {
        var form_data = $("#form_topics").serializeArray()
        $.ajax({
            type: "POST",
            url: "plugin.php?plugin=topics&action=setting",
            data: form_data,
            dataType: "json",
            success: function(response) { //请求成功回调
                // console.log(response);
                $('#articleModal').modal('hide');
                if (response.code == 200) {
                    window.location.reload()
                }
            },
            error: function(e) { //请求超时回调
                if (e.statusText == "timeout") {
                    alert("请求超时")
                }
            },
        })
    })

    $('#articleModal').on('show.bs.modal', function(e) {
        var button = $(e.relatedTarget);
        var modal = $(this);
        modal.find('.modal-body .row').load(button.data("remote"));
    });

    $(document).ready(function() {
        var $modal = $('#modal');
        var image = document.getElementById('sample_image');
        var cropper;
        $('#upload_img').change(function(event) {
            var files = event.target.files;
            var done = function(url) {
                image.src = url;
                $modal.modal('show');
            };
            if (files && files.length > 0) {
                reader = new FileReader();
                reader.onload = function(event) {
                    done(reader.result);
                };
                reader.readAsDataURL(files[0]);
            }
        });
        $modal.on('shown.bs.modal', function() {
            cropper = new Cropper(image, {
                aspectRatio: 16 / 9,
                viewMode: 1
            });
        }).on('hidden.bs.modal', function() {
            cropper.destroy();
            cropper = null;
        });
        $('#crop').click(function() {
            canvas = cropper.getCroppedCanvas({
                width: 900,
                height: 500
            });
            canvas.toBlob(function(blob) {
                var formData = new FormData();
                formData.append('image', blob, 'cover.jpg');
                $.ajax('./article.php?action=upload_cover', {
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $modal.modal('hide');
                        if (data != "error") {
                            $('#cover_image').attr('src', data);
                            $('#cover').val(data);
                            $('#cover_rm').show();
                        }
                    }
                });
            });
        });

        $('#cover_rm').click(function() {
            $('#cover_image').attr('src', "./views/images/cover.svg");
            $('#cover').val("");
            $('#cover_rm').hide();
        });
    });
</script>
<link rel="stylesheet" type="text/css" href="./views/highslide/highslide.css?t=<?= Option::EMLOG_VERSION_TIMESTAMP ?>" />
<script src="./views/highslide/highslide.min.js?t=<?= Option::EMLOG_VERSION_TIMESTAMP ?>"></script>
<script>
    if (window.outerWidth > 767) {
        hs.graphicsDir = './views/highslide/graphics/';
        hs.wrapperClassName = 'rounded-white';
    } else {
        $('.highslide').removeAttr('onclick') // 如果是移动端，则不使用 highslide 功能
    }
</script>