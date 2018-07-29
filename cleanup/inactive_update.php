<?php

/**
 * @param $data
 */
function inactive_update($data)
{
    global $queries;

    set_time_limit(1200);
    ignore_user_abort(true);

    $users = [];

    $secs = 2 * 86400;
    $dt = (TIME_NOW - $secs);
    $res = sql_query("SELECT id FROM users
                        WHERE id != 2 AND status != 'confirmed' AND added < $dt") or sqlerr(__FILE__, __LINE__);
    while ($user = mysqli_fetch_assoc($res)) {
        $users[] = $user['id'];
    }

    $secs = 180 * 86400;
    $dt = (TIME_NOW - $secs);
    $maxclass = UC_STAFF;
    $res = sql_query("SELECT id FROM users
                        WHERE id != 2 AND immunity = 'no' AND parked = 'no' AND status = 'confirmed' AND class < $maxclass AND last_access < $dt") or sqlerr(__FILE__, __LINE__);
    while ($user = mysqli_fetch_assoc($res)) {
        $users[] = $user['id'];
    }

    $secs = 365 * 86400; // change the time to fit your needs
    $dt = (TIME_NOW - $secs);
    $maxclass = UC_STAFF;
    $res = sql_query("SELECT id FROM users
                        WHERE id != 2 AND immunity = 'no' AND parked = 'yes' AND status = 'confirmed' AND class < $maxclass AND last_access < $dt") or sqlerr(__FILE__, __LINE__);
    while ($user = mysqli_fetch_assoc($res)) {
        $users[] = $user['id'];
    }
    if (count($users) >= 1) {
        delete_cleanup(implode(', ', $users));
    }

    if ($data['clean_log'] && $queries > 0) {
        write_log("Inactive Cleanup: Completed using $queries queries");
    }
}

/**
 * @param      $users
 * @param bool $using_foreign_keys
 */
function delete_cleanup($users)
{
    global $cache;

    if (empty($users)) {
        return;
    }
    $cache->delete('all_users_');
    sql_query("DELETE FROM users WHERE id IN ({$users})") or sqlerr(__FILE__, __LINE__);
    sql_query("DELETE FROM staffmessages WHERE sender IN ({$users})") or sqlerr(__FILE__, __LINE__);
    sql_query("DELETE FROM staffmessages_answers WHERE sender IN ({$users})") or sqlerr(__FILE__, __LINE__);
    sql_query("DELETE FROM messages WHERE sender IN ({$users})") or sqlerr(__FILE__, __LINE__);
    sql_query("DELETE FROM messages WHERE receiver IN ({$users})") or sqlerr(__FILE__, __LINE__);
}
