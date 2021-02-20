<?php

function lipsum($n=1, $type="s")
{
    $gen = new \Badcow\LoremIpsum\Generator();
    if ($type === 's') {
        return implode('<p>', $gen->getSentences($n));
    } else if ($type === 'w') {
        return implode(' ', $gen->getRandomWords($n));
    } else if ($type === 'p') {
        return implode('<p>', $gen->getParagraphs($n));
    }   
}
