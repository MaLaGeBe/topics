<div class="col-12">
    <div class="card">
        <div class="card-header card-header-actions">
            <div id="f_title" class="row form-inline">
                <div id="f_t_sort" class="mx-1">
                    <select id="bysort" onChange="selectOther('sort',this);" class="form-control">
                        <option value="" selected="selected">按分类查看</option>
                        <?php
                        foreach ($sorts as $key => $value) :
                            if ($value['pid'] != 0) {
                                continue;
                            }
                            $flg = $value['sid'] == $sid ? 'selected' : '';
                        ?>
                            <option value="<?= $value['sid'] ?>" <?= $flg ?>><?= $value['sortname'] ?></option>
                            <?php
                            $children = $value['children'];
                            foreach ($children as $key) :
                                $value = $sorts[$key];
                                $flg = $value['sid'] == $sid ? 'selected' : '';
                            ?>
                                <option value="<?= $value['sid'] ?>" <?= $flg ?>>&nbsp; &nbsp; &nbsp; <?= $value['sortname'] ?></option>
                        <?php
                            endforeach;
                        endforeach;
                        ?>
                        <option value="-1" <?php if ($sid == -1) echo 'selected' ?>>未分类</option>
                    </select>
                </div>
                <?php if (User::isAdmin() && count($user_cache) > 1) : ?>
                    <div id="f_t_user" class="mx-1">
                        <select id="byuser" onChange="selectOther('user',this);" class="form-control">
                            <option value="" selected="selected">按作者查看</option>
                            <?php
                            foreach ($user_cache as $key => $value) :
                                $flg = $key == $uid ? 'selected' : '';
                            ?>
                                <option value="<?= $key ?>" <?= $flg ?>><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                <?php endif ?>
                <div class="mx-1">
                    <input class="form-control" placeholder="Search">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="filters">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAll" onclick="checkALL()"/></th>
                                <th>标题</th>
                                <th><a href="javascript:selectOther('link','&sortComm=<?= $sortComm ?>')">评论</a></th>
                                <th><a href="javascript:selectOther('link','&sortView=<?= $sortView ?>')">浏览</a></th>
                                <th>作者</th>
                                <th>分类</th>
                                <th><a href="javascript:selectOther('link','&sortDate=<?= $sortDate ?>')">时间</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $key => $value) :
                                $sortName = $value['sortid'] == -1 && !array_key_exists($value['sortid'], $sorts) ? '未分类' : $sorts[$value['sortid']]['sortname'];
                                $author = $user_cache[$value['author']]['name'];
                                $author_role = $user_cache[$value['author']]['role'];
                            ?>
                                <tr>
                                    <td style="width: 20px;"><input type="checkbox" name="blog[]" value="<?= $value['gid'] ?>" class="ids" /></td>
                                    <td><a href="<?= Url::log($value['gid']) ?>" target="_blank"><?= $value['title'] ?></a>
                                        <?php if ($value['top'] == 'y') : ?><span class="badge small badge-warning">首页置顶</span><?php endif ?>
                                        <?php if ($value['sortop'] == 'y') : ?><span class="badge small badge-secondary">分类置顶</span><?php endif ?>
                                    </td>
                                    <td><span class="badge badge-info"><?= $value['comnum'] ?></span></td>
                                    <td><span class="badge badge-secondary" target="_blank"><?= $value['views'] ?></span></td>
                                    <td><a href="javascript:selectOther('link','&uid=<?= $value['author'] ?>')"><?= $author ?></a></td>
                                    <td><a href="javascript:selectOther('link','&sid=<?= $value['sortid'] ?>')"><?= $sortName ?></a></td>
                                    <td class="small"><?= $value['date'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <input name="token" id="token" value="<?= LoginAuth::genToken() ?>" type="hidden" />
                <div class="form-inline">
                </div>
                <div class="page"><?= $pageurl ?> (有 <?= $logNum ?> 篇文章)</div>
            </div>
        </div>
    </div>
</div>