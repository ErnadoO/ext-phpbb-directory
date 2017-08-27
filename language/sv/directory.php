<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'DIR_ARE_WATCHING_CAT'					=> 'Du prenumererar på att få notifikationer om nya hemsidor i denna kategori.',
	'DIR_BANNER_DISALLOWED_CONTENT'			=> 'Överföringen har avbrutits eftersom filen har identifierats som ett potentiellt hot.',
	'DIR_BANNER_DISALLOWED_EXTENSION'		=> 'Filen kan inte visas eftersom filändelsen <strong>%s</strong> inte är tillåten.',
	'DIR_BANNER_EMPTY_FILEUPLOAD'			=> 'Filen till bannern är tom.',
	'DIR_BANNER_EMPTY_REMOTE_DATA'			=> 'Den tillagda bannern kan inte överföras eftersom datan verkar vara felaktig eller skadad.',
	'DIR_BANNER_IMAGE_FILETYPE_MISMATCH'	=> 'Bannerns filtyp matchar inte: förväntade filändelsen %1$s men den hade istället filändelsen %2$s.',
	'DIR_BANNER_INVALID_FILENAME'			=> '%s är ett ogiltigt filnamn.',
	'DIR_BANNER_NOT_UPLOADED'				=> 'Bannern kan inte överföras',
	'DIR_BANNER_PARTIAL_UPLOAD'				=> 'Filen kan inte överföras helt och hållet.',
	'DIR_BANNER_PHP_SIZE_NA'				=> 'Bannerstorleken är för stor.<br />Den maximala storleken som är skriven i php.ini kunde inte bestämmas".',
	'DIR_BANNER_PHP_SIZE_OVERRUN'			=> 'Bannerstorleken är för stor. Den maximala storleken som tillåts är %d Mo.<br />Var vänlig notera att denna inställning är skriven i php.ini och kan inte överskridas.',
	'DIR_BANNER_REMOTE_UPLOAD_TIMEOUT'		=> 'Den specificerade bannern kunde inte laddas upp eftersom det tog för lång tid.',
	'DIR_BANNER_UNABLE_GET_IMAGE_SIZE'		=> 'Det gick inte att bestämma bannerns dimensioner',
	'DIR_BANNER_URL_INVALID'				=> 'Adressen för bannern är ogiltig',
	'DIR_BANNER_URL_NOT_FOUND'				=> 'Filen kan inte hittas.',
	'DIR_BANNER_WRONG_FILESIZE'				=> 'Bannerstorleken måste vara mellan 0 och %1d %2s.',
	'DIR_BANNER_WRONG_SIZE'					=> 'Den specificerade bannern har en bredd på %3$d pixlar och en höjd på %4$d pixlar. Bannern kan inte vara över %1$d pixlar bred och %2$d hög.',
	'DIR_BUTTON_NEW_SITE'					=> 'Ny länk',
	'DIR_CAT'								=> 'Kategori',
	'DIR_CAT_NAME'							=> 'Kategorinamn',
	'DIR_CAT_TITLE'							=> 'Kategorier i länklistan',
	'DIR_CAT_TOOLS'							=> 'Kategoriverktyg',
	'DIR_CLICK_RETURN_DIR'					=> 'Klicka %shär%s för att gå tillbaka till länklistans startsida',
	'DIR_CLICK_RETURN_CAT'					=> 'Klicka %shär%s för att gå tillbaka till kategorin',
	'DIR_CLICK_RETURN_COMMENT'				=> 'Klicka %shär%s för att gå tillbaka till kommentarssidan',
	'DIR_COMMENTS_ORDER'					=> 'Kommentarer',
	'DIR_COMMENT_TITLE'						=> 'Läs/Skriv en kommentar',
	'DIR_COMMENT_DELETE'					=> 'Ta bort kommentaren',
	'DIR_COMMENT_DELETE_CONFIRM'			=> 'Är du säker på att du vill ta bort kommentaren ?',
	'DIR_COMMENT_DELETE_OK'					=> 'Denna kommentar har tagits bort.',
	'DIR_COMMENT_EDIT'						=> 'Redigera kommentaren',
	'DIR_DELETE_BANNER'						=> 'Ta bort bannern',
	'DIR_DELETE_OK'							=> 'Hemsidan har tagits bort',
	'DIR_DELETE_SITE'						=> 'Tog bort hemsidan',
	'DIR_DELETE_SITE_CONFIRM'				=> 'Är du säker på att du vill ta bort hemsidan ?',
	'DIR_DESCRIPTION'						=> 'Beskrivning',
	'DIR_DESCRIPTION_EXP'					=> 'En kort beskrivning av din hemsida, max %d tecken.',
	'DIR_DISPLAY_LINKS'						=> 'Visa de föregående länkarna',
	'DIR_EDIT'								=> 'Redigera',
	'DIR_EDIT_COMMENT_OK'					=> 'Denna kommentar har redigerats',
	'DIR_EDIT_SITE'							=> 'Redigera en hemsida',
	'DIR_EDIT_SITE_ACTIVE'					=> 'Din hemsida har redigerats, men den måste godkännas innan den publiceras i länklistan',
	'DIR_EDIT_SITE_OK'						=> 'Hemsidan har redigerats',
	'DIR_ERROR_AUTH_COMM'					=> 'Du har inte tillåtelse att skriva en kommentar',
	'DIR_ERROR_CAT'							=> 'Ett fel uppstod vid försök att återställa data från den nuvarande kategorin.',
	'DIR_ERROR_CHECK_URL'					=> 'Denna webbadress verkar vara onåbar',
	'DIR_ERROR_COMM_LOGGED'					=> 'Du måste vara inloggad för att skriva en kommentar',
	'DIR_ERROR_KEYWORD'						=> 'Du måste skriva något ord för att göra en sökning.',
	'DIR_ERROR_NOT_AUTH'					=> 'Du har inte tillåtelse att genomföra denna ändring',
	'DIR_ERROR_NOT_FOUND_BACK'				=> 'Den specificerade hemsidan för tillbakalänken kan inte hittas.',
	'DIR_ERROR_NO_CATS'						=> 'Denna kategori finns inte',
	'DIR_ERROR_NO_LINK'						=> 'Hemsidan du letar efter finns inte',
	'DIR_ERROR_NO_LINKS'					=> 'Denna hemsida finns inte',
	'DIR_ERROR_NO_LINK_BACK'				=> 'Tillbakalänken hittades inte på hemsidan som du specificerade',
	'DIR_ERROR_SUBMIT_TYPE'					=> 'Felaktig datatyp i dir_submit_type function',
	'DIR_ERROR_URL'							=> 'Du måste skriva in en korrekt webbadress',
	'DIR_ERROR_VOTE'						=> 'Du har redan röstat på denna hemsida',
	'DIR_ERROR_VOTE_LOGGED'					=> 'Du måste vara inloggad för att kunna rösta',
	'DIR_ERROR_WRONG_DATA_BACK'				=> 'Adressen för tillbakalänken måste vara en giltig webbadress, inklusive protokollet (ex http). Till exempel http://www.example.com/.',
	'DIR_FIELDS'							=> 'Var vänlig fyll i alla fält med en *',
	'DIR_FLAG'								=> 'Flagga',
	'DIR_FLAG_EXP'							=> 'Välj en flagga, vilket indikerar nationaliteten av hemsidan',
	'DIR_FROM_TEN'							=> '%s/10',
	'DIR_GUEST_EMAIL'						=> 'Din e-postadress',
	'DIR_MAKE_SEARCH'						=> 'Sök efter en hemsida',
	'DIR_NAME_ORDER'						=> 'Namn',
	'DIR_NEW_COMMENT_OK'					=> 'Denna kommentar har lagts till',
	'DIR_NEW_SITE'							=> 'Lägg till en hemsida i länklistan',
	'DIR_NEW_SITE_ACTIVE'					=> 'Din hemsida har lagts till, men den måste godkännas innan den publiceras i länklistan',
	'DIR_NEW_SITE_OK'						=> 'Din hemsida har lagts till i länklistan',
	'DIR_NB_CLICKS'							=> array(
													1 => '%d klick',
													2 => '%d klick',
												),
	'DIR_NB_CLICKS_ORDER'					=> 'Klick',
	'DIR_NB_COMMS'							=> array(
													1 => '%d kommentar',
													2 => '%d kommentarer',
												),
	'DIR_NB_LINKS'							=> array(
													1 => '%d länk',
													2 => '%d länkar',
												),
	'DIR_NB_VOTES'							=> array(
													1 => '%d röst',
													2 => '%d röster',
												),
	'DIR_NONE'								=> 'Inga',
	'DIR_NOTE'								=> 'Notering',
	'DIR_NO_COMMENT'						=> 'Det finns inga kommentarer för denna hemsida',
	'DIR_NO_DRAW_CAT'						=> 'Det finns inga kategorier',
	'DIR_NO_DRAW_LINK'						=> 'Det finns inga hemsidor i kategorin',
	'DIR_NO_NOTE'							=> 'Inga',
	'DIR_NOT_WATCHING_CAT'					=> 'Du prenumererar inte längre på denna kategori.',

	'DIR_REPLY_EXP'							=> 'Din kommentar kan inte vara mer än %d tecken lång.',
	'DIR_REPLY_TITLE'						=> 'Lägg till en kommentar',
	'DIR_RSS'								=> 'RSS av',
	'DIR_SEARCH_CATLIST'					=> 'Sök i en specifik kategori',
	'DIR_SEARCH_DESC_ONLY'					=> 'Endast beskrivning',
	'DIR_SEARCH_METHOD'						=> 'Metod',
	'DIR_SEARCH_NB_CLICKS'					=> array(
													1 => 'Klick',
													2 => 'Klick',
												),
	'DIR_SEARCH_NB_COMMS'					=> array(
													1 => 'Kommentar',
													2 => 'Kommentarer',
												),
	'DIR_SEARCH_NO_RESULT'					=> 'Inga sökresultat',
	'DIR_SEARCH_RESULT'						=> 'Sökresultat',
	'DIR_SEARCH_SUBCATS'					=> 'Sök igenom underkategorier',
	'DIR_SEARCH_TITLE_DESC'					=> 'Namn och beskrivning',
	'DIR_SEARCH_TITLE_ONLY'					=> 'Endast namn',
	'DIR_SITE_BACK'							=> 'Webbadress för tillbakalänken',
	'DIR_SITE_BACK_EXPLAIN'					=> 'I denna kategori efterfrågas en tillbakalänk ifrån hemsidans ägare. Var vänlig specificera webbadressen till hemsidan där vi kan hitta länken',
	'DIR_SITE_BANN'							=> 'Lägg till en banner ',
	'DIR_SITE_BANN_EXP'						=> 'Du måste skriva in den kompletta webbadressen för din banner här. Var vänlig notera att detta fält inte är obligatoriskt. Den maximala tillåtna storleken är <b>%d x %d</b> pixlar, bannern kommer automatiskt att ändra storlek om den är för stor.',
	'DIR_SITE_NAME'							=> 'Hemsidans namn',
	'DIR_SITE_RSS'							=> 'RSS-flöde',
	'DIR_SITE_RSS_EXPLAIN'					=> 'Du kan lägga till adressen för RSS-flödet om det finns något. En RSS-ikon kommer att visas bredvid din hemsida, vilket visar att folk kan prenumerera på den',
	'DIR_SITE_URL'							=> 'Webbadress',
	'DIR_SOMMAIRE'							=> 'Startsidan för länklistan',
	'DIR_START_WATCHING_CAT'				=> 'Prenumerera på kategorin',
	'DIR_STOP_WATCHING_CAT'					=> 'Avregistrera dig från kategorin',
	'DIR_SUBMIT_TYPE_1'						=> 'Din hemsida måste godkännas av en administratör.',
	'DIR_SUBMIT_TYPE_2'						=> 'Din hemsida kommer omedelbart att synas i länklistan.',
	'DIR_SUBMIT_TYPE_3'						=> 'Du är administratör, din hemsida kommer automatiskt att läggas till.',
	'DIR_SUBMIT_TYPE_4'						=> 'Du är moderator, din hemsida kommer automatiskt att läggas till.',
	'DIR_THUMB'								=> 'Miniatyrbild för hemsidan',
	'DIR_USER_PROP'							=> 'Hemsida tillagd av',
	'DIR_VOTE'								=> 'Rösta',
	'DIR_VOTE_OK'							=> 'Din röst har blivit tillagd',
	'DIR_POST'								=> 'Lägg till',

	'DIRECTORY_TRANSLATION_INFO'			=> '',

	'RECENT_LINKS'							=> 'Senast tillagda hemsidor',

	'TOO_LONG_BACK'							=> 'Adressen som innehåller tillbakalänken är för lång (255 tecken max)',
	'TOO_LONG_DESCRIPTION'					=> 'Din beskrivning är för lång',
	'TOO_LONG_REPLY'						=> 'Din kommentar är för lång',
	'TOO_LONG_RSS'							=> 'Webbadressen för RSS-flödet är för lång',
	'TOO_LONG_SITE_NAME'					=> 'Namnet som du har skrivit in är för långt (100 tecken max)',
	'TOO_LONG_URL'							=> 'Webbadressen som du har skrivit in är för lång (255 tecken max)',
	'TOO_MANY_ADDS'							=> 'Du har uppnått gränsen för max antal försök att lägga till en hemsida. Var vänlig försök igen senare.',
	'TOO_SHORT_BACK'						=> 'Du måste skriva in adressen för hemsidan där tillbakalänken finns.',
	'TOO_SHORT_DESCRIPTION'					=> 'Du måste skriva en beskrivning',
	'TOO_SHORT_REPLY'						=> 'Din kommentar är för kort',
	'TOO_SHORT_RSS'							=> 'Webbadressen för RSS-flödet är för kort',
	'TOO_SHORT_SITE_NAME'					=> 'Du måste skriva ett namn för hemsidan',
	'TOO_SHORT_URL'							=> 'Du måste skriva en adress för hemsidan',
	'TOO_SMALL_CAT'							=> 'Du måste välja en kategori',

	'WRONG_DATA_RSS'						=> 'RSS-flödet måste vara en giltig webbadress, inklusive protokollet (ex http). Till exempel http://www.example.com/.',
	'WRONG_DATA_WEBSITE'					=> 'Hemsidans adress måste vara en giltig webbadress, inklusive protokollet (ex http). Till exempel http://www.example.com/.',
));
