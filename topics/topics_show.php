<?php
$CACHE = TopicsCache::getInstance();
$Log_Model = new Log_Model();
$options_cache = Option::getAll();
extract($options_cache);

$query = explode('/', $_SERVER['QUERY_STRING']);
$topic = isset($query[1]) ? $query[1] : 0;
$page = isset($query[2]) ? abs((int)$query[2]) : 1;

$id = '';
if (!empty($topic)) {
    if (is_numeric($topic)) {
        $id = (int)$topic;
    } else {
        $topics_cache = $CACHE->readCache('topics');
        foreach ($topics_cache as $key => $value) {
            $alias = addslashes(urldecode(trim($topic)));
            if ($alias === $value['alias']) {
                $id = $key;
                break;
            }
        }
    }
} else {
    $lists = $CACHE->readCache('topics');

    include View::getView('header');
    include View::getView('topics');
    view::output();
}

$pageurl = '';

$topics_cache = $CACHE->readCache('topics');
if (!isset($topics_cache[$id])) {
    show_404_page();
}

extract($topics_cache[$id]);

$site_title = !empty($term_meta['title']) ?  $term_meta['title'] . ' - ' . $site_title : $name . ' - ' . $site_title;
$site_description = !empty($term_meta['description']) ? $term_meta['description'] : '';
$site_key = !empty($term_meta['keywords']) ? $term_meta['keywords'] : '';

if (empty($logs)) {
    $logs = array(0);
}

$blogIdStr = implode(',', $logs);
$sqlSegment = "and gid IN ($blogIdStr) ORDER BY date DESC";

$lognum = $Log_Model->getLogNum('n', $sqlSegment);

$total_pages = ceil($lognum / $index_lognum);

if ($page > $total_pages) {
    $page = $total_pages;
}

$pageurl .= TopicsUrl::topics($id, 'page');

$logs = $Log_Model->getLogsForHome($sqlSegment, $page, $index_lognum);

$page_url = pagination($lognum, $index_lognum, $page, $pageurl);

include View::getView('header');
include View::getView('topic');
