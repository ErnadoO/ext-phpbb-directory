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
	'DIR_ARE_WATCHING_CAT'					=> 'Od sada pratite ovu kategoriju',
	'DIR_BANNER_DISALLOWED_CONTENT'			=> 'Transfer je prekinut, jer je fajl identifikovan kao potencijalna pretnja.',
	'DIR_BANNER_DISALLOWED_EXTENSION'		=> 'Fajl ne može biti prikazan jer ekstenzija <strong>%s</strong> nije dozvoljena.',
	'DIR_BANNER_EMPTY_FILEUPLOAD'			=> 'Baner fajl je prazan.',
	'DIR_BANNER_EMPTY_REMOTE_DATA'			=> 'Uneti baner ne može biti prebačen jer su podaci netačni ili oštećeni',
	'DIR_BANNER_IMAGE_FILETYPE_MISMATCH'	=> 'Tip baner fajla nije odgovarajući: očekivana ekstenzija je %1$s a uneta je %2$s.',
	'DIR_BANNER_INVALID_FILENAME'			=> '%s je nevažeće ime za fajl.',
	'DIR_BANNER_NOT_UPLOADED'				=> 'Baner ne može biti prebačen',
	'DIR_BANNER_PARTIAL_UPLOAD'				=> 'Fajl ne može biti prebačen u potpunosti',
	'DIR_BANNER_PHP_SIZE_NA'				=> 'Veličina banera prevazilazi dozvoljenu.<br />maksimalna veličina postavljena u php.ini ne može biti utvrdjena".',
	'DIR_BANNER_PHP_SIZE_OVERRUN'			=> 'Veličina banera prevazilazi dozvoljenu. Maksimalna veličina je %d Mo.<br />Veličina vašeg banera ne sme da prelazi veličinu definisanu u php.ini.',
	'DIR_BANNER_REMOTE_UPLOAD_TIMEOUT'		=> 'Od sada pratite ovu kategoriju.',
	'DIR_BANNER_UNABLE_GET_IMAGE_SIZE'		=> 'Nemoguće je utvrditi dimenzije banera',
	'DIR_BANNER_URL_INVALID'				=> 'Uneta baner adressa je nevažeća',
	'DIR_BANNER_URL_NOT_FOUND'				=> 'Nepostojeća stranica',
	'DIR_BANNER_WRONG_FILESIZE'				=> 'Veličina banera mora biti izmedju 0 i %1d %2s.',
	'DIR_BANNER_WRONG_SIZE'					=> 'Uneti baner je širok %3$d piksela i visok %4$d piksela. Širina ne sme da prelazi %1$d piksela, a visina %2$d.',
	'DIR_BUTTON_NEW_SITE'					=> 'Novi link',
	'DIR_CAT'								=> 'Kategorija',
	'DIR_CAT_NAME'							=> 'Ime kategorije',
	'DIR_CAT_TITLE'							=> 'Direktorijum kategorija',
	'DIR_CAT_TOOLS'							=> 'Alatke kategorije',
	'DIR_CLICK_RETURN_DIR'					=> 'Kliknuti %sovde%s da biste se vratili na početnu stranicu direktorijuma',
	'DIR_CLICK_RETURN_CAT'					=> 'Kliknuti %sovde%s da biste se vratili u kategoriju',
	'DIR_CLICK_RETURN_COMMENT'				=> 'Kliknuti %sovde%s za povratak na stranicu sa komenatarima',
	'DIR_COMMENTS_ORDER'					=> 'Komentari',
	'DIR_COMMENT_TITLE'						=> 'Pročitati/Ostaviti komentar',
	'DIR_COMMENT_DELETE'					=> 'Izbrisati komentar',
	'DIR_COMMENT_DELETE_CONFIRM'			=> 'Da li ste sigurni da želite da izbrišete komentar?',
	'DIR_COMMENT_DELETE_OK'					=> 'Komentar je uspešno izbrisan.',
	'DIR_COMMENT_EDIT'						=> 'Izmeniti komentare',
	'DIR_DELETE_BANNER'						=> 'Izbrisati baner',
	'DIR_DELETE_OK'							=> 'Vebsajt je izbrisan',
	'DIR_DELETE_SITE'						=> 'Izbrisati vebsajt',
	'DIR_DELETE_SITE_CONFIRM'				=> 'Da li ste sigurni da želite da izbrišete vebsajt ?',
	'DIR_DESCRIPTION'						=> 'Opis',
	'DIR_DESCRIPTION_EXP'					=> 'Kratak opis vašeg vebsajta, maksimum %d karaktera.',
	'DIR_DISPLAY_LINKS'						=> 'Prikazati prethodne linkove',
	'DIR_EDIT'								=> 'Izmeniti',
	'DIR_EDIT_COMMENT_OK'					=> 'Vaš komentar je uspešno izmenjen',
	'DIR_EDIT_SITE'							=> 'Izmeniti vebsajt',
	'DIR_EDIT_SITE_ACTIVE'					=> 'Vas vebsajt je modifikovan, ali morate sačekati odobrenje da bi se pojavio u direktorijumu',
	'DIR_EDIT_SITE_OK'						=> 'Vebsajt je modifikovan',
	'DIR_ERROR_AUTH_COMM'					=> 'Nemate dozvolu da ostavite komentar',
	'DIR_ERROR_CAT'							=> 'Greška prilikom pokušaja vraćanja kategorije u prethodno stanje.',
	'DIR_ERROR_CHECK_URL'					=> 'Nema odgovora sa unetog URL-a',
	'DIR_ERROR_COMM_LOGGED'					=> 'Morate biti ulogovani da biste ostavili komentar',
	'DIR_ERROR_KEYWORD'						=> 'Morate uneti ključne reči da biste otpočeli pretragu',
	'DIR_ERROR_NOT_AUTH'					=> 'Nemate dozvolu za ovu operaciju',
	'DIR_ERROR_NOT_FOUND_BACK'				=> 'Uneta stranica sa povratnim linkom (link back) nije pronadjena',
	'DIR_ERROR_NO_CATS'						=> 'Ta kategorija ne postoji.',
	'DIR_ERROR_NO_LINK'						=> 'Traženi vebsajt ne postoji.',
	'DIR_ERROR_NO_LINKS'					=> 'Taj vebsajt ne postoji',
	'DIR_ERROR_NO_LINK_BACK'				=> 'Povratni link nije pronadjen na unetoj stranici.',
	'DIR_ERROR_SUBMIT_TYPE'					=> 'Netačan tip podataka u dir_submit_type funkciji',
	'DIR_ERROR_URL'							=> 'Morate uneti validan URL',
	'DIR_ERROR_VOTE'						=> 'Već ste glasali za ovaj vebsajt',
	'DIR_ERROR_VOTE_LOGGED'					=> 'Morate biti ulogovani da biste glasali',
	'DIR_ERROR_WRONG_DATA_BACK'				=> 'Adresa povratnog linka mora biti validan URL, uključujući i protokol. Na primer http://www.example.com/.',
	'DIR_FIELDS'							=> 'Molimo popunite sva polja sa *',
	'DIR_FLAG'								=> 'Zastava',
	'DIR_FLAG_EXP'							=> 'Izaberite zastavu, koja odgovara nacionalnosti sajta',
	'DIR_FROM_TEN'							=> '%s/10',
	'DIR_GUEST_EMAIL'						=> 'Vaša email adresa',
	'DIR_MAKE_SEARCH'						=> 'Pretraga vebsajta',
	'DIR_NAME_ORDER'						=> 'Ime',
	'DIR_NEW_COMMENT_OK'					=> 'Vaš komentar je uspešno objavljen',
	'DIR_NEW_SITE'							=> 'Dodati vebsajt u direktorijum',
	'DIR_NEW_SITE_ACTIVE'					=> 'Vaš vebsajt je dodat, ali morate sačekati odobrenje da bi se pojavio u direktorijumu',
	'DIR_NEW_SITE_OK'						=> 'Vaš vebsajt je dodat',
	'DIR_NB_CLICKS'							=> array(
													1 => '%d klik',
													2 => '%d klika',
													3 => '%d klikova',
												),
	'DIR_NB_CLICKS_ORDER'					=> 'Klikovi',
	'DIR_NB_COMMS'							=> array(
													1 => '%d komentar',
													2 => '%d komentara',
													3 => '%d komentara',
												),
	'DIR_NB_LINKS'							=> array(
													1 => '%d link',
													2 => '%d linka',
													3 => '%d linkovi',
												),
	'DIR_NB_VOTES'							=> array(
													1 => '%d glas',
													2 => '%d glasa',
													3 => '%d glasovi',
												),
	'DIR_NONE'								=> 'Ništa',
	'DIR_NOTE'								=> 'Notacija',
	'DIR_NO_COMMENT'						=> 'Nema komentara za ovaj vebsajt',
	'DIR_NO_DRAW_CAT'						=> 'Nema kategorija',
	'DIR_NO_DRAW_LINK'						=> 'Nema vebsajtova u ovoj kategoriji',
	'DIR_NO_NOTE'							=> 'Ništa',
	'DIR_NOT_WATCHING_CAT'					=> 'Ne pratite više ovu kategoriju',

	'DIR_REPLY_EXP'							=> 'Vaš komentar ne sme da sadrži više od %d karaktera.',
	'DIR_REPLY_TITLE'						=> 'Ostaviti komentar',
	'DIR_RSS'								=> 'RSS of',
	'DIR_SEARCH_CATLIST'					=> 'Tražiti u posebnoj kategoriji',
	'DIR_SEARCH_DESC_ONLY'					=> 'Jedino opis',
	'DIR_SEARCH_METHOD'						=> 'Method',
	'DIR_SEARCH_NB_CLICKS'					=> array(
													1 => 'Klik',
													2 => 'Klika',
													3 => 'Klikova',
												),
	'DIR_SEARCH_NB_COMMS'					=> array(
													1 => 'Komentar',
													2 => 'Komentara',
													3 => 'Komentara',
												),
	'DIR_SEARCH_NO_RESULT'					=> 'Nema rezultata',
	'DIR_SEARCH_RESULT'						=> 'Rezultati pretrage',
	'DIR_SEARCH_SUBCATS'					=> 'Pretražiti pod kategorije',
	'DIR_SEARCH_TITLE_DESC'					=> 'Ime i opis',
	'DIR_SEARCH_TITLE_ONLY'					=> 'Ime',
	'DIR_SITE_BACK'							=> 'URL stranice povratnog linka',
	'DIR_SITE_BACK_EXPLAIN'					=> 'U ovoj kategoriji potrebno je da vlasnik sajta doda povratni link. Molimo vas da unesete URL stranice na kojoj se nalazi link.',
	'DIR_SITE_BANN'							=> 'Dodati baner ',
	'DIR_SITE_BANN_EXP'						=> 'Ovde morate uneti kompletan URL vašeg banera. Ovo polje nije obavezno. Maksimalna veličina je <b>%d x %d</b> piksela, Baner ce biti automatski umanjen ako prelazi ovu veličinu.',
	'DIR_SITE_NAME'							=> 'Ime vebsajta',
	'DIR_SITE_RSS'							=> 'RSS feeds',
	'DIR_SITE_RSS_EXPLAIN'					=> 'Mozete dodati adresu RSS feeds ako postoji. Ikonica RSS ce biti prikazana pored vašeg vebsajta,da bi omogućila ljudima da se prijave.',
	'DIR_SITE_URL'							=> 'URL',
	'DIR_SOMMAIRE'							=> 'Početna stranica direktorijuma',
	'DIR_START_WATCHING_CAT'				=> 'Zapratiti kategoriju',
	'DIR_STOP_WATCHING_CAT'					=> 'Odustati od praćenja kategorije',
	'DIR_SUBMIT_TYPE_1'						=> 'Your website need to be approved by an adminsitrator.',
	'DIR_SUBMIT_TYPE_2'						=> 'Vaš vebsajt će odmah biti dodat u direktorijum.',
	'DIR_SUBMIT_TYPE_3'						=> 'Vi ste administrator, vaš vebsajt će biti dodat odmah.',
	'DIR_SUBMIT_TYPE_4'						=> 'Vi ste moderator, vaš vebsajt će biti dodat odmah.',
	'DIR_THUMB'								=> 'Umanjeni prikaz vebsajta',
	'DIR_USER_PROP'							=> 'Vebsajt je dodat',
	'DIR_VOTE'								=> 'Glasati',
	'DIR_VOTE_OK'							=> 'Uspešno ste glasali',
	'DIR_POST'								=> 'Post',

	'DIRECTORY_TRANSLATION_INFO'			=> 'Prevod na srpski jezik: Aleksandra KNEZEVIC',

	'RECENT_LINKS'							=> 'Poslednji dodati vebsajtovi',

	'TOO_LONG_BACK'							=> 'Adresa povratnog linka je preduga (maksimum 255 karaktera)',
	'TOO_LONG_DESCRIPTION'					=> 'Vaš opis je prekratak',
	'TOO_LONG_REPLY'						=> 'Vaš komentar je predug',
	'TOO_LONG_RSS'							=> 'URL za RSS feeds je predug',
	'TOO_LONG_SITE_NAME'					=> 'Uneto ime je predugo (maksimum 100 karaktera)',
	'TOO_LONG_URL'							=> 'Uneti URL je predug (maksimum 255 karaktera)',
	'TOO_MANY_ADDS'							=> 'Dostigli ste maksimalan broj pokušaja za unos vebsajta. Pokušajte kasnije.',
	'TOO_SHORT_BACK'						=> 'Morate uneti adresu stranice na kojoj se nalazi povratni link.',
	'TOO_SHORT_DESCRIPTION'					=> 'Morate uneti opis',
	'TOO_SHORT_REPLY'						=> 'Vaš komentar je predug',
	'TOO_SHORT_RSS'							=> 'URL za RSS feeds je prekratak',
	'TOO_SHORT_SITE_NAME'					=> 'Morate uneti ime sajta',
	'TOO_SHORT_URL'							=> 'Morate uneti adresu sajta',
	'TOO_SMALL_CAT'							=> 'Morate izabrati kategoriju',

	'WRONG_DATA_RSS'						=> 'RSS feeds mora biti validan URL, uključujući i protokol. Na primer http://www.primer.com/.',
	'WRONG_DATA_WEBSITE'					=> 'Adresa sajta mora biti validna URL, uključujući i protokol. Na primer http://www.primer.com/.',
));
