<?php

function callback_init()
{
    $topics = Storage::getInstance('topics');
    $topics->setValue('topics', array(), 'array');
    $topics->setValue('topics_data', array(), 'array');
}

function callback_rm()
{
    $topics = Storage::getInstance('topics');
    $topics->deleteAllName('NO'); //如果要删除数据，请将参数改为`YES`
}