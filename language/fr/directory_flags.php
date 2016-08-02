<?php
/**
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

/**
 * DO NOT CHANGE.
 */
if (!defined('IN_PHPBB')) {
    exit;
}

if (empty($lang) || !is_array($lang)) {
    $lang = [];
}

$lang = array_merge($lang, [
    'DIR_FLAG_CODE_AD' => 'Andorre',
    'DIR_FLAG_CODE_AE' => 'Émirats arabes unis',
    'DIR_FLAG_CODE_AF' => 'Afghanistan',
    'DIR_FLAG_CODE_AG' => 'Antigua-et-barbuda',
    'DIR_FLAG_CODE_AI' => 'Anguilla',
    'DIR_FLAG_CODE_AL' => 'Albanie',
    'DIR_FLAG_CODE_AM' => 'Arménie',
    'DIR_FLAG_CODE_AN' => 'Antilles Néerlandaises',
    'DIR_FLAG_CODE_AO' => 'Angola',
    'DIR_FLAG_CODE_AQ' => 'Antarctique',
    'DIR_FLAG_CODE_AR' => 'Argentine',
    'DIR_FLAG_CODE_AS' => 'Samoa américaines',
    'DIR_FLAG_CODE_AT' => 'Autriche',
    'DIR_FLAG_CODE_AU' => 'Australie',
    'DIR_FLAG_CODE_AW' => 'Aruba',
    'DIR_FLAG_CODE_AX' => 'Åland',
    'DIR_FLAG_CODE_AZ' => 'Azerbaïdjan',
    'DIR_FLAG_CODE_BA' => 'Bosnie-herzégovine',
    'DIR_FLAG_CODE_BB' => 'Barbade',
    'DIR_FLAG_CODE_BD' => 'Bangladesh',
    'DIR_FLAG_CODE_BE' => 'Belgique',
    'DIR_FLAG_CODE_BF' => 'Burkina faso',
    'DIR_FLAG_CODE_BG' => 'Bulgarie',
    'DIR_FLAG_CODE_BH' => 'Bahreïn',
    'DIR_FLAG_CODE_BI' => 'Burundi',
    'DIR_FLAG_CODE_BJ' => 'Bénin',
    'DIR_FLAG_CODE_BL' => 'Saint-Barthélemy',
    'DIR_FLAG_CODE_BM' => 'Bermudes',
    'DIR_FLAG_CODE_BN' => 'Brunéi',
    'DIR_FLAG_CODE_BO' => 'Bolivie',
    'DIR_FLAG_CODE_BR' => 'Brésil',
    'DIR_FLAG_CODE_BS' => 'Bahamas',
    'DIR_FLAG_CODE_BT' => 'Bhoutan',
    'DIR_FLAG_CODE_BV' => 'Île Bouvet',
    'DIR_FLAG_CODE_BW' => 'Botswana',
    'DIR_FLAG_CODE_BY' => 'Bélarus',
    'DIR_FLAG_CODE_BZ' => 'Belize',
    'DIR_FLAG_CODE_CA' => 'Canada',
    'DIR_FLAG_CODE_CC' => 'Îles Cocos',
    'DIR_FLAG_CODE_CD' => 'République démocratique du Congo',
    'DIR_FLAG_CODE_CF' => 'République Centrafricaine',
    'DIR_FLAG_CODE_CG' => 'Congo',
    'DIR_FLAG_CODE_CH' => 'Suisse',
    'DIR_FLAG_CODE_CI' => 'Côte d’Ivoire',
    'DIR_FLAG_CODE_CK' => 'Îles Cook',
    'DIR_FLAG_CODE_CL' => 'Chili',
    'DIR_FLAG_CODE_CM' => 'Cameroun',
    'DIR_FLAG_CODE_CN' => 'Chine',
    'DIR_FLAG_CODE_CO' => 'Colombie',
    'DIR_FLAG_CODE_CR' => 'Costa Rica',
    'DIR_FLAG_CODE_CU' => 'Cuba',
    'DIR_FLAG_CODE_CV' => 'Cap-vert',
    'DIR_FLAG_CODE_CX' => 'Île Christmas',
    'DIR_FLAG_CODE_CY' => 'Chypre',
    'DIR_FLAG_CODE_CZ' => 'République Tchèque',
    'DIR_FLAG_CODE_DE' => 'Allemagne',
    'DIR_FLAG_CODE_DJ' => 'Djibouti',
    'DIR_FLAG_CODE_DK' => 'Danemark',
    'DIR_FLAG_CODE_DM' => 'Dominique',
    'DIR_FLAG_CODE_DO' => 'République Dominicaine',
    'DIR_FLAG_CODE_DZ' => 'Algérie',
    'DIR_FLAG_CODE_EC' => 'Équateur',
    'DIR_FLAG_CODE_EE' => 'Estonie',
    'DIR_FLAG_CODE_EG' => 'Égypte',
    'DIR_FLAG_CODE_EH' => 'Sahara occidental',
    'DIR_FLAG_CODE_ER' => 'Érythrée',
    'DIR_FLAG_CODE_ES' => 'Espagne',
    'DIR_FLAG_CODE_ET' => 'Éthiopie',
    'DIR_FLAG_CODE_FI' => 'Finlande',
    'DIR_FLAG_CODE_FJ' => 'Fidji',
    'DIR_FLAG_CODE_FK' => 'Îles Malouines',
    'DIR_FLAG_CODE_FM' => 'Micronésie',
    'DIR_FLAG_CODE_FO' => 'Îles Féroé',
    'DIR_FLAG_CODE_FR' => 'France',
    'DIR_FLAG_CODE_GA' => 'Gabon',
    'DIR_FLAG_CODE_GB' => 'Royaume-uni',
    'DIR_FLAG_CODE_GD' => 'Grenade',
    'DIR_FLAG_CODE_GE' => 'Géorgie',
    'DIR_FLAG_CODE_GF' => 'Guyane Française',
    'DIR_FLAG_CODE_GG' => 'Guernesey',
    'DIR_FLAG_CODE_GH' => 'Ghana',
    'DIR_FLAG_CODE_GI' => 'Gibraltar',
    'DIR_FLAG_CODE_GL' => 'Groenland',
    'DIR_FLAG_CODE_GM' => 'Gambie',
    'DIR_FLAG_CODE_GN' => 'Guinée',
    'DIR_FLAG_CODE_GP' => 'Guadeloupe',
    'DIR_FLAG_CODE_GQ' => 'Guinée équatoriale',
    'DIR_FLAG_CODE_GR' => 'Grèce',
    'DIR_FLAG_CODE_GS' => 'Géorgie du Sud-et-les îles Sandwich du Sud',
    'DIR_FLAG_CODE_GT' => 'Guatemala',
    'DIR_FLAG_CODE_GU' => 'Guam',
    'DIR_FLAG_CODE_GW' => 'Guinée-Bissau',
    'DIR_FLAG_CODE_GY' => 'Guyana',
    'DIR_FLAG_CODE_HK' => 'Hong-Kong',
    'DIR_FLAG_CODE_HM' => 'Île Heard et îles McDonald',
    'DIR_FLAG_CODE_HN' => 'Honduras',
    'DIR_FLAG_CODE_HR' => 'Croatie',
    'DIR_FLAG_CODE_HT' => 'Haïti',
    'DIR_FLAG_CODE_HU' => 'Hongrie',
    'DIR_FLAG_CODE_ID' => 'Indonésie',
    'DIR_FLAG_CODE_IE' => 'Irlande',
    'DIR_FLAG_CODE_IL' => 'Israël',
    'DIR_FLAG_CODE_IM' => 'Île de Man',
    'DIR_FLAG_CODE_IN' => 'Inde',
    'DIR_FLAG_CODE_IO' => 'Territoire britannique de l’océan Indien',
    'DIR_FLAG_CODE_IQ' => 'Iraq',
    'DIR_FLAG_CODE_IR' => 'Iran',
    'DIR_FLAG_CODE_IS' => 'Islande',
    'DIR_FLAG_CODE_IT' => 'Italie',
    'DIR_FLAG_CODE_JE' => 'Jersey',
    'DIR_FLAG_CODE_JM' => 'Jamaïque',
    'DIR_FLAG_CODE_JO' => 'Jordanie',
    'DIR_FLAG_CODE_JP' => 'Japon',
    'DIR_FLAG_CODE_KE' => 'Kenya',
    'DIR_FLAG_CODE_KG' => 'Kirghizistan',
    'DIR_FLAG_CODE_KH' => 'Cambodge',
    'DIR_FLAG_CODE_KI' => 'Kiribati',
    'DIR_FLAG_CODE_KM' => 'Comores',
    'DIR_FLAG_CODE_KN' => 'Saint-Christophe-et-Niévès',
    'DIR_FLAG_CODE_KP' => 'Corée du nord',
    'DIR_FLAG_CODE_KR' => 'Corée du sud',
    'DIR_FLAG_CODE_KW' => 'Koweït',
    'DIR_FLAG_CODE_KY' => 'Îles Caïmans',
    'DIR_FLAG_CODE_KZ' => 'Kazakhstan',
    'DIR_FLAG_CODE_LA' => 'Laos',
    'DIR_FLAG_CODE_LB' => 'Liban',
    'DIR_FLAG_CODE_LC' => 'Sainte-Lucie',
    'DIR_FLAG_CODE_LI' => 'Liechtenstein',
    'DIR_FLAG_CODE_LK' => 'Sri Lanka',
    'DIR_FLAG_CODE_LR' => 'Libéria',
    'DIR_FLAG_CODE_LS' => 'Lesotho',
    'DIR_FLAG_CODE_LT' => 'Lituanie',
    'DIR_FLAG_CODE_LU' => 'Luxembourg',
    'DIR_FLAG_CODE_LV' => 'Lettonie',
    'DIR_FLAG_CODE_LY' => 'Liby',
    'DIR_FLAG_CODE_MA' => 'Maroc',
    'DIR_FLAG_CODE_MC' => 'Monaco',
    'DIR_FLAG_CODE_MD' => 'Moldovie',
    'DIR_FLAG_CODE_ME' => 'Monténégro',
    'DIR_FLAG_CODE_MF' => 'Saint-Martin',
    'DIR_FLAG_CODE_MG' => 'Madagascar',
    'DIR_FLAG_CODE_MH' => 'Îles Marshall',
    'DIR_FLAG_CODE_MK' => 'Macédoine',
    'DIR_FLAG_CODE_ML' => 'Mali',
    'DIR_FLAG_CODE_MM' => 'Myanmar',
    'DIR_FLAG_CODE_MN' => 'Mongolie',
    'DIR_FLAG_CODE_MO' => 'Macao',
    'DIR_FLAG_CODE_MP' => 'Îles Mariannes du Nord',
    'DIR_FLAG_CODE_MQ' => 'Martinique',
    'DIR_FLAG_CODE_MR' => 'Mauritanie',
    'DIR_FLAG_CODE_MS' => 'Montserrat',
    'DIR_FLAG_CODE_MT' => 'Malte',
    'DIR_FLAG_CODE_MU' => 'Maurice',
    'DIR_FLAG_CODE_MV' => 'Maldives',
    'DIR_FLAG_CODE_MW' => 'Malawi',
    'DIR_FLAG_CODE_MX' => 'Mexique',
    'DIR_FLAG_CODE_MY' => 'Malaisie',
    'DIR_FLAG_CODE_MZ' => 'Mozambique',
    'DIR_FLAG_CODE_NA' => 'Namibie',
    'DIR_FLAG_CODE_NC' => 'Nouvelle-Calédonie',
    'DIR_FLAG_CODE_NE' => 'Niger',
    'DIR_FLAG_CODE_NF' => 'Île Norfolk',
    'DIR_FLAG_CODE_NG' => 'Nigéria',
    'DIR_FLAG_CODE_NI' => 'Nicaragua',
    'DIR_FLAG_CODE_NL' => 'Pays-Bas',
    'DIR_FLAG_CODE_NO' => 'Norvège',
    'DIR_FLAG_CODE_NP' => 'Népal',
    'DIR_FLAG_CODE_NR' => 'Nauru',
    'DIR_FLAG_CODE_NU' => 'Niué',
    'DIR_FLAG_CODE_NZ' => 'Nouvelle-Zélande',
    'DIR_FLAG_CODE_OM' => 'Oman',
    'DIR_FLAG_CODE_PA' => 'Panama',
    'DIR_FLAG_CODE_PE' => 'Pérou',
    'DIR_FLAG_CODE_PF' => 'Polynésie Française',
    'DIR_FLAG_CODE_PG' => 'Papouasie-Nouvelle-Guinée',
    'DIR_FLAG_CODE_PH' => 'Philippines',
    'DIR_FLAG_CODE_PK' => 'Pakistan',
    'DIR_FLAG_CODE_PL' => 'Pologne',
    'DIR_FLAG_CODE_PM' => 'Saint-Pierre-et-Miquelon',
    'DIR_FLAG_CODE_PN' => 'Pitcairn',
    'DIR_FLAG_CODE_PR' => 'Porto rico',
    'DIR_FLAG_CODE_PS' => 'Palestine',
    'DIR_FLAG_CODE_PT' => 'Portugal',
    'DIR_FLAG_CODE_PW' => 'Palaos',
    'DIR_FLAG_CODE_PY' => 'Paraguay',
    'DIR_FLAG_CODE_QA' => 'Qatar',
    'DIR_FLAG_CODE_RE' => 'Réunion',
    'DIR_FLAG_CODE_RO' => 'Roumanie',
    'DIR_FLAG_CODE_RS' => 'Serbie',
    'DIR_FLAG_CODE_RU' => 'Russie',
    'DIR_FLAG_CODE_RW' => 'Rwanda',
    'DIR_FLAG_CODE_SA' => 'Arabie Saoudite',
    'DIR_FLAG_CODE_SB' => 'Salomon',
    'DIR_FLAG_CODE_SC' => 'Seychelles',
    'DIR_FLAG_CODE_SD' => 'Soudan',
    'DIR_FLAG_CODE_SE' => 'Suède',
    'DIR_FLAG_CODE_SG' => 'Singapour',
    'DIR_FLAG_CODE_SH' => 'Sainte-Hélène',
    'DIR_FLAG_CODE_SI' => 'Slovénie',
    'DIR_FLAG_CODE_SJ' => 'Svalbard et île Jan Mayen',
    'DIR_FLAG_CODE_SK' => 'Slovaquie',
    'DIR_FLAG_CODE_SL' => 'Sierra Leone',
    'DIR_FLAG_CODE_SM' => 'Saint-Marin',
    'DIR_FLAG_CODE_SN' => 'Sénégal',
    'DIR_FLAG_CODE_SO' => 'Somalie',
    'DIR_FLAG_CODE_SR' => 'Suriname',
    'DIR_FLAG_CODE_ST' => 'Sao Tomé-et-Principe',
    'DIR_FLAG_CODE_SV' => 'Salvador',
    'DIR_FLAG_CODE_SY' => 'Syrie',
    'DIR_FLAG_CODE_SZ' => 'Swaziland',
    'DIR_FLAG_CODE_TC' => 'Îles Turques-et-Caïques',
    'DIR_FLAG_CODE_TD' => 'Tchad',
    'DIR_FLAG_CODE_TF' => 'Terres Australes et Antarctiques Françaises',
    'DIR_FLAG_CODE_TG' => 'Togo',
    'DIR_FLAG_CODE_TH' => 'Thaïlande',
    'DIR_FLAG_CODE_TJ' => 'Tadjikistan',
    'DIR_FLAG_CODE_TK' => 'Tokelau',
    'DIR_FLAG_CODE_TL' => 'Timor oriental',
    'DIR_FLAG_CODE_TM' => 'Turkménistan',
    'DIR_FLAG_CODE_TN' => 'Tunisie',
    'DIR_FLAG_CODE_TO' => 'Tonga',
    'DIR_FLAG_CODE_TR' => 'Turquie',
    'DIR_FLAG_CODE_TT' => 'Trinité-et-Tobago',
    'DIR_FLAG_CODE_TV' => 'Tuvalu',
    'DIR_FLAG_CODE_TW' => 'Taïwan',
    'DIR_FLAG_CODE_TZ' => 'Tanzanie',
    'DIR_FLAG_CODE_UA' => 'Ukraine',
    'DIR_FLAG_CODE_UG' => 'Ouganda',
    'DIR_FLAG_CODE_UM' => 'Îles mineures éloignées des États-Unis',
    'DIR_FLAG_CODE_US' => 'États-unis',
    'DIR_FLAG_CODE_UY' => 'Uruguay',
    'DIR_FLAG_CODE_UZ' => 'Ouzbékistan',
    'DIR_FLAG_CODE_VA' => 'Vatican',
    'DIR_FLAG_CODE_VC' => 'Saint-Vincent-et-les Grenadines',
    'DIR_FLAG_CODE_VE' => 'Venezuela',
    'DIR_FLAG_CODE_VG' => 'Îles Vierges Britanniques',
    'DIR_FLAG_CODE_VI' => 'Îles Vierges des États-Unis',
    'DIR_FLAG_CODE_VN' => 'Viêt Nam',
    'DIR_FLAG_CODE_VU' => 'Vanuatu',
    'DIR_FLAG_CODE_WF' => 'Wallis-et-Futuna',
    'DIR_FLAG_CODE_WS' => 'Samoa',
    'DIR_FLAG_CODE_YE' => 'Yémen',
    'DIR_FLAG_CODE_YT' => 'Mayotte',
    'DIR_FLAG_CODE_ZA' => 'Afrique du sud',
    'DIR_FLAG_CODE_ZM' => 'Zambie',
    'DIR_FLAG_CODE_ZW' => 'Zimbabwe',
]);
