<?php
$result = isset($_GET['result']) ? $_GET['result'] : '';

switch ($result) {
    case 'del_success':
        $class = 'success';
        $msg = '专题删除成功';
        break;
    case 'add_success':
        $class = 'success';
        $msg = '专题添加成功';
        break;
    case 'edit_success':
        $class = 'success';
        $msg = '专题修改成功';
        break;

    default:
        # code...
        break;
}

if (isset($msg) && isset($class)) {
    echo "<div class=\"alert alert-{$class}\">{$msg}</div>";
}
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">专题</h1>
</div>

<div class="row">
    <div class="col-lg-3 mb-3">
        <div class="card card-scrollable">
            <form method="post" action="plugin.php?plugin=topics&action=setting">
                <div class="card-header">添加</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">名称</label>
                        <input class="form-control" name="name" id="name" type="text" value="" size="40" required>
                        <p class="small text-muted">这将是它在站点上显示的名字。</p>
                    </div>
                    <div class="form-group">
                        <label for="alias">别名</label>
                        <input class="form-control" name="alias" id="alias" type="text" value="" size="40">
                        <p class="small text-muted">“别名”是在URL中使用的别称，它可以令URL更美观。通常使用小写，只能包含字母，数字和连字符（-）。</p>
                    </div>
                    <div class="form-group">
                        <label for="description">描述</label>
                        <textarea class="form-control" name="description" id="description" rows="2" cols="40"></textarea>
                        <p class="small text-muted">描述只会在一部分主题中显示。</p>
                    </div>
                    <div class="form-group">
                        <label for="term_meta[image]">展示图片地址</label>
                        <label for="upload_img">
                            <img src="<?= './views/images/cover.svg' ?>" id="cover_image" class="rounded" />
                            <input type="file" class="image" id="upload_img" style="display:none" />
                            <input type="hidden" name="term_meta[image]" id="cover" value="" />
                            <button type="button" id="cover_rm" class="btn-sm btn btn-link" style="display: none;">x</button>
                        </label>
                        <p class="small text-muted">封面尺寸：900*500px</p>
                    </div>

                    <div class="form-group">
                        <label for="term_meta[title]">SEO 标题</label>
                        <input class="form-control" type="text" name="term_meta[title]" id="term_meta[title]">
                    </div>
                    <div class="form-group">
                        <label for="term_meta[keywords]">SEO 关键字（keywords）</label>
                        <input class="form-control" type="text" name="term_meta[keywords]" id="term_meta[keywords]">
                    </div>
                    <div class="form-group">
                        <label for="term_meta[keywords]">SEO 描述（description）</label>
                        <textarea class="form-control" name="term_meta[description]" id="term_meta[description]" rows="4" cols="40"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <input type="hidden" name="action" value="add">
                    <input type="submit" name="submit" id="submit" class="btn btn-success" value="添加专题"> <span class="spinner"></span>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-9 mb-3">
        <div class="card">
            <div class="card-header">管理</div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>名称</th>
                            <th>别名</th>
                            <th>描述</th>
                            <th>图片</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($topicsSort) >= 1) : ?>
                            <?php foreach ($topicsSort as $key => $value) : ?>
                                <tr>
                                    <td><?= $value['name'] ?></td>
                                    <td><?= $value['alias'] ?></td>
                                    <td><?= $value['description'] ?></td>
                                    <td>
                                        <?php if (empty($value['term_meta']['image'])) : ?>
                                            <span>未上传</span>
                                        <?php else : ?>
                                            <a href="<?= $value['term_meta']['image'] ?>" class="highslide" onclick="return hs.expand(this)" target="_blank">点击查看</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="plugin.php?plugin=topics&edit=<?= $value['id'] ?>" class="btn btn-primary btn-sm">编辑</a>
                                        <a href="javascript:del_confirm('<?= $value['id'] ?>','<?= LoginAuth::genToken() ?>');" class="btn btn-danger btn-sm">删除</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5">当前还没有创建专题，请在左侧创建一个吧</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>