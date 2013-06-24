<?php

require_once __DIR__.'/../tools.php';

if (! $data) {
    $host = get_origin().'api/v1';
    return array(
        "block_url" => "$host/block/{block}",
        "block" => array_map(function ($str) {
            return str_replace(' ', '_', strtolower($str));
        }, UnicodeBlock::getAllNames($api->_db)),
    );
}


try {
    $block = new UnicodeBlock($data, $api->_db);
} catch (Exception $e) {
    $api->throwError(API_NOT_FOUND, _('Not a block'));
}
$block_limits = $block->getBlockLimits();
$prev = $block->getPrev();
$next = $block->getNext();
$plane = $block->getPlane();

$return = array(
    "name" => $block->getName(),
    "first" => sprintf("U+%04X", $block_limits[0]),
    "last" => sprintf("U+%04X", $block_limits[1]),
);

header('Link: <http://codepoints.net'.Router::getRouter()->getUrl($block).'>; rel=alternate', false);
header('Link: <http://codepoints.net/api/v1/plane'.Router::getRouter()->getUrl($plane).'>; rel=up', false);
if ($next) {
    header('Link: <http://codepoints.net/api/v1/block'.Router::getRouter()->getUrl($next).'>; rel=next', false);
    $return["next_block"] = $next->getName();
}
if ($prev) {
    header('Link: <http://codepoints.net/api/v1/block'.Router::getRouter()->getUrl($prev).'>; rel=prev', false);
    $return["prev_block"] = $prev->getName();
}

return $return;


#EOF
