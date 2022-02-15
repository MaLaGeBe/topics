<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">专题</h1>
    <a href="#articleModal" class="btn btn-sm btn-success shadow-sm mt-4" data-remote="plugin.php?plugin=topics&action=article_lib&token=<?= LoginAuth::genToken() ?>" data-toggle="modal" data-target="#articleModal"><i class="icofont-page"></i> 添加文章</a>
</div>
<div class="row">
    <div class="col-lg-3 mb-3">
        <div class="card card-scrollable">
            <form method="post" action="plugin.php?plugin=topics&action=setting">
                <div class="card-header">编辑</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">名称</label>
                        <input class="form-control" name="name" id="name" type="text" value="<?= $topics['name'] ?>" size="40" required>
                        <p class="small text-muted">这将是它在站点上显示的名字。</p>
                    </div>
                    <div class="form-group">
                        <label for="alias">别名</label>
                        <input class="form-control" name="alias" id="alias" type="text" value="<?= $topics['alias'] ?>" size="40">
                        <p class="small text-muted">“别名”是在URL中使用的别称，它可以令URL更美观。通常使用小写，只能包含字母，数字和连字符（-）。</p>
                    </div>
                    <div class="form-group">
                        <label for="description">描述</label>
                        <textarea class="form-control" name="description" id="description" rows="2" cols="40"><?= $topics['description'] ?></textarea>
                        <p class="small text-muted">描述只会在一部分主题中显示。</p>
                    </div>
                    <div class="form-group">
                        <label for="term_meta[image]">展示图片地址</label>
                        <label for="upload_img">
                            <img src="<?= $topics['term_meta']['image'] ?: './views/images/cover.svg' ?>" id="cover_image" class="rounded" />
                            <input type="file" class="image" id="upload_img" style="display:none" />
                            <input type="hidden" name="term_meta[image]" id="cover" value="<?= $topics['term_meta']['image'] ?>" />
                            <button type="button" id="cover_rm" class="btn-sm btn btn-link" <?= $topics['term_meta']['image'] ?: 'style="display: none;"' ?>>x</button>
                        </label>
                        <p class="small text-muted">封面尺寸：900*500px</p>
                    </div>

                    <div class="form-group">
                        <label for="term_meta[title]">SEO 标题</label>
                        <input class="form-control" type="text" name="term_meta[title]" id="term_meta[title]" value="<?= $topics['term_meta']['title'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="term_meta[keywords]">SEO 关键字（keywords）</label>
                        <input class="form-control" type="text" name="term_meta[keywords]" id="term_meta[keywords]" value="<?= $topics['term_meta']['keywords'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="term_meta[keywords]">SEO 描述（description）</label>
                        <textarea class="form-control" name="term_meta[description]" id="term_meta[description]" rows="4" cols="40"><?= $topics['term_meta']['description'] ?></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <input type="hidden" name="edit" value="<?= $_GET['edit'] ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="submit" name="submit" id="submit" class="btn btn-success" value="修改专题"> <span class="spinner"></span>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-9 mb-3">
        <div class="card">
            <div class="card-header">专题文章管理</div>

            <div class="card-body">
                <table class="table table-bordered table-striped table-hover dataTable no-footer">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll" /></th>
                            <th>标题</th>
                            <th>评论</th>
                            <th>浏览</th>
                            <th>作者</th>
                            <th>分类</th>
                            <th>时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($logs)) : ?>
                            <form action="plugin.php?plugin=topics&action=setting" method="post" id="form_log">
                                <?php foreach ($logs as $key => $value) :
                                    $sortName = $value['sortid'] == -1 && !array_key_exists($value['sortid'], $sorts) ? '未分类' : $sorts[$value['sortid']]['sortname'];
                                    $author = $user_cache[$value['author']]['name'];
                                    $author_role = $user_cache[$value['author']]['role'];
                                ?>
                                    <tr>
                                        <td style="width: 20px;"><input type="checkbox" name="blog[]" value="<?= $value['gid'] ?>" class="ids" /></td>
                                        <td><a href="article.php?action=edit&gid=<?= $value['gid'] ?>"><?= $value['title'] ?></a>
                                            <?php if ($value['top'] == 'y') : ?><span class="badge small badge-warning">首页置顶</span><?php endif ?>
                                            <?php if ($value['sortop'] == 'y') : ?><span class="badge small badge-secondary">分类置顶</span><?php endif ?>
                                        </td>
                                        <td><a href="comment.php?gid=<?= $value['gid'] ?>" class="badge badge-info"><?= $value['comnum'] ?></a></td>
                                        <td><a href="<?= Url::log($value['gid']) ?>" class="badge badge-secondary" target="_blank"><?= $value['views'] ?></a></td>
                                        <td><a href="article.php?uid=<?= $value['author'] ?>"><?= $author ?></a></td>
                                        <td><a href="article.php?sid=<?= $value['sortid'] ?>"><?= $sortName ?></a></td>
                                        <td class="small"><?= $value['date'] ?></td>
                                    </tr>
                                <?php endforeach ?>
                                <input type="hidden" name="edit" value="<?= $_GET['edit'] ?>">
                                <input type="hidden" name="action" value="del">
                            </form>
                        <?php else : ?>
                            <tr>
                                <td colspan="7">当前未添加文章</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if (isset($pageurl)) : ?>
                <div class="page"><?= $pageurl ?> (有 <?= $logNum ?> 篇文章)</div>
                <?php endif; ?>
            </div>
            <?php if (isset($logs)) : ?>
                <div class="card-footer">
                    <a href="javascript:logact('del');" class="btn btn-sm btn-danger">删除</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal" id="articleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">图文资源</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="plugin.php?plugin=topics&action=setting" method="post" id="form_topics">
                    <div class="row">
                    </div>
                    <input type="hidden" name="edit" value="<?= $_GET['edit'] ?>">
                    <input type="hidden" name="action" value="updateLogs">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-sm btn-success" id="save_topics">保存</button>
            </div>
        </div>
    </div>
</div>