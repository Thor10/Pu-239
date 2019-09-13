<?php

declare(strict_types = 1);

use Pu239\Cache;
use Pu239\Database;
use Pu239\Session;

require_once __DIR__ . '/../include/bittorrent.php';
require_once INCL_DIR . 'function_users.php';
require_once INCL_DIR . 'function_html.php';
require_once INCL_DIR . 'function_staff.php';
require_once BIN_DIR . 'uglify.php';
require_once BIN_DIR . 'functions.php';
require_once CLASS_DIR . 'class_check.php';
$user = check_user_status();
global $container, $site_config;

$session = $container->get(Session::class);

class_check(UC_STAFF);
$lang = array_merge(load_language('global'), load_language('index'), load_language('staff_panel'));
if (!$site_config['site']['staffpanel_online']) {
    stderr($lang['spanel_information'], $lang['spanel_panel_cur_offline']);
}
$stdhead = [
    'css' => [
        get_file_name('sceditor_css'),
    ],
];
$stdfoot = [
    'js' => [
        get_file_name('sceditor_js'),
        get_file_name('navbar_show_js'),
    ],
];

$HTMLOUT = $page_name = $file_name = $navbar = '';
$fluent = $container->get(Database::class);
$cache = $container->get(Cache::class);
$cache->delete('staff_classes_');
$staff_classes = $cache->get('staff_classes_');
if ($staff_classes === false || is_null($staff_classes)) {
    $available_classes = $fluent->from('class_config')
                                ->select(null)
                                ->select('value')
                                ->where("name != 'UC_MIN'")
                                ->where("name != 'UC_MAX'")
                                ->where("name != 'UC_STAFF'")
                                ->where('value >= ?', UC_STAFF)
                                ->groupBy('value')
                                ->orderBy('value')
                                ->fetchAll();
    foreach ($available_classes as $class) {
        $staff_classes[] = $class['value'];
    }
    $cache->set('staff_classes_', $staff_classes, 0);
}
$data = array_merge($_POST, $_GET);
$action = isset($data['action']) ? htmlsafechars($data['action']) : null;
$id = isset($data['id']) ? (int) $data['id'] : 0;
$tool = !empty($data['tool']) ? $data['tool'] : null;
write_info("{$user['username']} has accessed the " . (empty($tool) ? 'staffpanel' : "$tool staff page"));
$staff_tools = [
    'modtask' => 'modtask',
    'iphistory' => 'iphistory',
    'ipsearch' => 'ipsearch',
    'shit_list' => 'shit_list',
    'invite_tree' => 'invite_tree',
    'user_hits' => 'user_hits',
];
$file_names = $fluent->from('staffpanel')
                     ->select(null)
                     ->select('file_name')
                     ->fetchPairs('id', 'file_name');
