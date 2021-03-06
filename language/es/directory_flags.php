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

$lang = array_merge($lang, array(
	'DIR_FLAG_CODE_AD' => 'Andorra',
	'DIR_FLAG_CODE_AE' => 'United Arab Emirates',
	'DIR_FLAG_CODE_AF' => 'Afghanistan',
	'DIR_FLAG_CODE_AG' => 'Antigua and Barbuda',
	'DIR_FLAG_CODE_AI' => 'Anguilla',
	'DIR_FLAG_CODE_AL' => 'Albania',
	'DIR_FLAG_CODE_AM' => 'Armenia',
	'DIR_FLAG_CODE_AN' => 'Antilles',
	'DIR_FLAG_CODE_AO' => 'Angola',
	'DIR_FLAG_CODE_AQ' => 'Antarctica',
	'DIR_FLAG_CODE_AR' => 'Argentina',
	'DIR_FLAG_CODE_AS' => 'American Samoa',
	'DIR_FLAG_CODE_AT' => 'Austria',
	'DIR_FLAG_CODE_AU' => 'Australia',
	'DIR_FLAG_CODE_AW' => 'Aruba',
	'DIR_FLAG_CODE_AX' => 'Åland',
	'DIR_FLAG_CODE_AZ' => 'Azerbaijan',
	'DIR_FLAG_CODE_BA' => 'Bosnia-Herzegovina',
	'DIR_FLAG_CODE_BB' => 'Barbados',
	'DIR_FLAG_CODE_BD' => 'Bangladesh',
	'DIR_FLAG_CODE_BE' => 'Belgium',
	'DIR_FLAG_CODE_BF' => 'Burkina faso',
	'DIR_FLAG_CODE_BG' => 'Bulgaria',
	'DIR_FLAG_CODE_BH' => 'Bahrain',
	'DIR_FLAG_CODE_BI' => 'Burundi',
	'DIR_FLAG_CODE_BJ' => 'Benin',
	'DIR_FLAG_CODE_BL' => 'St. Barthelemy',
	'DIR_FLAG_CODE_BM' => 'Bermuda',
	'DIR_FLAG_CODE_BN' => 'Brunei',
	'DIR_FLAG_CODE_BO' => 'Bolivia',
	'DIR_FLAG_CODE_BR' => 'Brazil',
	'DIR_FLAG_CODE_BS' => 'Bahamas',
	'DIR_FLAG_CODE_BT' => 'Bhutan',
	'DIR_FLAG_CODE_BV' => 'Bouvet Isle',
	'DIR_FLAG_CODE_BW' => 'Botswana',
	'DIR_FLAG_CODE_BY' => 'Belarus',
	'DIR_FLAG_CODE_BZ' => 'Belize',
	'DIR_FLAG_CODE_CA' => 'Canada',
	'DIR_FLAG_CODE_CC' => 'Cocos Isle',
	'DIR_FLAG_CODE_CD' => 'Congo, the Democratic Republic of the',
	'DIR_FLAG_CODE_CF' => 'Central African Republic',
	'DIR_FLAG_CODE_CG' => 'Congo',
	'DIR_FLAG_CODE_CH' => 'Switzerland',
	'DIR_FLAG_CODE_CI' => 'Ivory Coast',
	'DIR_FLAG_CODE_CK' => 'Cook Isle',
	'DIR_FLAG_CODE_CL' => 'Chili',
	'DIR_FLAG_CODE_CM' => 'Cameroon',
	'DIR_FLAG_CODE_CN' => 'China',
	'DIR_FLAG_CODE_CO' => 'Colombia',
	'DIR_FLAG_CODE_CR' => 'Costa Rica',
	'DIR_FLAG_CODE_CU' => 'Cuba',
	'DIR_FLAG_CODE_CV' => 'Cape Verde',
	'DIR_FLAG_CODE_CX' => 'Christmas Isle',
	'DIR_FLAG_CODE_CY' => 'Cyprus',
	'DIR_FLAG_CODE_CZ' => 'The Czech Republic',
	'DIR_FLAG_CODE_DE' => 'Germany',
	'DIR_FLAG_CODE_DJ' => 'Djibouti',
	'DIR_FLAG_CODE_DK' => 'Denmark',
	'DIR_FLAG_CODE_DM' => 'Dominica',
	'DIR_FLAG_CODE_DO' => 'Dominican Republic',
	'DIR_FLAG_CODE_DZ' => 'Algeria',
	'DIR_FLAG_CODE_EC' => 'Ecuador',
	'DIR_FLAG_CODE_EE' => 'Estonia',
	'DIR_FLAG_CODE_EG' => 'Egypt',
	'DIR_FLAG_CODE_EH' => 'Western Sahara',
	'DIR_FLAG_CODE_ER' => 'Eritrea',
	'DIR_FLAG_CODE_ES' => 'Spain',
	'DIR_FLAG_CODE_ET' => 'Ethiopia',
	'DIR_FLAG_CODE_FI' => 'Finland',
	'DIR_FLAG_CODE_FJ' => 'Fiji',
	'DIR_FLAG_CODE_FK' => 'Falklands',
	'DIR_FLAG_CODE_FM' => 'Micronesia',
	'DIR_FLAG_CODE_FO' => 'Faroe Islands',
	'DIR_FLAG_CODE_FR' => 'France',
	'DIR_FLAG_CODE_GA' => 'Gabon',
	'DIR_FLAG_CODE_GB' => 'United Kingdom',
	'DIR_FLAG_CODE_GD' => 'Grenade',
	'DIR_FLAG_CODE_GE' => 'Georgia',
	'DIR_FLAG_CODE_GF' => 'French Guiana',
	'DIR_FLAG_CODE_GG' => 'Guernesey',
	'DIR_FLAG_CODE_GH' => 'Ghana',
	'DIR_FLAG_CODE_GI' => 'Gibraltar',
	'DIR_FLAG_CODE_GL' => 'Groenland',
	'DIR_FLAG_CODE_GM' => 'Gambia',
	'DIR_FLAG_CODE_GN' => 'Guinea',
	'DIR_FLAG_CODE_GP' => 'Guadeloupe',
	'DIR_FLAG_CODE_GQ' => 'Equatorial Guinea',
	'DIR_FLAG_CODE_GR' => 'Greece',
	'DIR_FLAG_CODE_GS' => 'South Georgia and the South Sandwich Islands',
	'DIR_FLAG_CODE_GT' => 'Guatemala',
	'DIR_FLAG_CODE_GU' => 'Guam',
	'DIR_FLAG_CODE_GW' => 'Guinea-Bissau',
	'DIR_FLAG_CODE_GY' => 'Guyana',
	'DIR_FLAG_CODE_HK' => 'Hong Kong',
	'DIR_FLAG_CODE_HM' => 'Heard Island and McDonald Islands',
	'DIR_FLAG_CODE_HN' => 'Honduras',
	'DIR_FLAG_CODE_HR' => 'Croatie',
	'DIR_FLAG_CODE_HT' => 'Haïti',
	'DIR_FLAG_CODE_HU' => 'Hungary',
	'DIR_FLAG_CODE_ID' => 'Indonesia',
	'DIR_FLAG_CODE_IE' => 'Ireland',
	'DIR_FLAG_CODE_IL' => 'Israel',
	'DIR_FLAG_CODE_IM' => 'Isle of Man',
	'DIR_FLAG_CODE_IN' => 'India',
	'DIR_FLAG_CODE_IO' => 'British Territory Indian Ocean',
	'DIR_FLAG_CODE_IQ' => 'Iraq',
	'DIR_FLAG_CODE_IR' => 'Iran',
	'DIR_FLAG_CODE_IS' => 'Iceland',
	'DIR_FLAG_CODE_IT' => 'Italia',
	'DIR_FLAG_CODE_JE' => 'Jersey',
	'DIR_FLAG_CODE_JM' => 'Jamaica',
	'DIR_FLAG_CODE_JO' => 'Jordanie',
	'DIR_FLAG_CODE_JP' => 'Japan',
	'DIR_FLAG_CODE_KE' => 'Kenya',
	'DIR_FLAG_CODE_KG' => 'Kirghizistan',
	'DIR_FLAG_CODE_KH' => 'Cambodge',
	'DIR_FLAG_CODE_KI' => 'Kiribati',
	'DIR_FLAG_CODE_KM' => 'Comores',
	'DIR_FLAG_CODE_KN' => 'Saint Kitts and Nevis',
	'DIR_FLAG_CODE_KP' => 'North Korea',
	'DIR_FLAG_CODE_KR' => 'South Korea',
	'DIR_FLAG_CODE_KW' => 'Kuwait',
	'DIR_FLAG_CODE_KY' => 'Cayman Isle',
	'DIR_FLAG_CODE_KZ' => 'Kazakhstan',
	'DIR_FLAG_CODE_LA' => 'Laos',
	'DIR_FLAG_CODE_LB' => 'Lebanon',
	'DIR_FLAG_CODE_LC' => 'Saint Lucia',
	'DIR_FLAG_CODE_LI' => 'Liechtenstein',
	'DIR_FLAG_CODE_LK' => 'Sri Lanka',
	'DIR_FLAG_CODE_LR' => 'Libéria',
	'DIR_FLAG_CODE_LS' => 'Lesotho',
	'DIR_FLAG_CODE_LT' => 'Lituanie',
	'DIR_FLAG_CODE_LU' => 'Luxembourg',
	'DIR_FLAG_CODE_LV' => 'Lettonie',
	'DIR_FLAG_CODE_LY' => 'Liby',
	'DIR_FLAG_CODE_MA' => 'Morroco',
	'DIR_FLAG_CODE_MC' => 'Monaco',
	'DIR_FLAG_CODE_MD' => 'Moldovie',
	'DIR_FLAG_CODE_ME' => 'Montenegro',
	'DIR_FLAG_CODE_MF' => 'St. Martin',
	'DIR_FLAG_CODE_MG' => 'Madagascar',
	'DIR_FLAG_CODE_MH' => 'Marshallese',
	'DIR_FLAG_CODE_MK' => 'Macedonia, the former Yugoslav Republic of',
	'DIR_FLAG_CODE_ML' => 'Mali',
	'DIR_FLAG_CODE_MM' => 'Myanmar',
	'DIR_FLAG_CODE_MN' => 'Mongolie',
	'DIR_FLAG_CODE_MO' => 'Macao',
	'DIR_FLAG_CODE_MP' => 'Northern Mariana Islands',
	'DIR_FLAG_CODE_MQ' => 'Martinique',
	'DIR_FLAG_CODE_MR' => 'Mauritanie',
	'DIR_FLAG_CODE_MS' => 'Montserrat',
	'DIR_FLAG_CODE_MT' => 'Malta',
	'DIR_FLAG_CODE_MU' => 'Mauritius',
	'DIR_FLAG_CODE_MV' => 'Maldives',
	'DIR_FLAG_CODE_MW' => 'Malawi',
	'DIR_FLAG_CODE_MX' => 'Mexico',
	'DIR_FLAG_CODE_MY' => 'Malaysia',
	'DIR_FLAG_CODE_MZ' => 'Mozambique',
	'DIR_FLAG_CODE_NA' => 'Namibia',
	'DIR_FLAG_CODE_NC' => 'New Caledonia',
	'DIR_FLAG_CODE_NE' => 'Niger',
	'DIR_FLAG_CODE_NF' => 'Norfolk Island',
	'DIR_FLAG_CODE_NG' => 'Nigeria',
	'DIR_FLAG_CODE_NI' => 'Nicaragua',
	'DIR_FLAG_CODE_NL' => 'Holland',
	'DIR_FLAG_CODE_NO' => 'Norway',
	'DIR_FLAG_CODE_NP' => 'Nepal',
	'DIR_FLAG_CODE_NR' => 'Nauru',
	'DIR_FLAG_CODE_NU' => 'Niue',
	'DIR_FLAG_CODE_NZ' => 'New Zealand',
	'DIR_FLAG_CODE_OM' => 'Oman',
	'DIR_FLAG_CODE_PA' => 'Panama',
	'DIR_FLAG_CODE_PE' => 'Peru',
	'DIR_FLAG_CODE_PF' => 'French Polynesia',
	'DIR_FLAG_CODE_PG' => 'Papua New Guinea',
	'DIR_FLAG_CODE_PH' => 'Philippines',
	'DIR_FLAG_CODE_PK' => 'Pakistan',
	'DIR_FLAG_CODE_PL' => 'Poland',
	'DIR_FLAG_CODE_PM' => 'Saint Pierre and Miquelon',
	'DIR_FLAG_CODE_PN' => 'Pitcairn',
	'DIR_FLAG_CODE_PR' => 'Porto Rico',
	'DIR_FLAG_CODE_PS' => 'Palestinian Territory, Occupied',
	'DIR_FLAG_CODE_PT' => 'Portugal',
	'DIR_FLAG_CODE_PW' => 'Palau',
	'DIR_FLAG_CODE_PY' => 'Paraguay',
	'DIR_FLAG_CODE_QA' => 'Qatar',
	'DIR_FLAG_CODE_RE' => 'Reunion',
	'DIR_FLAG_CODE_RO' => 'Romania',
	'DIR_FLAG_CODE_RS' => 'Serbia',
	'DIR_FLAG_CODE_RU' => 'Russia',
	'DIR_FLAG_CODE_RW' => 'Rwanda',
	'DIR_FLAG_CODE_SA' => 'Saudi Arabia',
	'DIR_FLAG_CODE_SB' => 'Salomon',
	'DIR_FLAG_CODE_SC' => 'Seychelles',
	'DIR_FLAG_CODE_SD' => 'Sudan',
	'DIR_FLAG_CODE_SE' => 'Sweden',
	'DIR_FLAG_CODE_SG' => 'Singapore',
	'DIR_FLAG_CODE_SH' => 'Saint Helena, Ascension and Tristan da Cunha',
	'DIR_FLAG_CODE_SI' => 'Slovenia',
	'DIR_FLAG_CODE_SJ' => 'Svalbard and Jan Mayen Isle',
	'DIR_FLAG_CODE_SK' => 'Slovakia',
	'DIR_FLAG_CODE_SL' => 'Sierra Leone',
	'DIR_FLAG_CODE_SM' => 'San Marino',
	'DIR_FLAG_CODE_SN' => 'Senegal',
	'DIR_FLAG_CODE_SO' => 'Somalia',
	'DIR_FLAG_CODE_SR' => 'Suriname',
	'DIR_FLAG_CODE_ST' => 'Sao Tome and Principe',
	'DIR_FLAG_CODE_SV' => 'Salvador',
	'DIR_FLAG_CODE_SY' => 'Syria',
	'DIR_FLAG_CODE_SZ' => 'Swaziland',
	'DIR_FLAG_CODE_TC' => 'Turks and Caicos Islands',
	'DIR_FLAG_CODE_TD' => 'Tchad',
	'DIR_FLAG_CODE_TF' => 'French Southern and Antarctic Lands',
	'DIR_FLAG_CODE_TG' => 'Togo',
	'DIR_FLAG_CODE_TH' => 'Thailand',
	'DIR_FLAG_CODE_TJ' => 'Tadjikistan',
	'DIR_FLAG_CODE_TK' => 'Tokelau',
	'DIR_FLAG_CODE_TL' => 'Timor oriental',
	'DIR_FLAG_CODE_TM' => 'Turkmenistan',
	'DIR_FLAG_CODE_TN' => 'Tunisia',
	'DIR_FLAG_CODE_TO' => 'Tonga',
	'DIR_FLAG_CODE_TR' => 'Turkey',
	'DIR_FLAG_CODE_TT' => 'Trinidad and Tobago',
	'DIR_FLAG_CODE_TV' => 'Tuvalu',
	'DIR_FLAG_CODE_TW' => 'Taïwan',
	'DIR_FLAG_CODE_TZ' => 'Tanzanie',
	'DIR_FLAG_CODE_UA' => 'Ukraine',
	'DIR_FLAG_CODE_UG' => 'Ouganda',
	'DIR_FLAG_CODE_UM' => 'United States Minor Outlying Islands',
	'DIR_FLAG_CODE_US' => 'USA',
	'DIR_FLAG_CODE_UY' => 'Uruguay',
	'DIR_FLAG_CODE_UZ' => 'Uzbekistan',
	'DIR_FLAG_CODE_VA' => 'Vatican',
	'DIR_FLAG_CODE_VC' => 'St. Vincent and the Grenadines',
	'DIR_FLAG_CODE_VE' => 'Venezuela',
	'DIR_FLAG_CODE_VG' => 'British Virgin Islands',
	'DIR_FLAG_CODE_VI' => 'United States Virgin Islands',
	'DIR_FLAG_CODE_VN' => 'Vietnam',
	'DIR_FLAG_CODE_VU' => 'Vanuatu',
	'DIR_FLAG_CODE_WF' => 'Wallis and Futuna',
	'DIR_FLAG_CODE_WS' => 'Samoa',
	'DIR_FLAG_CODE_YE' => 'Yemen',
	'DIR_FLAG_CODE_YT' => 'Mayotte',
	'DIR_FLAG_CODE_ZA' => 'South Africa',
	'DIR_FLAG_CODE_ZM' => 'Zambia',
	'DIR_FLAG_CODE_ZW' => 'Zimbabwe',
));
