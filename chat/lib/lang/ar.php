<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @author pepotiger (www.dd4bb.com)
 * @copyright (c) Sebastian Tschan
 * @license Modified MIT License
 * @link https://blueimp.net/ajax/
 */

$lang                                = [];
$lang['title']                       = 'AJAX Chat';
$lang['userName']                    = 'اسم المستخدم';
$lang['password']                    = 'كلمة المرور';
$lang['login']                       = 'دخول';
$lang['logout']                      = 'خروج';
$lang['channel']                     = 'الغرفة';
$lang['style']                       = 'الشكل';
$lang['language']                    = 'اللغة';
$lang['inputLineBreak']              = 'Press SHIFT+ENTER to input a line break';
$lang['messageSubmit']               = 'ارسل';
$lang['registeredUsers']             = 'الأعضاء المسجلين';
$lang['onlineUsers']                 = 'الأعضاء المتواجدين';
$lang['toggleAutoScroll']            = 'Autoscroll on/off';
$lang['toggleAudio']                 = 'Sound on/off';
$lang['toggleHelp']                  = 'Show/hide help';
$lang['toggleSettings']              = 'Show/hide settings';
$lang['toggleOnlineList']            = 'Show/hide online list';
$lang['bbCodeLabelBold']             = 'b';
$lang['bbCodeLabelItalic']           = 'i';
$lang['bbCodeLabelUnderline']        = 'u';
$lang['bbCodeLabelQuote']            = 'اقتباس';
$lang['bbCodeLabelCode']             = 'كود';
$lang['bbCodeLabelURL']              = 'رابط';
$lang['bbCodeLabelImg']              = 'Image';
$lang['bbCodeLabelColor']            = 'لون الخط';
$lang['bbCodeLabelEmoticon']         = 'Emoticons';
$lang['bbCodeTitleBold']             = 'نص عريض: [b]نص[/b]';
$lang['bbCodeTitleItalic']           = 'نص مائل: [i]نص[/i]';
$lang['bbCodeTitleUnderline']        = 'نص تحته خط: [u]نص[/u]';
$lang['bbCodeTitleQuote']            = 'نص مقتبس: [quote]نص[/quote] او [quote=الكاتب]نص[/quote]';
$lang['bbCodeTitleCode']             = 'عرض الكود: [code]كود[/code]';
$lang['bbCodeTitleURL']              = 'ادحال رابط: [url]http://example.org[/url] او [url=http://example.org]نص[/url]';
$lang['bbCodeTitleImg']              = 'Insert image: [img]http://example.org/image.jpg[/img]';
$lang['bbCodeTitleColor']            = 'لون النص: [color=red]نص[/color]';
$lang['bbCodeTitleEmoticon']         = 'Emoticons list';
$lang['help']                        = 'مساعدة';
$lang['helpItemDescJoin']            = 'دخول الغرفة:';
$lang['helpItemCodeJoin']            = '/join اسم الغرفة';
$lang['helpItemDescJoinCreate']      = 'انشاء غرفة خاصة (للمسجلين فقط):';
$lang['helpItemCodeJoinCreate']      = '/join';
$lang['helpItemDescInvite']          = 'دعوة احد (لغرفة خاصة مثلا):';
$lang['helpItemCodeInvite']          = '/invite username';
$lang['helpItemDescUninvite']        = 'الغاء الدعوة:';
$lang['helpItemCodeUninvite']        = '/uninvite Username';
$lang['helpItemDescLogout']          = 'خروج:';
$lang['helpItemCodeLogout']          = '/quit';
$lang['helpItemDescPrivateMessage']  = 'رسالة خاصة:';
$lang['helpItemCodePrivateMessage']  = '/msg Username نص';
$lang['helpItemDescQueryOpen']       = 'فتح نافذة خاصة:';
$lang['helpItemCodeQueryOpen']       = '/query Username';
$lang['helpItemDescQueryClose']      = 'غلق النافذة الخاصة:';
$lang['helpItemCodeQueryClose']      = '/query';
$lang['helpItemDescAction']          = 'وصف الحدث:';
$lang['helpItemCodeAction']          = '/action نص';
$lang['helpItemDescDescribe']        = 'وصف حدث برسالة خاصة:';
$lang['helpItemCodeDescribe']        = '/describe Username نص';
$lang['helpItemDescIgnore']          = 'تجاهل/قبول رسائل خاصة من:';
$lang['helpItemCodeIgnore']          = '/ignore Username';
$lang['helpItemDescIgnoreList']      = 'الأعضاء المتجاهلين:';
$lang['helpItemCodeIgnoreList']      = '/ignore';
$lang['helpItemDescWhereis']         = 'Display user channel:';
$lang['helpItemCodeWhereis']         = '/whereis Username';
$lang['helpItemDescKick']            = 'حظر مستخدمين (للمديرين فقط):';
$lang['helpItemCodeKick']            = '/kick Username [دقائق الحظر]';
$lang['helpItemDescUnban']           = 'الغاء حظر عضو (للمديرين فقط):';
$lang['helpItemCodeUnban']           = '/unban Username';
$lang['helpItemDescBans']            = 'الأعضاء المحظورين (للمديرين فقط):';
$lang['helpItemCodeBans']            = '/bans';
$lang['helpItemDescWhois']           = 'عرض الأى بى للمستخدم (المديرين فقط):';
$lang['helpItemCodeWhois']           = '/whois Username';
$lang['helpItemDescWho']             = 'الأعضاء المتواجدين:';
$lang['helpItemCodeWho']             = '/who [Channelname]';
$lang['helpItemDescList']            = 'القنوات المتوافرة:';
$lang['helpItemCodeList']            = '/list';
$lang['helpItemDescRoll']            = 'Roll dice:';
$lang['helpItemCodeRoll']            = '/roll [number]d[sides]';
$lang['helpItemDescNick']            = 'Change username:';
$lang['helpItemCodeNick']            = '/nick Username';
$lang['settings']                    = 'Settings';
$lang['settingsBBCode']              = 'Enable BBCode:';
$lang['settingsBBCodeImages']        = 'Enable image BBCode:';
$lang['settingsBBCodeColors']        = 'Enable font color BBCode:';
$lang['settingsHyperLinks']          = 'Enable hyperlinks:';
$lang['settingsLineBreaks']          = 'Enable line breaks:';
$lang['settingsEmoticons']           = 'Enable emoticons:';
$lang['settingsAutoFocus']           = 'Automatically set the focus on the input field:';
$lang['settingsMaxMessages']         = 'Maximum number of messages in the chatlist:';
$lang['settingsWordWrap']            = 'Enable wrapping of long words:';
$lang['settingsMaxWordLength']       = 'Maximum length of a word before it gets wrapped:';
$lang['settingsDateFormat']          = 'Format of date and time display:';
$lang['settingsPersistFontColor']    = 'Persist font color:';
$lang['settingsAudioSupport']        = 'Audio Support:';
$lang['settingsAudioVolume']         = 'Sound Volume:';
$lang['settingsSoundReceive']        = 'Sound for incoming messages:';
$lang['settingsSoundSend']           = 'Sound for outgoing messages:';
$lang['settingsSoundEnter']          = 'Sound for login and channel enter messages:';
$lang['settingsSoundLeave']          = 'Sound for logout and channel leave messages:';
$lang['settingsSoundChatBot']        = 'Sound for chatbot messages:';
$lang['settingsSoundError']          = 'Sound for error messages:';
$lang['settingsSoundPrivate']        = 'Sound for private messages:';
$lang['settingsBlink']               = 'Blink window title on new messages:';
$lang['settingsBlinkInterval']       = 'Blink interval in milliseconds:';
$lang['settingsBlinkIntervalNumber'] = 'Number of blink intervals:';
$lang['playSelectedSound']           = 'Play selected sound';
$lang['requiresJavaScript']          = 'يجب دعم الجافة سكريبت لهذا الشات.';
$lang['errorInvalidUser']            = 'اسم مستخدم خطأ.';
$lang['errorUserInUse']              = 'اسم المستخدم مستخدم.';
$lang['errorBanned']                 = 'المستخدم او عنوان الأى بى محظور.';
$lang['errorMaxUsersLoggedIn']       = 'الشات به الحد الأقصى من الأعضاء المسجلين.';
$lang['errorChatClosed']             = 'الشات مغلق حاليا.';
$lang['logsTitle']                   = 'سحل الشات';
$lang['logsDate']                    = 'التاريخ';
$lang['logsTime']                    = 'الوقت';
$lang['logsSearch']                  = 'بحث';
$lang['logsPrivateChannels']         = 'القنوات الخاصة';
$lang['logsPrivateMessages']         = 'الرسائل الخاصة';