foreach ($file_names as $key => $file_name) {
    $item = str_replace([
        'staffpanel.php?tool=',
        '.php',
        '&mode=news',
        '&action=app',
    ], '', $file_name);
    $staff_tools[$item] = $item;
}
ksort($staff_tools);
if (in_array($tool, $staff_tools) && file_exists(ADMIN_DIR . $staff_tools[$tool] . '.php')) {
    require_once ADMIN_DIR . $staff_tools[$tool] . '.php';
} else {
    if ($action === 'delete' && is_valid_id($id) && has_access($user['class'], UC_MAX, 'coder')) {
        $sure = (isset($_GET['sure']) ? $_GET['sure'] : '') === 'yes';
        $arr = $fluent->from('staffpanel')
                      ->select(null)
                      ->select('navbar')
                      ->select('added_by')
                      ->select('av_class')
                      ->select('page_name')
                      ->where('id = ?', $id)
                      ->fetch();
        if ($user['class'] < $arr['av_class']) {
            stderr($lang['spanel_error'], $lang['spanel_you_not_allow_del_page']);
        }
        if (!$sure) {
            stderr($lang['spanel_sanity_check'], $lang['spanel_are_you_sure_del'] . ': "' . htmlsafechars($arr['page_name']) . '"? ' . $lang['spanel_click'] . ' <a href="' . $_SERVER['PHP_SELF'] . '?action=' . $action . '&amp;id=' . $id . '&amp;sure=yes">' . $lang['spanel_here'] . '</a> ' . $lang['spanel_to_del_it_or'] . ' <a href="' . $_SERVER['PHP_SELF'] . '">' . $lang['spanel_here'] . '</a> ' . $lang['spanel_to_go_back'] . '.');
        }
        $cache->delete('staff_classes_');
        $result = $fluent->deleteFrom('staffpanel')
                         ->where('id = ?', $id)
                         ->execute();
        $cache->delete('av_class_');
        $cache->delete('staff_panels_6');
        $cache->delete('staff_panels_5');
        $cache->delete('staff_panels_4');
        if ($result >= 1) {
            if ($user['class'] <= UC_MAX) {
                $page = "{$lang['spanel_page']} '[color=#" . get_user_class_color((int) $arr['av_class']) . "]{$arr['page_name']}[/color]'";
                $user_bbcode = "[url={$site_config['paths']['baseurl']}/userdetails.php?id={$user['id']}][color=#" . get_user_class_color($user['class']) . "]{$user['username']}[/color][/url]";
                write_log("$page {$lang['spanel_in_the_sp_was']} $action by $user_bbcode");
            }
            header('Location: ' . $_SERVER['PHP_SELF']);
            die();
        } else {
            stderr($lang['spanel_error'], $lang['spanel_db_error_msg']);
        }
    } elseif ($action === 'flush' && has_access($user['class'], UC_SYSOP, 'coder')) {
        $cache->flushDB();
        $session->set('is-success', 'You flushed the ' . ucfirst($site_config['cache']['driver']) . ' cache');
        header('Location: ' . $_SERVER['PHP_SELF']);
        die();
    } elseif ($action === 'uglify' && has_access($user['class'], UC_SYSOP, 'coder')) {
        toggle_site_status(true);
        $result = run_uglify();
        toggle_site_status(false);
        if ($result) {
            $session->set('is-success', 'All CSS and Javascript files processed');
            $cache->flushDB();
            $session->set('is-success', 'You flushed the ' . ucfirst($site_config['cache']['driver']) . ' cache');
        } else {
            $session->set('is-warning', 'uglify.php failed');
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        die();
    } elseif ($action === 'clear_ajaxchat' && has_access($user['class'], UC_SYSOP, 'coder')) {
        $fluent->deleteFrom('ajax_chat_messages')
               ->where('id>0')
               ->execute();
        $session->set('is-success', 'You deleted [i]all[/i] messages in AJAX Chat.');
        header('Location: ' . $_SERVER['PHP_SELF']);
        die();
    } elseif ($action === 'toggle_status' && has_access($user['class'], UC_SYSOP, 'coder')) {
        if (toggle_site_status($site_config['site']['online'])) {
            $session->set('is-success', 'Site is Online.');
        } else {
            $session->set('is-success', 'Site is Offline.');
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        die();
    } elseif (($action === 'add' && has_access($user['class'], UC_MAX, 'coder')) || ($action === 'edit' && is_valid_id($id) && $user['class'] >= UC_MAX)) {
        $names = [
            'page_name',
            'file_name',
            'description',
            'type',
            'av_class',
            'navbar',
        ];
        if ($action === 'edit') {
            $arr = $fluent->from('staffpanel')
                          ->select(null)
                          ->select('page_name')
                          ->select('file_name')
                          ->select('description')
                          ->select('type')
                          ->select('av_class')
                          ->select('navbar')
                          ->where('id = ?', $id)
                          ->fetch();
        }
        foreach ($names as $name) {
            ${$name} = (isset($_POST[$name]) ? $_POST[$name] : ($action === 'edit' ? $arr[$name] : ''));
        }
        if ($action === 'edit' && $user['class'] < $arr['av_class']) {
            stderr($lang['spanel_error'], $lang['spanel_cant_edit_this_pg']);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            if (empty($page_name)) {
                $errors[] = $lang['spanel_the_pg_name'] . ' ' . $lang['spanel_cannot_be_empty'] . '.';
            }
            if (empty($file_name)) {
                $errors[] = $lang['spanel_the_filename'] . ' ' . $lang['spanel_cannot_be_empty'] . '.';
            }
            if (empty($description)) {
                $errors[] = $lang['spanel_the_descr'] . ' ' . $lang['spanel_cannot_be_empty'] . '.';
            }
            if (!isset($navbar)) {
                $errors[] = 'Show in Navbar ' . $lang['spanel_cannot_be_empty'] . '.';
            }
            if (!in_array((int) $_POST['av_class'], $staff_classes)) {
                $errors[] = $lang['spanel_selected_class_not_valid'];
            }
            if (!empty($file_name) && !is_file($file_name . '.php') && !preg_match('/.php/', $file_name)) {
                $errors[] = $lang['spanel_inexistent_php_file'];
            }
            if (!empty($page_name) && strlen($page_name) < 4) {
                $errors[] = $lang['spanel_the_pg_name'] . ' ' . $lang['spanel_is_too_short_min_4'] . '.';
            }
            if (!empty($page_name) && strlen($page_name) > 80) {
                $errors[] = $lang['spanel_the_pg_name'] . ' ' . $lang['spanel_is_too_long'] . ' (' . $lang['spanel_max_80'] . ').';
            }
            if (!empty($file_name) && strlen($file_name) > 80) {
                $errors[] = $lang['spanel_the_filename'] . ' ' . $lang['spanel_is_too_long'] . ' (' . $lang['spanel_max_80'] . ').';
            }
            if (strlen($description) > 100) {
                $errors[] = $lang['spanel_the_descr'] . ' ' . $lang['spanel_is_too_long'] . ' (' . $lang['spanel_max_100'] . ').';
            }
            if (empty($errors)) {
                if ($action === 'add') {
                    $values = [
                        'page_name' => $page_name,
                        'file_name' => $file_name,
                        'description' => $description,
                        'type' => $type,
                        'av_class' => (int) $_POST['av_class'],
                        'added_by' => $user['id'],
                        'added' => TIME_NOW,
                        'navbar' => $navbar,
                    ];
                    try {
                        $new_id = $fluent->insertInto('staffpanel')
                                         ->values($values)
                                         ->execute();
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                    $cache->delete('staff_classes_');
                    $cache->delete('av_class_');
                    $classes = $fluent->from('class_config')
                                      ->select(null)
                                      ->select('DISTINCT value AS value')
                                      ->where('value >= ?', UC_STAFF);
                    foreach ($classes as $class) {
                        $cache->delete('staff_panels_' . $class['value']);
                    }
                } else {
                    $set = [
                        'navbar' => $navbar,
                        'page_name' => $page_name,
                        'file_name' => $file_name,
                        'description' => $description,
                        'type' => $type,
                        'av_class' => (int) $_POST['av_class'],
                    ];
                    $res = $fluent->update('staffpanel')
                           ->set($set)
                           ->where('id=?', $id)
                           ->execute();
                    $cache->delete('av_class_');
                    $classes = $fluent->from('class_config')
                                      ->select(null)
                                      ->select('DISTINCT value AS value')
                                      ->where('value>= ?', UC_STAFF);
                    foreach ($classes as $class) {
                        $cache->delete('staff_panels_' . $class['value']);
                    }
                    if (empty($res)) {
                        $errors[] = $lang['spanel_db_error_msg'];
                    }
                }
                if (empty($errors)) {
                    if ($user['class'] <= UC_MAX) {
                        $page = "{$lang['spanel_page']} '[color=#" . get_user_class_color((int) $_POST['av_class']) . "]{$page_name}[/color]'";
                        $what = $action === 'add' ? 'added' : 'edited';
                        $user_bbcode = "[url={$site_config['paths']['baseurl']}/userdetails.php?id={$user['id']}][color=#" . get_user_class_color($user['class']) . "]{$user['username']}[/color][/url]";
                        write_log("$page {$lang['spanel_in_the_sp_was']} $what by $user_bbcode");
                    }
                    $session->set('is-success', "'{$page_name}' " . ucwords($action) . 'ed Successfully');
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    die();
                }
            }
        }
        if (!empty($errors)) {
            $HTMLOUT .= stdmsg($lang['spanel_there'] . ' ' . (count($errors) > 1 ? 'are' : 'is') . ' ' . count($errors) . ' error' . (count($errors) > 1 ? 's' : '') . ' ' . $lang['spanel_in_the_form'] . '.', '<b>' . implode('<br>', $errors) . '</b>');
            $HTMLOUT .= '<br>';
        }
        $HTMLOUT .= "<form method='post' action='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' accept-charset='utf-8'>
    <input type='hidden' name='action' value='{$action}'>";
        if ($action === 'edit') {
            $HTMLOUT .= "<input type='hidden' name='id' value='{$id}'>";
        }
        $header = "
                <tr>
                    <th colspan='2'>
                        " . ($action === 'edit' ? $lang['spanel_edit'] . ' "' . $page_name . '"' : $lang['spanel_add_a_new']) . ' Staffpage' . '
                    </th>
                </tr>';
        $body = "
                <tr>
                    <td class='rowhead'>
                        {$lang['spanel_pg_name']}
                    </td>
                    <td>
                        <input type='text' class='w-100' name='page_name' value='{$page_name}' required>
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'>
                        {$lang['spanel_filename']}
                    </td>
                    <td>
                        <input type='text' class='w-100' name='file_name' value='{$file_name}' required>
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'>
                        {$lang['spanel_description']}
                    </td>
                    <td>
                        <input type='text' class='w-100' name='description' value='{$description}' required>
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'>
                        Show in Navbar
                    </td>
                    <td>
                        <input name='navbar' value='1' type='radio' " . ($navbar == 1 ? 'checked' : '') . "><span class='left5'>Yes</span><br>
                        <input name='navbar' value='0' type='radio' " . ($navbar == 0 ? 'checked' : '') . "><span class='left5'>No</span>
                    </td>
                </tr>";

        $types = [
            'user',
            'settings',
            'stats',
            'other',
        ];

        $body .= "
                <tr>
                    <td class='rowhead'>{$lang['spanel_type_of_tool']}</td>
                    <td>
                        <select name='type' required>
                            <option value=''>Choose Type</option>";
        foreach ($types as $this_type) {
            $body .= '
                            <option value="' . $this_type . '" ' . ($type === $this_type ? 'selected' : '') . '>' . ucfirst($this_type) . '</option>';
        }
        $body .= "
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'>
                        <span>{$lang['spanel_available_for']}</span>
                        </td>
                    <td>
                        <select name='av_class' required>
                            <option value=''>Choose Class</option>";
        $maxclass = UC_MAX;
        for ($class = UC_STAFF; $class <= $maxclass; ++$class) {
            $body .= '
                           <option value="' . $class . '" ' . (isset($arr['av_class']) && $arr['av_class'] == $class ? 'selected' : '') . '>' . get_user_class_name((int) $class) . '</option>';
        }
        $body .= '
                        </select>
                    </td>';

        $body .= '
                </tr>';

        $HTMLOUT .= main_table($body, $header);
        $HTMLOUT .= "
    <div class='level-center margin20'>
            <input type='submit' class='button is-small' value='{$lang['spanel_submit']}'>
        </form>
        <form method='post' action='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' accept-charset='utf-8'>
            <input type='submit' class='button is-small' value='{$lang['spanel_cancel']}'>
        </form>
    </div>";
        echo stdhead($lang['spanel_header'] . ' :: ' . ($action == 'edit' ? '' . $lang['spanel_edit'] . ' "' . $page_name . '"' : $lang['spanel_add_a_new']) . ' page', $stdhead) . wrapper($HTMLOUT) . stdfoot($stdfoot);
    } else {
        $add_button = '';
        if (has_access($user['class'], UC_SYSOP, 'coder')) {
            $add_button = "
                <ul class='level-center bg-06'>
                    <li class='margin10'>
                        <a href='{$_SERVER['PHP_SELF']}?action=add' class='tooltipper' title='{$lang['spanel_add_a_new_pg']}'>{$lang['spanel_add_a_new_pg']}</a>
                    </li>
                    <li class='margin10'>
                        <a href='{$_SERVER['PHP_SELF']}?action=clear_ajaxchat' class='tooltipper' title='{$lang['spanel_clear_chat_caution']}'>{$lang['spanel_clear_chat']}</a>
                    </li>
                    <li class='margin10'>
                        <a href='{$_SERVER['PHP_SELF']}?action=uglify' class='tooltipper' title='{$lang['spanel_uglify']}'>{$lang['spanel_uglify']}</a>
                    </li>
                    <li class='margin10'>
                        <a href='{$_SERVER['PHP_SELF']}?action=flush' class='tooltipper' title='{$lang['spanel_flush_cache']}'>{$lang['spanel_flush_cache']}</a>
                    </li>
                    <li class='margin10'>
                        <a href='{$_SERVER['PHP_SELF']}?action=toggle_status' class='tooltipper' title='{$lang['spanel_toggle_status_title']}'>{$lang['spanel_toggle_status']}</a>
                    </li>
                </ul>";
        }
        $user_class = $user['class'] >= UC_STAFF ? $user['class'] : UC_MAX;
        $data = $fluent->from('staffpanel AS s')
                       ->select('u.username')
                       ->leftJoin('users AS u ON s.added_by = u.id')
                       ->where('s.av_class <= ?', $user_class)
                       ->orderBy('s.av_class DESC')
                       ->orderBy('s.page_name')
                       ->fetchAll();
        if (!empty($data)) {
            $db_classes = $unique_classes = [];
            foreach ($data as $key => $value) {
                $db_classes[$value['av_class']][] = $value['av_class'];
            }
            $i = 1;
            $HTMLOUT .= "{$add_button}
            <h1 class='has-text-centered'>{$lang['spanel_welcome']} {$user['username']} {$lang['spanel_to_the']} {$lang['spanel_header']}!</h1>";

            $header = "
                    <tr>
                        <th class='w-50'>{$lang['spanel_pg_name']}</th>
                        <th><div class='has-text-centered'>Show in Navbar</div></th>
                        <th><div class='has-text-centered'>{$lang['spanel_added_by']}</div></th>
                        <th><div class='has-text-centered'>{$lang['spanel_date_added']}</div></th>";
            if ($user['class'] >= UC_MAX) {
                $header .= "
                        <th><div class='has-text-centered'>{$lang['spanel_links']}</div></th>";
            }
            $header .= '
                    </tr>';
            $body = '';
            foreach ($data as $key => $arr) {
                $end_table = count($db_classes[$arr['av_class']]) == $i ? true : false;

                if (!in_array($arr['av_class'], $unique_classes)) {
                    $unique_classes[] = $arr['av_class'];
                    $table = "
            <h1 class='has-text-centered text-shadow " . get_user_class_name((int) $arr['av_class'], true) . "'>" . get_user_class_name((int) $arr['av_class']) . "'s Panel</h1>";
                }
                $show_in_nav = $arr['navbar'] == 1 ? '
                <span class="has-text-success show_in_navbar tooltipper" title="Hide from Navbar" data-show="' . $arr['navbar'] . '" data-id="' . $arr['id'] . '">true</span>' : '
                <span class="has-text-info show_in_navbar tooltipper" title="Show in Navbar" data-show="' . $arr['navbar'] . '" data-id="' . $arr['id'] . '">false</span>';
                $body .= "
                    <tr>
                        <td>
                            <div class='size_4'>
                                <a href='{$site_config['paths']['baseurl']}/" . htmlsafechars($arr['file_name']) . "' class='tooltipper' title='" . htmlsafechars($arr['description'] . '<br>' . $arr['file_name']) . "'>" . ucwords(htmlsafechars($arr['page_name'])) . "</a>
                            </div>
                        </td>
                        <td>
                            <div class='has-text-centered'>
                                {$show_in_nav}
                            </div>
                        </td>
                        <td>
                            <div class='has-text-centered'>
                                " . format_username((int) $arr['added_by']) . "
                            </div>
                        </td>
                        <td>
                            <div class='has-text-centered'>
                                <span>" . get_date((int) $arr['added'], 'DATE', 0, 1) . '</span>
                            </div>
                        </td>';
                if (has_access($user['class'], UC_MAX, 'coder')) {
                    $body .= "
                        <td>
                            <div class='level-center'>
                                <a href='{$_SERVER['PHP_SELF']}?action=edit&amp;id=" . (int) $arr['id'] . "' class='tooltipper' title='{$lang['spanel_edit']}'>
                                    <i class='icon-edit icon has-text-info' aria-hidden='true'></i>
                                </a>
                                <a href='{$_SERVER['PHP_SELF']}?action=delete&amp;id=" . (int) $arr['id'] . "' class='tooltipper' title='{$lang['spanel_delete']}'>
                                    <i class='icon-trash-empty icon has-text-danger' aria-hidden='true'></i>
                                </a>
                            </div>
                        </td>";
                }
                $body .= '
                    </tr>';
                ++$i;
                if ($end_table) {
                    $i = 1;
                    $HTMLOUT .= "<div class='bg-00 top20 round10'>$table" . main_table($body, $header) . '</div>';
                    $body = '';
                }
            }
        } else {
            $HTMLOUT .= stdmsg($lang['spanel_sorry'], $lang['spanel_nothing_found']);
        }
        echo stdhead($lang['spanel_header'], $stdhead) . wrapper($HTMLOUT) . stdfoot($stdfoot);
    }
}
