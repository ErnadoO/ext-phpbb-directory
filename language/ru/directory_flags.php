<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
* Russian translation by HD321kbps
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

$lang = array_merge($lang, array(
	'DIR_FLAG_CODE_AD' => 'Андорра',
	'DIR_FLAG_CODE_AE' => 'Объединенные Арабские Эмираты',
	'DIR_FLAG_CODE_AF' => 'Афганистан',
	'DIR_FLAG_CODE_AG' => 'Антигуа и Барбуда',
	'DIR_FLAG_CODE_AI' => 'Ангилья',
	'DIR_FLAG_CODE_AL' => 'Албания',
	'DIR_FLAG_CODE_AM' => 'Армения',
	'DIR_FLAG_CODE_AN' => 'Антильские острова',
	'DIR_FLAG_CODE_AO' => 'Ангола',
	'DIR_FLAG_CODE_AQ' => 'Антарктика',
	'DIR_FLAG_CODE_AR' => 'Аргентина',
	'DIR_FLAG_CODE_AS' => 'Американское Самоа',
	'DIR_FLAG_CODE_AT' => 'Австрия',
	'DIR_FLAG_CODE_AU' => 'Австралия',
	'DIR_FLAG_CODE_AW' => 'Аруба',
	'DIR_FLAG_CODE_AX' => 'Аланды',
	'DIR_FLAG_CODE_AZ' => 'Азербайджан',
	'DIR_FLAG_CODE_BA' => 'Босния-Герцеговина',
	'DIR_FLAG_CODE_BB' => 'Барбадос',
	'DIR_FLAG_CODE_BD' => 'Бангладеш',
	'DIR_FLAG_CODE_BE' => 'Бельгия',
	'DIR_FLAG_CODE_BF' => 'Буркина-Фасо',
	'DIR_FLAG_CODE_BG' => 'Болгария',
	'DIR_FLAG_CODE_BH' => 'Бахрейна',
	'DIR_FLAG_CODE_BI' => 'Бурунди',
	'DIR_FLAG_CODE_BJ' => 'Бенин',
	'DIR_FLAG_CODE_BL' => 'Сен-Бартелеми ',
	'DIR_FLAG_CODE_BM' => 'Бермудские острова',
	'DIR_FLAG_CODE_BN' => 'Бруней',
	'DIR_FLAG_CODE_BO' => 'Боливия',
	'DIR_FLAG_CODE_BR' => 'Бразилия',
	'DIR_FLAG_CODE_BS' => 'Багамские острова',
	'DIR_FLAG_CODE_BT' => 'Бутан',
	'DIR_FLAG_CODE_BV' => 'Остров Буве',
	'DIR_FLAG_CODE_BW' => 'Ботсвана',
	'DIR_FLAG_CODE_BY' => 'Беларусь',
	'DIR_FLAG_CODE_BZ' => 'Белиз',
	'DIR_FLAG_CODE_CA' => 'Канада',
	'DIR_FLAG_CODE_CC' => 'Кокосовые острова',
	'DIR_FLAG_CODE_CD' => 'Конго Демократическая Республика',
	'DIR_FLAG_CODE_CF' => 'Центрально-Африканская Республика',
	'DIR_FLAG_CODE_CG' => 'Конго',
	'DIR_FLAG_CODE_CH' => 'Швейцария',
	'DIR_FLAG_CODE_CI' => 'БСК',
	'DIR_FLAG_CODE_CK' => 'Остров Кук',
	'DIR_FLAG_CODE_CL' => 'Чили',
	'DIR_FLAG_CODE_CM' => 'Камерун',
	'DIR_FLAG_CODE_CN' => 'Китай',
	'DIR_FLAG_CODE_CO' => 'Колумбия',
	'DIR_FLAG_CODE_CR' => 'Коста-Рика',
	'DIR_FLAG_CODE_CU' => 'Куба',
	'DIR_FLAG_CODE_CV' => 'Кабо-Верде',
	'DIR_FLAG_CODE_CX' => 'Остров Рождество',
	'DIR_FLAG_CODE_CY' => 'Кипр',
	'DIR_FLAG_CODE_CZ' => 'Чехия',
	'DIR_FLAG_CODE_DE' => 'Германия',
	'DIR_FLAG_CODE_DJ' => 'Джибути',
	'DIR_FLAG_CODE_DK' => 'Дания',
	'DIR_FLAG_CODE_DM' => 'Доминику',
	'DIR_FLAG_CODE_DO' => 'Доминиканская Республика',
	'DIR_FLAG_CODE_DZ' => 'Алжир',
	'DIR_FLAG_CODE_EC' => 'Эквадор',
	'DIR_FLAG_CODE_EE' => 'Эстония',
	'DIR_FLAG_CODE_EG' => 'Египет',
	'DIR_FLAG_CODE_EH' => 'Западная Сахара',
	'DIR_FLAG_CODE_ER' => 'Эритрея',
	'DIR_FLAG_CODE_ES' => 'Испания',
	'DIR_FLAG_CODE_ET' => 'Эфиопия',
	'DIR_FLAG_CODE_FI' => 'Финляндия',
	'DIR_FLAG_CODE_FJ' => 'Фиджи',
	'DIR_FLAG_CODE_FK' => 'Фолкленды',
	'DIR_FLAG_CODE_FM' => 'Микронезия',
	'DIR_FLAG_CODE_FO' => 'Фарерские острова',
	'DIR_FLAG_CODE_FR' => 'Франция',
	'DIR_FLAG_CODE_GA' => 'Габон',
	'DIR_FLAG_CODE_GB' => 'Великобритания',
	'DIR_FLAG_CODE_GD' => 'Гренада',
	'DIR_FLAG_CODE_GE' => 'Грузия',
	'DIR_FLAG_CODE_GF' => 'Французская Гвиана',
	'DIR_FLAG_CODE_GG' => 'Гарнси',
	'DIR_FLAG_CODE_GH' => 'Гана',
	'DIR_FLAG_CODE_GI' => 'Гибралтар',
	'DIR_FLAG_CODE_GL' => 'Гренландия',
	'DIR_FLAG_CODE_GM' => 'Гамбия',
	'DIR_FLAG_CODE_GN' => 'Гвинея',
	'DIR_FLAG_CODE_GP' => 'Гваделупе',
	'DIR_FLAG_CODE_GQ' => 'Экваториальная Гвинея',
	'DIR_FLAG_CODE_GR' => 'Греция',
	'DIR_FLAG_CODE_GS' => 'Южная Джорджия и Южные Сандвичевы острова',
	'DIR_FLAG_CODE_GT' => 'Гватемала',
	'DIR_FLAG_CODE_GU' => 'Гуам',
	'DIR_FLAG_CODE_GW' => 'Гвинея-Бисау',
	'DIR_FLAG_CODE_GY' => 'Гайана',
	'DIR_FLAG_CODE_HK' => 'Гонконг',
	'DIR_FLAG_CODE_HM' => 'Остров Херд и острова Макдональд',
	'DIR_FLAG_CODE_HN' => 'Гондурас',
	'DIR_FLAG_CODE_HR' => 'Хорватия',
	'DIR_FLAG_CODE_HT' => 'Гаити',
	'DIR_FLAG_CODE_HU' => 'Венгрия',
	'DIR_FLAG_CODE_ID' => 'Индонезия',
	'DIR_FLAG_CODE_IE' => 'Ирландия',
	'DIR_FLAG_CODE_IL' => 'Израиль',
	'DIR_FLAG_CODE_IM' => 'Остров Мэн',
	'DIR_FLAG_CODE_IN' => 'Индия',
	'DIR_FLAG_CODE_IO' => 'Британская территория в Индийском океане',
	'DIR_FLAG_CODE_IQ' => 'Ирак',
	'DIR_FLAG_CODE_IR' => 'Иран',
	'DIR_FLAG_CODE_IS' => 'Исландия',
	'DIR_FLAG_CODE_IT' => 'Италия',
	'DIR_FLAG_CODE_JE' => 'Остров Джерси',
	'DIR_FLAG_CODE_JM' => 'Ямайка',
	'DIR_FLAG_CODE_JO' => 'Иордания',
	'DIR_FLAG_CODE_JP' => 'Япония',
	'DIR_FLAG_CODE_KE' => 'Кения',
	'DIR_FLAG_CODE_KG' => 'Киргизстан',
	'DIR_FLAG_CODE_KH' => 'Камбоджа',
	'DIR_FLAG_CODE_KI' => 'Кирибати',
	'DIR_FLAG_CODE_KM' => 'Каморские острова',
	'DIR_FLAG_CODE_KN' => 'Сент-Китс и Невис',
	'DIR_FLAG_CODE_KP' => 'Северная Корея',
	'DIR_FLAG_CODE_KR' => 'Южная Корея',
	'DIR_FLAG_CODE_KW' => 'Кувейт',
	'DIR_FLAG_CODE_KY' => 'Каймановые острова',
	'DIR_FLAG_CODE_KZ' => 'Казахстан',
	'DIR_FLAG_CODE_LA' => 'Лаос',
	'DIR_FLAG_CODE_LB' => 'Ливан',
	'DIR_FLAG_CODE_LC' => 'Сент-Люсия',
	'DIR_FLAG_CODE_LI' => 'Лихтенштейн',
	'DIR_FLAG_CODE_LK' => 'Шри-Ланки',
	'DIR_FLAG_CODE_LR' => 'Либерия',
	'DIR_FLAG_CODE_LS' => 'Лесото',
	'DIR_FLAG_CODE_LT' => 'Литва',
	'DIR_FLAG_CODE_LU' => 'Люксембург',
	'DIR_FLAG_CODE_LV' => 'Латвия',
	'DIR_FLAG_CODE_LY' => 'Либи',
	'DIR_FLAG_CODE_MA' => 'Морроко',
	'DIR_FLAG_CODE_MC' => 'Монако',
	'DIR_FLAG_CODE_MD' => 'Молдавия',
	'DIR_FLAG_CODE_ME' => 'Черногория',
	'DIR_FLAG_CODE_MF' => 'Сен-Мартин',
	'DIR_FLAG_CODE_MG' => 'Мадагаскар',
	'DIR_FLAG_CODE_MH' => 'Маршалловые Острова',
	'DIR_FLAG_CODE_MK' => 'Македония, бывшая югославская Республика',
	'DIR_FLAG_CODE_ML' => 'Мали',
	'DIR_FLAG_CODE_MM' => 'Мьянма',
	'DIR_FLAG_CODE_MN' => 'Монголия',
	'DIR_FLAG_CODE_MO' => 'Макао',
	'DIR_FLAG_CODE_MP' => 'Северные Марианские острова',
	'DIR_FLAG_CODE_MQ' => 'Мартинике',
	'DIR_FLAG_CODE_MR' => 'Мауритания',
	'DIR_FLAG_CODE_MS' => 'Монсеррат',
	'DIR_FLAG_CODE_MT' => 'Мальта',
	'DIR_FLAG_CODE_MU' => 'Маврикий',
	'DIR_FLAG_CODE_MV' => 'Мальдивы',
	'DIR_FLAG_CODE_MW' => 'Малави',
	'DIR_FLAG_CODE_MX' => 'Мексика',
	'DIR_FLAG_CODE_MY' => 'Малайзия',
	'DIR_FLAG_CODE_MZ' => 'Мозамбик',
	'DIR_FLAG_CODE_NA' => 'Намибия',
	'DIR_FLAG_CODE_NC' => 'Новой Каледонии',
	'DIR_FLAG_CODE_NE' => 'Нигер',
	'DIR_FLAG_CODE_NF' => 'Остров Норфолк',
	'DIR_FLAG_CODE_NG' => 'Нигерии',
	'DIR_FLAG_CODE_NI' => 'Никарагуа',
	'DIR_FLAG_CODE_NL' => 'Голландия',
	'DIR_FLAG_CODE_NO' => 'Норвегия',
	'DIR_FLAG_CODE_NP' => 'Непал',
	'DIR_FLAG_CODE_NR' => 'Науру',
	'DIR_FLAG_CODE_NU' => 'Ниуэ',
	'DIR_FLAG_CODE_NZ' => 'Новая Зеландия',
	'DIR_FLAG_CODE_OM' => 'Оман',
	'DIR_FLAG_CODE_PA' => 'Панама',
	'DIR_FLAG_CODE_PE' => 'Перу',
	'DIR_FLAG_CODE_PF' => 'Французская Полинезия',
	'DIR_FLAG_CODE_PG' => 'Папуа-Новая Гвинея',
	'DIR_FLAG_CODE_PH' => 'Филиппины',
	'DIR_FLAG_CODE_PK' => 'Пакистан',
	'DIR_FLAG_CODE_PL' => 'Польша',
	'DIR_FLAG_CODE_PM' => 'Сен-Пьер и Микелон',
	'DIR_FLAG_CODE_PN' => 'Питкерн',
	'DIR_FLAG_CODE_PR' => 'Порто Рико',
	'DIR_FLAG_CODE_PS' => 'Палестинская территория',
	'DIR_FLAG_CODE_PT' => 'Португалия',
	'DIR_FLAG_CODE_PW' => 'Палау',
	'DIR_FLAG_CODE_PY' => 'Парагвай',
	'DIR_FLAG_CODE_QA' => 'Катар',
	'DIR_FLAG_CODE_RE' => 'Реюньон',
	'DIR_FLAG_CODE_RO' => 'Румыния',
	'DIR_FLAG_CODE_RS' => 'Сербия',
	'DIR_FLAG_CODE_RU' => 'Россия',
	'DIR_FLAG_CODE_RW' => 'Руанда',
	'DIR_FLAG_CODE_SA' => 'Саудовская Аравия',
	'DIR_FLAG_CODE_SB' => 'Соломоновы острова',
	'DIR_FLAG_CODE_SC' => 'Сейшелы',
	'DIR_FLAG_CODE_SD' => 'Судан',
	'DIR_FLAG_CODE_SE' => 'Швеция',
	'DIR_FLAG_CODE_SG' => 'Сингапур',
	'DIR_FLAG_CODE_SH' => 'Святой Елены, Вознесения и Тристан-да-Кунья',
	'DIR_FLAG_CODE_SI' => 'Словения',
	'DIR_FLAG_CODE_SJ' => 'Шпицберген и Ян-Майен Остров',
	'DIR_FLAG_CODE_SK' => 'Словакия',
	'DIR_FLAG_CODE_SL' => 'Республика Сьерра-Леоне',
	'DIR_FLAG_CODE_SM' => 'Сан-Марино',
	'DIR_FLAG_CODE_SN' => 'Сенегал',
	'DIR_FLAG_CODE_SO' => 'Сомали',
	'DIR_FLAG_CODE_SR' => 'Суринам',
	'DIR_FLAG_CODE_ST' => 'Сан-Томе и Принсипи',
	'DIR_FLAG_CODE_SV' => 'Сальвадор',
	'DIR_FLAG_CODE_SY' => 'Сирия',
	'DIR_FLAG_CODE_SZ' => 'Свазиленд',
	'DIR_FLAG_CODE_TC' => 'Острова Теркс и Кайкос',
	'DIR_FLAG_CODE_TD' => 'Республика Чад',
	'DIR_FLAG_CODE_TF' => 'Французские Южные и Антарктические земли',
	'DIR_FLAG_CODE_TG' => 'Тоголе́зская Респу́блика',
	'DIR_FLAG_CODE_TH' => 'Таиланд',
	'DIR_FLAG_CODE_TJ' => 'Таджикистан',
	'DIR_FLAG_CODE_TK' => 'Токелау',
	'DIR_FLAG_CODE_TL' => 'Восточный Тимор',
	'DIR_FLAG_CODE_TM' => 'Туркмения',
	'DIR_FLAG_CODE_TN' => 'Тунис',
	'DIR_FLAG_CODE_TO' => 'Тонги',
	'DIR_FLAG_CODE_TR' => 'Турция',
	'DIR_FLAG_CODE_TT' => 'Тринидад и Тобаго',
	'DIR_FLAG_CODE_TV' => 'Тувалу',
	'DIR_FLAG_CODE_TW' => 'Tайвань',
	'DIR_FLAG_CODE_TZ' => 'Танзания',
	'DIR_FLAG_CODE_UA' => 'Украина',
	'DIR_FLAG_CODE_UG' => 'Уганда',
	'DIR_FLAG_CODE_UM' => 'Внешние малые острова США',
	'DIR_FLAG_CODE_US' => 'США',
	'DIR_FLAG_CODE_UY' => 'Уругвай',
	'DIR_FLAG_CODE_UZ' => 'Узбекистан',
	'DIR_FLAG_CODE_VA' => 'Ватикан',
	'DIR_FLAG_CODE_VC' => 'Сен-Винсент и Гренадины',
	'DIR_FLAG_CODE_VE' => 'Венесуэла',
	'DIR_FLAG_CODE_VG' => 'Британские Виргинские острова',
	'DIR_FLAG_CODE_VI' => 'Виргинские острова Соединенных Штатов',
	'DIR_FLAG_CODE_VN' => 'Вьетнам',
	'DIR_FLAG_CODE_VU' => 'Вануату',
	'DIR_FLAG_CODE_WF' => 'Уоллис и Футуна',
	'DIR_FLAG_CODE_WS' => 'Самоа',
	'DIR_FLAG_CODE_YE' => 'Йемен',
	'DIR_FLAG_CODE_YT' => 'Майотта',
	'DIR_FLAG_CODE_ZA' => 'Южная Африка',
	'DIR_FLAG_CODE_ZM' => 'Замбия',
	'DIR_FLAG_CODE_ZW' => 'Зимбабве',

));
