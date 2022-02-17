<?php
/*
Plugin Name: 专题
Version: v1.0.1
Plugin URL: 
Description: 为你的Emlog添加一个专题功能
Author: MaLaGeBe
Author URL: https://github.com/malagebe/topics
ForEmlog: pro v1.2.0
*/
!defined('EMLOG_ROOT') && exit('access deined!');

$action = isset($_GET['action']) ? addslashes($_GET['action']) : '';

if ($action === 'article_lib') {
    LoginAuth::checkToken();

    $topics = Storage::getInstance('topics');
    $topicsSort = $topics->getValue('topics');

    global $CACHE;
    $Log_Model = new Log_Model();
    $Tag_Model = new Tag_Model();

    $tagId = isset($_GET['tagid']) ? (int)$_GET['tagid'] : '';
    $sid = isset($_GET['sid']) ? (int)$_GET['sid'] : '';
    $uid = isset($_GET['uid']) ? (int)$_GET['uid'] : '';
    $keyword = isset($_GET['keyword']) ? addslashes($_GET['keyword']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $checked = isset($_GET['checked']) ? addslashes($_GET['checked']) : '';

    $sortView = (isset($_GET['sortView']) && $_GET['sortView'] == 'ASC') ? 'DESC' : 'ASC';
    $sortComm = (isset($_GET['sortComm']) && $_GET['sortComm'] == 'ASC') ? 'DESC' : 'ASC';
    $sortDate = (isset($_GET['sortDate']) && $_GET['sortDate'] == 'DESC') ? 'ASC' : 'DESC';

    $sqlSegment = '';
    if ($tagId) {
        $blogIdStr = $Tag_Model->getTagById($tagId);
        $sqlSegment = "and gid IN ($blogIdStr)";
    } elseif ($sid) {
        $sqlSegment = "and sortid=$sid";
    } elseif ($uid) {
        $sqlSegment = "and author=$uid";
    } elseif ($checked) {
        $sqlSegment = "and checked='$checked'";
    } elseif ($keyword) {
        $sqlSegment = "and title like '%$keyword%'";
    }
    $sqlSegment .= ' ORDER BY ';
    if (isset($_GET['sortView'])) {
        $sqlSegment .= "views $sortView";
    } elseif (isset($_GET['sortComm'])) {
        $sqlSegment .= "comnum $sortComm";
    } elseif (isset($_GET['sortDate'])) {
        $sqlSegment .= "date $sortDate";
    } else {
        $sqlSegment .= 'top DESC, sortop DESC, date DESC';
    }

    $logNum = $Log_Model->getLogNum('n', $sqlSegment, 'blog', 1);
    $logs = $Log_Model->getLogsForAdmin($sqlSegment, 'n', $page);
    $sorts = $CACHE->readCache('sort');
    $log_cache_tags = $CACHE->readCache('logtags');
    $user_cache = $CACHE->readCache('user');

    $subPage = '';
    foreach ($_GET as $key => $val) {
        $subPage .= $key != 'page' ? "&$key=$val" : '';
    }

    $pageurl = pagination_topics($logNum, Option::get('admin_perpage_num'), $page, "plugin.php?{$subPage}&page=");

    include 'views/article_lib.php';
    View::output();
}

if ($action === 'del') {
    LoginAuth::checkToken();

    $topics = Storage::getInstance('topics');
    $topicsSort = $topics->getValue('topics');
    $topicsDate = $topics->getValue('topics_date');

    $id = isset($_GET['id']) ? $_GET['id'] : '';

    if (isset($topicsSort[$id])) {
        unset($topicsSort[$id]);
    }

    if (isset($topicsDate[$id])) {
        unset($topicsDate[$id]);
    }

    $topics->updateValue('topics', $topicsSort);
    $topics->updateValue('topics_date', $topicsDate);

    TopicsCache::getInstance()->updateCache('topics');

    emDirect('plugin.php?plugin=topics&result=del_success');
}

addAction('adm_menu_ext', 'admin_menu_topics');

function admin_menu_topics()
{
    echo '<a class="collapse-item" id="menu_topics" href="plugin.php?plugin=topics">专题管理</a>';
}

function pagination_topics($count, $perlogs, $page, $url, $anchor = '')
{
    $pnums = @ceil($count / $perlogs);
    $re = '';
    $urlHome = preg_replace("|[\?&/][^\./\?&=]*page[=/\-]|", "", $url);
    for ($i = $page - 5; $i <= $page + 5 && $i <= $pnums; $i++) {
        if ($i <= 0) {
            continue;
        }
        if ($i == $page) {
            $re .= " <span>$i</span> ";
        } elseif ($i == 1) {
            $re .= " <a href=\"javascript:selectOther('link','$urlHome$anchor');\">$i</a> ";
        } else {
            $re .= " <a href=\"javascript:selectOther('link','$url$i$anchor');\">$i</a> ";
        }
    }
    if ($page > 6)
        $re = "<a href=\"javascript:selectOther('link','{$urlHome}$anchor');\" title=\"首页\">&laquo;</a><em> ... </em>$re";
    if ($page + 5 < $pnums)
        $re .= "<em> ... </em> <a href=\"javascript:selectOther('link','$url$pnums$anchor');\" title=\"尾页\">&raquo;</a>";
    if ($pnums <= 1)
        $re = '';
    return $re;
}

function get_topics($id = '')
{
    $CACHE = TopicsCache::getInstance();
    $topics_cache = $CACHE->readCache('topics');
    if (empty($id)) {
        return $topics_cache;
    } elseif (isset($topics_cache[$id])) {
        return $topics_cache[$id];
    } else {
        return false;
    }
}

class TopicsCache extends Cache
{
    protected $topics_cache;

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new TopicsCache();
        }
        return self::$instance;
    }

    function mc_topics()
    {
        $topics = Storage::getInstance('topics');
        $topicsSort = $topics->getValue('topics');
        $topicsData = $topics->getValue('topics_data');

        $topics_cache = [];

        foreach ($topicsSort as $key => $value) {
            $topicsSort[$key]['logs'] = isset($topicsData[$key]) ? $topicsData[$key] : array();
        }

        $topics_cache = $topicsSort;

        $cacheData = serialize($topics_cache);
        $this->cacheWrite($cacheData, 'topics');
    }
}

class TopicsUrl extends Url
{
    static function topics($topic, $page = null)
    {
        $topicUrl = '';

        $topicUrl = BLOG_URL . '?plugin=topics/' . $topic;
        if ($page)
            $topicUrl .= '/';

        return $topicUrl;
    }
}
