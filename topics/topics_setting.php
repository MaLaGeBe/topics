<?php
!defined('EMLOG_ROOT') && exit('access deined!');

function plugin_setting_view()
{
    $topics = Storage::getInstance('topics');
    $topicsSort = $topics->getValue('topics');
    $topicsData = $topics->getValue('topics_data');

    if (isset($_GET['edit']) && !empty($_GET['edit'])) {
        if (isset($topicsSort[$_GET['edit']])) {
            $topics = $topicsSort[$_GET['edit']];
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

            global $CACHE;

            $Log_Model = new Log_Model();

            if (isset($topicsData[$_GET['edit']]) && !empty($topicsData[$_GET['edit']])) {
                $blogIdStr = implode(',', $topicsData[$_GET['edit']]);

                $sqlSegment = "and gid IN ($blogIdStr) ORDER BY date DESC";
                $logNum = $Log_Model->getLogNum('n', $sqlSegment, 'blog', 1);
                $logs = $Log_Model->getLogsForAdmin($sqlSegment, 'n', $page);
                $sorts = $CACHE->readCache('sort');
                $log_cache_tags = $CACHE->readCache('logtags');
                $user_cache = $CACHE->readCache('user');

                $subPage = '';
                foreach ($_GET as $key => $val) {
                    $subPage .= $key != 'page' ? "&$key=$val" : '';
                }
                $pageurl = pagination($logNum, Option::get('admin_perpage_num'), $page, "plugin.php?{$subPage}&page=");
            }

            include 'views/edit.php';
        } else {
            emDirect('plugin.php?plugin=topics');
        }
    } else {
        include 'views/topics.php';
    }
    include 'views/assets.php';
}


function plugin_setting()
{
    $topics = Storage::getInstance('topics');
    $topicsSort = $topics->getValue('topics');
    $topicsData = $topics->getValue('topics_data');

    $edit_id = isset($_POST['edit']) ? (int)trim($_POST['edit']) : '';

    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $last_key = array_key_last((array)$topicsSort);
        if (empty($last_key)) {
            $topicsSort[1]['id'] = 1;
            $topicsSort[1]['name'] = addslashes(trim($_POST['name']));
            $topicsSort[1]['alias'] = addslashes(trim($_POST['alias']));
            $topicsSort[1]['description'] = addslashes(trim($_POST['description']));
            $topicsSort[1]['term_meta'] = $_POST['term_meta'];
        } else {
            $last_key = $last_key + 1;
            $topicsSort[$last_key]['id'] = $last_key;
            $topicsSort[$last_key]['name'] = addslashes(trim($_POST['name']));
            $topicsSort[$last_key]['alias'] = addslashes(trim($_POST['alias']));
            $topicsSort[$last_key]['description'] = addslashes(trim($_POST['description']));
            $topicsSort[$last_key]['term_meta'] = $_POST['term_meta'];
        }
        $topics->updateValue('topics', $topicsSort);
        TopicsCache::getInstance()->updateCache('topics');
        emDirect('plugin.php?plugin=topics&result=add_success');
    } elseif (isset($_POST['action']) && $_POST['action'] == 'update' && !empty($edit_id)) {
        $last_key = $edit_id;
        $topicsSort[$last_key]['id'] = $last_key;
        $topicsSort[$last_key]['name'] = addslashes(trim($_POST['name']));
        $topicsSort[$last_key]['alias'] = addslashes(trim($_POST['alias']));
        $topicsSort[$last_key]['description'] = addslashes(trim($_POST['description']));
        $topicsSort[$last_key]['term_meta'] = $_POST['term_meta'];

        $topics->updateValue('topics', $topicsSort);
        TopicsCache::getInstance()->updateCache('topics');
        emDirect('plugin.php?plugin=topics&result=edit_success');
    } elseif (isset($_POST['action']) && $_POST['action'] == 'updateLogs' && !empty($edit_id)) {
        $topicsData[$edit_id] = isset($topicsData[$edit_id]) ? array_merge($topicsData[$edit_id], $_POST['blog']) : $_POST['blog'];

        $topics->updateValue('topics_data', $topicsData);
        TopicsCache::getInstance()->updateCache('topics');
        print_r(json_encode(array(
            "code" => 200,
            "msg" => "更新成功",
            "data" => $topicsData
        )));
        exit;
    } elseif (isset($_POST['action']) && $_POST['action'] == 'del' && !empty($edit_id)) {
        foreach ($topicsData[$edit_id] as $key => $value) {
            if (in_array($value, $_POST['blog'])) {
                unset($topicsData[$edit_id][$key]);
            }
        }
        $topics->updateValue('topics_data', $topicsData);
        TopicsCache::getInstance()->updateCache('topics');
        emDirect('plugin.php?plugin=topics&edit=' . $_POST['edit']);
    }
}
