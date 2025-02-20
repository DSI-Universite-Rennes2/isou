<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use League\CommonMark\CommonMarkConverter;

$TITLE .= ' - Changelog';

$changelogs = array();
$changelog_path = PRIVATE_PATH.'/markdown/changelogs';

$handle = opendir($changelog_path);
if ($handle !== false) {
    $converter = new CommonMarkConverter();

    while (($file = readdir($handle)) !== false) {
        if (in_array($file, array('.', '..', 'index.php', 'index.html', 'template.md'), $strict = true) === true) {
            continue;
        }

        $filepath = $changelog_path.'/'.$file;

        if (is_dir($filepath) === true) {
            continue;
        }

        if (is_readable($filepath) === false) {
            continue;
        }

        if (substr($file, -3) !== '.md') {
            continue;
        }

        $build = basename($file, '.md');

        $content = file_get_contents($filepath);
        $changelogs[$build] = $converter->convertToHtml($content);
    }

    closedir($handle);
}

krsort($changelogs, SORT_NATURAL);

$smarty->assign('changelogs', $changelogs);

$SUBTEMPLATE = 'settings/changelog.tpl';
