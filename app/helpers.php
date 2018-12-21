<?php

use voku\helper\StopWords;

function remove_stop_words($text) {
    $stopWords = [];

    try {
        $stopWords = (new StopWords)->getStopWordsFromLanguage('id');
    } catch (Exception $e) {}

    $result = array_diff(explode(' ', strtolower($text)), $stopWords);

    return implode(' ', $result);
}
