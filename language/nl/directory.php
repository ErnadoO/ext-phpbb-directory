<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
* Dutch translation by Dutch Translators (https://github.com/dutch-translators)
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
	'DIR_ARE_WATCHING_CAT'					=> 'Je bent geabonneerd om deze categorie, om om de hoogte gehouden te worden van nieuwe websites in deze categorie.',
	'DIR_BANNER_DISALLOWED_CONTENT'			=> 'De overdraging van het bestand is onderbroken, omdat het wordt aangezien als bedreiging.',
	'DIR_BANNER_DISALLOWED_EXTENSION'		=> 'Het bestand kan niet worden getoond omdat de extensie <strong>%s</strong> niet is toegestaan.',
	'DIR_BANNER_EMPTY_FILEUPLOAD'			=> 'Het bannerbestand is leeg.',
	'DIR_BANNER_EMPTY_REMOTE_DATA'			=> 'De ingevoerde banner kan niet worden verwerkt omdat de gegevens incorrect of beschadigt is.',
	'DIR_BANNER_IMAGE_FILETYPE_MISMATCH'	=> 'Verkeerde bannerbestandstype: verwachte extensie %1$s ingevoerde extensie %2$s.',
	'DIR_BANNER_INVALID_FILENAME'			=> '%s is een ongeldige bestandsnaam.',
	'DIR_BANNER_NOT_UPLOADED'				=> 'De banner kan niet worden overgedragen',
	'DIR_BANNER_PARTIAL_UPLOAD'				=> 'Het bestand kan niet volledig worden overgedragen.',
	'DIR_BANNER_PHP_SIZE_NA'				=> 'De bannergrootte is te groot.<br />De maximale grootte die is ingesteld in de php.ini kan niet worden vastgesteld.',
	'DIR_BANNER_PHP_SIZE_OVERRUN'			=> 'De bannergrootte is te groot. De maximaal toegestane bestandsgrootte is %d Mo.<br />Houd er rekening mee dat dit is ingesteld in de php.ini en niet overtroffen kan worden.',
	'DIR_BANNER_REMOTE_UPLOAD_TIMEOUT'		=> 'De banner kan niet worden worden geüpload omdat de aanvraag is verlopen.',
	'DIR_BANNER_UNABLE_GET_IMAGE_SIZE'		=> 'Het is niet mogelijk om de afmetingen van de banner te bepalen  ',
	'DIR_BANNER_URL_INVALID'				=> 'Het banneradres is ongeldig',
	'DIR_BANNER_URL_NOT_FOUND'				=> 'Het bestand kan niet worden gevonden.',
	'DIR_BANNER_WRONG_FILESIZE'				=> 'De bannerafmeting moet tussen de 0 en %1d %2s zijn.',
	'DIR_BANNER_WRONG_SIZE'					=> 'De gespecificeerde banner heeft een breedte van %3$d pixels en een hoogte van %4$d pixels. De banner mag niet meer dan %1$d pixels breed en %2$d hoog zijn.',
	'DIR_BUTTON_NEW_SITE'					=> 'Nieuwe website',
	'DIR_CAT'								=> 'Categorie',
	'DIR_CAT_NAME'							=> 'Categorienaam',
	'DIR_CAT_TITLE'							=> 'Directory categorieën',
	'DIR_CAT_TOOLS'							=> 'Categorie-gereedschap',
	'DIR_CLICK_RETURN_DIR'					=> 'Klik %shier%s om terug te gaan naar de directory index',
	'DIR_CLICK_RETURN_CAT'					=> 'Klik %shier%s om terug te gaan naar de categorie',
	'DIR_CLICK_RETURN_COMMENT'				=> 'Klik %shier%s om terug te gaan naar de reactiepagina',
	'DIR_COMMENTS_ORDER'					=> 'Reacties',
	'DIR_COMMENT_TITLE'						=> 'Lees/plaats een reactie',
	'DIR_COMMENT_DELETE'					=> 'Verwijder de reactie',
	'DIR_COMMENT_DELETE_CONFIRM'			=> 'Weet je zeker dat je de reactie wilt verwijderen?',
	'DIR_COMMENT_DELETE_OK'					=> 'De reactie is succesvol verwijderd.',
	'DIR_COMMENT_EDIT'						=> 'Wijzig reactie',
	'DIR_DELETE_BANNER'						=> 'Verwijder banner',
	'DIR_DELETE_OK'							=> 'De website is succesvol verwijderd',
	'DIR_DELETE_SITE'						=> 'Verwijder de website',
	'DIR_DELETE_SITE_CONFIRM'				=> 'Weet je zeker dat je de website wilt verwijderen?',
	'DIR_DESCRIPTION'						=> 'Beschrijving',
	'DIR_DESCRIPTION_EXP'					=> 'Een korte beschrijving van de website, max %d tekens.',
	'DIR_DISPLAY_LINKS'						=> 'Links van vorige weergeven',
	'DIR_EDIT'								=> 'Wijzig',
	'DIR_EDIT_COMMENT_OK'					=> 'De reactie is succesvol gewijzigd',
	'DIR_EDIT_SITE'							=> 'Wijzig de website',
	'DIR_EDIT_SITE_ACTIVE'					=> 'Je website is succesvol gewijzigd, je wijziging moet eerst worden goedgekeurd voor je hem kan zien in de directory',
	'DIR_EDIT_SITE_OK'						=> 'De website is succesvol gewijzigd',
	'DIR_ERROR_AUTH_COMM'					=> 'Je hebt geen toestemming om een reactie te plaatsen',
	'DIR_ERROR_CAT'							=> 'Fout tijdens het herstellen van de gegevens van de huidige categorie.',
	'DIR_ERROR_CHECK_URL'					=> 'Deze link lijkt onbereikbaar',
	'DIR_ERROR_COMM_LOGGED'					=> 'Je moet aangemeld zijn om een reactie te kunnen plaatsen',
	'DIR_ERROR_KEYWORD'						=> 'Je moet sleutelwoorden invoeren om te zoeken.',
	'DIR_ERROR_NOT_AUTH'					=> 'Je hebt geen toestemming om deze handeling uit te voeren',
	'DIR_ERROR_NOT_FOUND_BACK'				=> 'De opgegeven pagina voor de backlink is niet gevonden.',
	'DIR_ERROR_NO_CATS'						=> 'Deze categorie bestaat niet',
	'DIR_ERROR_NO_LINK'						=> 'De website waar je naar opzoek bent bestaat niet',
	'DIR_ERROR_NO_LINKS'					=> 'De website bestaat niet',
	'DIR_ERROR_NO_LINK_BACK'				=> 'De backlink is niet gevonden op de pagina die je opgegeven hebt',
	'DIR_ERROR_SUBMIT_TYPE'					=> 'Verkeerde gegevens in de “dir_submit_type”-functie',
	'DIR_ERROR_URL'							=> 'Je moet een geldige link opgeven',
	'DIR_ERROR_VOTE'						=> 'Je hebt al op deze website gestemd',
	'DIR_ERROR_VOTE_LOGGED'					=> 'Je moet aangemeld zijn om te kunnen stemmen',
	'DIR_ERROR_WRONG_DATA_BACK'				=> 'Het adres voor de backlink moet een geldige link zijn, inclusief protocol. Bijvoorbeeld: http://www.example.com/.',
	'DIR_FIELDS'							=> 'Alle velden met een * moeten ingevuld worden',
	'DIR_FLAG'								=> 'Vlag',
	'DIR_FLAG_EXP'							=> 'Selecteer een vlag, die de nationaliteit van de website aangeeft',
	'DIR_FROM_TEN'							=> '%s/10',
	'DIR_GUEST_EMAIL'						=> 'Je e-mailadres',
	'DIR_MAKE_SEARCH'						=> 'Zoek een website',
	'DIR_NAME_ORDER'						=> 'Naam',
	'DIR_NEW_COMMENT_OK'					=> 'Je reactie is succesvol geplaatst',
	'DIR_NEW_SITE'							=> 'Voeg een website toe aan de directory',
	'DIR_NEW_SITE_ACTIVE'					=> 'Je website is toegevoegd, maar moet eerst worden goedgekeurd voor je hem kan zien in de directory',
	'DIR_NEW_SITE_OK'						=> 'Je website is toegevoegd aan de directory',
	'DIR_NB_CLICKS'							=> array(
													1 => '%d klik',
													2 => '%d klikken',
												),
	'DIR_NB_CLICKS_ORDER'					=> 'Klikken',
	'DIR_NB_COMMS'							=> array(
													1 => '%d reactie',
													2 => '%d reacties',
												),
	'DIR_NB_LINKS'							=> array(
													1 => '%d link',
													2 => '%d links',
												),
	'DIR_NB_VOTES'							=> array(
													1 => '%d stem',
													2 => '%d stemmen',
												),
	'DIR_NONE'								=> 'Geen',
	'DIR_NOTE'								=> 'Notatie',
	'DIR_NO_COMMENT'						=> 'Er zijn geen reacties op deze website',
	'DIR_NO_DRAW_CAT'						=> 'Er zijn geen categorieën',
	'DIR_NO_DRAW_LINK'						=> 'Er zijn geen websites in deze categorie',
	'DIR_NO_NOTE'							=> 'Geen',
	'DIR_NOT_WATCHING_CAT'					=> 'Je bent niet meer geabonneerd op deze categorie.',

	'DIR_REPLY_EXP'							=> 'Je reactie kan niet meer dan %d tekens lang zijn.',
	'DIR_REPLY_TITLE'						=> 'Plaats een reactie',
	'DIR_RSS'								=> 'RSS van',
	'DIR_SEARCH_CATLIST'					=> 'Zoek in een categorie',
	'DIR_SEARCH_DESC_ONLY'					=> 'Alleen op beschrijving',
	'DIR_SEARCH_METHOD'						=> 'Methode',
	'DIR_SEARCH_NB_CLICKS'					=> array(
													1 => 'Klik',
													2 => 'Klikken',
												),
	'DIR_SEARCH_NB_COMMS'					=> array(
													1 => 'Reactie',
													2 => 'Reacties',
												),
	'DIR_SEARCH_NO_RESULT'					=> 'Geen resultaten voor de zoekopdracht',
	'DIR_SEARCH_RESULT'						=> 'Zoekresultaten',
	'DIR_SEARCH_SUBCATS'					=> 'Zoek in subcategorie',
	'DIR_SEARCH_TITLE_DESC'					=> 'Naam en beschrijving',
	'DIR_SEARCH_TITLE_ONLY'					=> 'Alleen op naam',
	'DIR_SITE_BACK'							=> 'Link backlink pagina',
	'DIR_SITE_BACK_EXPLAIN'					=> 'In deze categorie wordt gevraagd om een backlink te plaatsen. Plaats hier een link naar de pagina waar de backlink staat.',
	'DIR_SITE_BANN'							=> 'Voeg een banner toe ',
	'DIR_SITE_BANN_EXP'						=> 'Plaats hier de volledige link naar je banner. Houd er rekening mee dat dit veld niet verplicht is. De maximale toegestane afmetingen zijn <b>%d x %d</b> pixels, de banner wordt automatische verkleind als de afmetingen worden overschreden.',
	'DIR_SITE_NAME'							=> 'Website naam',
	'DIR_SITE_RSS'							=> 'RSS feeds',
	'DIR_SITE_RSS_EXPLAIN'					=> 'Je kan hier een link naar de RSS feeds toevoegen. Een RSS-icoontje wordt dan weergegeven naast de website, zodat gebruikers zich daarop kunnen abonneren',
	'DIR_SITE_URL'							=> 'Link',
	'DIR_SOMMAIRE'							=> 'Directory index',
	'DIR_START_WATCHING_CAT'				=> 'Abonneer categorie',
	'DIR_STOP_WATCHING_CAT'					=> 'Uitschrijven categorie',
	'DIR_SUBMIT_TYPE_1'						=> 'Je website moet eerst goedgekeurd worden door een beheerder.',
	'DIR_SUBMIT_TYPE_2'						=> 'Je website verschijnt meteen in de directory.',
	'DIR_SUBMIT_TYPE_3'						=> 'Je bent een beheerder, je website wordt meteen toegevoegd.',
	'DIR_SUBMIT_TYPE_4'						=> 'Je bent een moderator, je website wordt meteen toegevoegd.',
	'DIR_THUMB'								=> 'Websiteminiatuur',
	'DIR_USER_PROP'							=> 'Website toegevoegd door',
	'DIR_VOTE'								=> 'Stem',
	'DIR_VOTE_OK'							=> 'Je stem is toegevoegd',
	'DIR_POST'								=> 'Bericht',

	'DIRECTORY_TRANSLATION_INFO'			=> 'Vertaald door <a href="https://github.com/dutch-translators">dutch-translators</a>',

	'RECENT_LINKS'							=> 'Laatst toegevoegde website',

	'TOO_LONG_BACK'							=> 'Adres met de link back is te lang (maximum 255 tekens)',
	'TOO_LONG_DESCRIPTION'					=> 'Je beschrijving is te lang',
	'TOO_LONG_REPLY'						=> 'Je reactie is te lang',
	'TOO_LONG_RSS'							=> 'De link voor de RSS feeds die je hebt opgegeven is te lang',
	'TOO_LONG_SITE_NAME'					=> 'De naam die je hebt opgegeven is te lang (max 100 tekens)',
	'TOO_LONG_URL'							=> 'De link die je hebt opgegeven is te lang (max 255 tekens)',
	'TOO_MANY_ADDS'							=> 'Je hebt het maximaal aantal pogingen om een website toe te voegen bereikt. Probeer het later nog eens.',
	'TOO_SHORT_BACK'						=> 'Je moet het adres van de pagina invullen waar de backlink is.',
	'TOO_SHORT_DESCRIPTION'					=> 'Je moet een beschrijving opgeven voor de website',
	'TOO_SHORT_REPLY'						=> 'Je reactie is te kort',
	'TOO_SHORT_RSS'							=> 'De link voor de RSS feeds is te kort',
	'TOO_SHORT_SITE_NAME'					=> 'Je moet een naam opgeven voor deze website',
	'TOO_SHORT_URL'							=> 'Je moet een link opgeven voor de website',
	'TOO_SMALL_CAT'							=> 'Je moet een categorie selecteren',

	'WRONG_DATA_RSS'						=> 'De ingevoerde RSS feeds link moet geldig zijn, inclusief protocol. Bijvoorbeeld: http://www.example.com/.',
	'WRONG_DATA_WEBSITE'	                => 'De ingevoerde link moet geldig zijn, inclusief protocol. Bijvoorbeeld: http://www.example.com/.',
));
