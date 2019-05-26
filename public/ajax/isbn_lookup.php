<?php

declare(strict_types = 1);

use Pu239\Torrent;

require_once __DIR__ . '/../../include/bittorrent.php';
require_once INCL_DIR . 'function_books.php';
extract($_POST);

header('content-type: application/json');
global $container;

$isbn = str_replace([
    ' ',
    '_',
    '-',
], '', $isbn);
$torrent_stuffs = $container->get(Torrent::class);
$torrent = $torrent_stuffs->get($tid);
$poster = !empty($torrent['poster']) ? $torrent['poster'] : '';
$book_info = get_book_info((!empty($isbn) ? $isbn : '000000'), $name, $tid, $poster);
if (!empty($book_info)) {
    echo json_encode(['content' => $book_info[0]]);
    die();
}

echo json_encode(['content' => 'Lookup Failed']);
die();
