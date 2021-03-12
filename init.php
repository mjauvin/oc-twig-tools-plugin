<?php


if (!function_exists('lipsum')) {

    function lipsum($n = 1, $type = "s", $seperator = null)
    {
        $result = '';
        $gen = new \Badcow\LoremIpsum\Generator();
        if ($type === 's') {
            $result = ($seperator ?: '<p>') . implode($seperator ?: '<p>', $gen->getSentences($n));
        } else if ($type === 'w') {
            $result = implode($seperator ?: ' ', $gen->getRandomWords($n));
        } else if ($type === 'p') {
            $result = ($seperator ?: '<p>') . implode($seperator ?: '<p>', $gen->getParagraphs($n));
        }

        return trim($result);
    }
}
