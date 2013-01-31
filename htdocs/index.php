<?php
//lets add CWD to path for everything future
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__))

require_once('conf/sections.php');

?><!DOCTYPE html>
<html>
<head>
    <title>Dashboards</title>
    <link rel="stylesheet" type="text/css" href="assets/css/screen.css">
</head>
<body class="index">

<?php foreach ($sections as $section_title => $dashboard_groups) : ?>
<div class='section'>
    <h2><?= $section_title ?></h2>
    <table width='100%'>
    <tr valign='top'>
        <?php $i = 0; ?>
        <?php foreach ($dashboard_groups as $dashboard_group_title => $dashboards) : ?>
        <td width='20%'>
            <div class='index-group-title'><?= $dashboard_group_title ?></div>
            <ul style='margin: 0; padding-left: 20px;'>
                <?php foreach ($dashboards as $name => $url) : ?>
                <?php 
                $link_title = Tabs::getLinkTitle($name, $url);
                $link_target = Tabs::getLinkTarget($url);
                $link_image = Tabs::getLinkIcon($url);
                ?>
                <li><a href='<?= $url ?>' <?= $link_target ?> title='<?= $link_title ?>'><span><?= $link_title ?><?= $link_image ?></span></a></li>
                <?php endforeach; ?>
            </ul>
        </td>
        <?php if ($i % 5 == 4 && $i < count($dashboard_groups) - 1) : ?>
        </tr><tr valign='top'>
            <?php endif; ?>
        <?php $i++; ?>
        <?php endforeach; ?>
        <?php if ($i % 5 != 0) : for ($j = 0; $j < (5 - ($i % 5)); $j++) : ?>
        <td width='20%'></td>
        <?php endfor; endif; ?>
    </tr>
    </table>
</div>
    <?php endforeach; ?>

</body>
</html>
