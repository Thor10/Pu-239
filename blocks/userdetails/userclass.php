<?php

declare(strict_types = 1);
global $user, $lang;

$HTMLOUT .= "
        <tr>
            <td class='rowhead'>{$lang['userdetails_class']}</td>
            <td>" . get_user_class_name($user['class']) . "&#160;&#160;<img src='" . get_user_class_image($user['class']) . "' alt='" . get_user_class_name($user['class']) . "' title='" . get_user_class_name($user['class']) . "'></td>
        </tr>";
