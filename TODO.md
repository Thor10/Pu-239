In no particular order  

add: cleanup scrip to add reputation points based on active time on site  
report comments not working correctly  
fix: iphistory.php  
update: blocks to include cooker  
update: upload to look to recipes in cooker  
add: notifications to cooker  
fix: parallax scrolling in firefox  
check: upload/download peers speed showing 0  
update: "Signups are Invite Only" to "Invalid or expired promotion link"
update: user info for paranoia  
replace or fix cookie consent, seems lifetime doesn't set or stay set correctly  
update code to use freetorrent and doubletorrent instead of free and double  
remove all cacheing from peers table, neither ocelot nor xbt can read from or update the cache  
finish: offers, requests, upcomin and bot replies  
add: script to check cheaters, make request to client to get random chunk, validate chuck against hash, update db for success, failure to validate, empty response and no response
remove: need for session in announce.php scrape.php  
check: $torrent_updateset['times_completed'] = $torrent['times_completed'] + 1; announce.php  
check: thanks  
fix: forum topic rating  
update admin/shit_list.php  
finish: casino.php rewrite  
remove the need for using global $mysqli  
remove the need for using global $CURUSER  

find and update all users "Pending"  
finish replacing homespun user create/delete/etc  

add torrent client ban pages, started in demo site  
finish cache inbox_ and messages_stuff  
fix birthday cleanup  
fix karma cleanup  


user blocks AVATAR is only used in userdetails.php, check to see if useful elsewhere in code  
replace mysql full text search with elasticsearch  
add language to staff.php  
replace current authentication system  
replace current session handler  
add ip login/seedbox restrictions  
finish/update/replace breadcrumbs  
replace page refresh with ajax for clickable items  
add live search (look ahead) to all search blocks  
add bot replies  
add lyrics api musiXmatch  
add music api spotify/last.fm  
replace paypal with stripe  
merge thanks and thankyou tables/code  
update headers: location  
what is cache userstatus  
update queries for user, get/set cache instead of query to get, only to set  
update caches replace delete with proper update  
add daily, weekly, monthly to top 10 stats page  
finish torrent blocks in userdetails, add pagination, initially closed on page load  
remove begin_table and end_table functions  
remove begin_frame and end_frame functions  
remove begin_main_frame and end_main_frame functions  
remove/replace function textbbcode calls, doesn't exist  
replace mysqli commands with pdo/fluentpdo  
format tables using main_table function  
format divs using main_div function  

xbt  
ocelot  
