<?php
require_once('sections.php');

<!DOCTYPE html>
<html>
<head>
    <title>Dashboards</title>
    <link rel="stylesheet" type="text/css" href="assets/css/screen.css">
</head>
<body class="index">

<? foreach ($sections as $section_title => $dashboard_groups) : ?>
<div class='section'>
    <h2><?= $section_title ?></h2>
    <table width='100%'>
    <tr valign='top'>
        <? $i = 0; ?>
        <? foreach ($dashboard_groups as $dashboard_group_title => $dashboards) : ?>
        <td width='20%'>
            <div class='index-group-title'><?= $dashboard_group_title ?></div>
            <ul style='margin: 0; padding-left: 20px;'>
                <? foreach ($dashboards as $name => $url) : ?>
                <?
                $link_title = Tabs::getLinkTitle($name, $url);
                $link_target = Tabs::getLinkTarget($url);
                $link_image = Tabs::getLinkIcon($url);
                ?>
                <li><a href='<?= $url ?>' <?= $link_target ?> title='<?= $link_title ?>'><span><?= $link_title ?><?= $link_image ?></span></a></li>
                <? endforeach; ?>
            </ul>
        </td>
        <? if ($i % 5 == 4 && $i < count($dashboard_groups) - 1) : ?>
        </tr><tr valign='top'>
            <? endif; ?>
        <? $i++; ?>
        <? endforeach; ?>
        <? if ($i % 5 != 0) : for ($j = 0; $j < (5 - ($i % 5)); $j++) : ?>
        <td width='20%'></td>
        <? endfor; endif; ?>
    </tr>
    </table>
</div>
    <? endforeach; ?>

</body>
</html>
