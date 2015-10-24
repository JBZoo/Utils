<?php
/**
 * JBZoo Utils
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Utils
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/Utils
 * @author    Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\Utils;

/**
 * Class Slug
 * @package JBZoo\Utils
 */
class Slug
{
    /**
     * The character map.
     * @var array
     */
    private static $_map = array();

    /**
     * The character list as a string.
     * @var string
     */
    private static $_chars = '';

    /**
     * The character list as a regular expression.
     * @var string
     */
    private static $_regex = '';

    /**
     * The current language
     * @var string
     */
    private static $_language = '';

    /**
     * @var array
     */
    public static $maps = array(

        'de'            => array( /* German */
            'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
            'ẞ' => 'SS',
        ),

        'latin'         => array(
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Ă' => 'A', 'Æ' => 'AE',
            'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
            'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'O', 'Ő' => 'O', 'Ø' => 'O', 'Ș' => 'S', 'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
            'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 'ß' => 'ss', 'à' => 'a', 'á' => 'a', 'â' => 'a',
            'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ă' => 'a', 'æ' => 'ae', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'd', 'ñ' => 'n',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 'ø' => 'o', 'ș' => 's',
            'ț' => 't', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',
        ),

        'latin_symbols' => array(
            '©' => '(c)',
        ),

        'el'            => array( /* Greek */
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
        ),

        'tr'            => array( /* Turkish */
            'ş' => 's', 'Ş' => 'S', 'ı' => 'i', 'İ' => 'I', 'ç' => 'c', 'Ç' => 'C', 'ü' => 'u', 'Ü' => 'U',
            'ö' => 'o', 'Ö' => 'O', 'ğ' => 'g', 'Ğ' => 'G',
        ),

        'bg'            => array( /* Bulgarian */
            "Щ" => 'Sht', "Ш" => 'Sh', "Ч" => 'Ch', "Ц" => 'C', "Ю" => 'Yu', "Я" => 'Ya',
            "Ж" => 'J', "А" => 'A', "Б" => 'B', "В" => 'V', "Г" => 'G', "Д" => 'D',
            "Е" => 'E', "З" => 'Z', "И" => 'I', "Й" => 'Y', "К" => 'K', "Л" => 'L',
            "М" => 'M', "Н" => 'N', "О" => 'O', "П" => 'P', "Р" => 'R', "С" => 'S',
            "Т" => 'T', "У" => 'U', "Ф" => 'F', "Х" => 'H', "Ь" => '', "Ъ" => 'A',
            "щ" => 'sht', "ш" => 'sh', "ч" => 'ch', "ц" => 'c', "ю" => 'yu', "я" => 'ya',
            "ж" => 'j', "а" => 'a', "б" => 'b', "в" => 'v', "г" => 'g', "д" => 'd',
            "е" => 'e', "з" => 'z', "и" => 'i', "й" => 'y', "к" => 'k', "л" => 'l',
            "м" => 'm', "н" => 'n', "о" => 'o', "п" => 'p', "р" => 'r', "с" => 's',
            "т" => 't', "у" => 'u', "ф" => 'f', "х" => 'h', "ь" => '', "ъ" => 'a',
        ),

        'ru'            => array( /* Russian */
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            '№' => '',
        ),

        'uk'            => array( /* Ukrainian */
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G', 'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
        ),

        'cs'            => array( /* Czech */
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z', 'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T',
            'Ů' => 'U', 'Ž' => 'Z',
        ),

        'pl'            => array( /* Polish */
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z', 'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'O', 'Ś' => 'S',
            'Ź' => 'Z', 'Ż' => 'Z',
        ),

        'ro'            => array( /* Romanian */
            'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ț' => 't', 'Ţ' => 'T', 'ţ' => 't',
        ),

        'lv'            => array( /* Latvian */
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z', 'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i',
            'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
        ),

        'lt'            => array( /* Lithuanian */
            'ą' => 'a', 'č' => 'c', 'ę' => 'e', 'ė' => 'e', 'į' => 'i', 'š' => 's', 'ų' => 'u', 'ū' => 'u', 'ž' => 'z',
            'Ą' => 'A', 'Č' => 'C', 'Ę' => 'E', 'Ė' => 'E', 'Į' => 'I', 'Š' => 'S', 'Ų' => 'U', 'Ū' => 'U', 'Ž' => 'Z',
        ),

        'vn'            => array( /* Vietnamese */
            'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Ă' => 'A', 'Ắ' => 'A', 'Ằ' => 'A', 'Ẳ' => 'A',
            'Ẵ' => 'A', 'Ặ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'Ầ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A', 'á' => 'a',
            'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a', 'É' => 'E', 'È' => 'E',
            'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E', 'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E',
            'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e',
            'ễ' => 'e', 'ệ' => 'e', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'í' => 'i', 'ì' => 'i',
            'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O', 'Ô' => 'O',
            'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O',
            'Ỡ' => 'O', 'Ợ' => 'O', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o',
            'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ợ' => 'o', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U',
            'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u',
            'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y',
            'Ỵ' => 'Y', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'Đ' => 'D', 'đ' => 'd',
        ),

        'ar'            => array( /* Arabic */
            'أ' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'g', 'ح' => 'h', 'خ' => 'kh', 'د' => 'd',
            'ذ' => 'th', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'd', 'ط' => 't',
            'ظ' => 'th', 'ع' => 'aa', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'k', 'ك' => 'k', 'ل' => 'l', 'م' => 'm',
            'ن' => 'n', 'ه' => 'h', 'و' => 'o', 'ي' => 'y',),

        'sr'            => array( /* Serbian */
            'ђ' => 'dj', 'ј' => 'j', 'љ' => 'lj', 'њ' => 'nj', 'ћ' => 'c', 'џ' => 'dz', 'đ' => 'dj',
            'Ђ' => 'Dj', 'Ј' => 'j', 'Љ' => 'Lj', 'Њ' => 'Nj', 'Ћ' => 'C', 'Џ' => 'Dz', 'Đ' => 'Dj',
        ),

        'az'            => array( /* Azerbaijani */
            'ç' => 'c', 'ə' => 'e', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
            'Ç' => 'C', 'Ə' => 'E', 'Ğ' => 'G', 'İ' => 'I', 'Ö' => 'O', 'Ş' => 'S', 'Ü' => 'U',
        ),
    );

    /**
     * Initializes the character map.
     * Part of the URLify.php Project <https://github.com/jbroadway/urlify/>
     * @see https://github.com/jbroadway/urlify/blob/master/URLify.php
     *
     * @param string $language
     */
    private static function _initLanguageMap($language = '')
    {
        if (count(self::$_map) > 0 && (($language == '') || ($language == self::$_language))) {
            return;
        }

        // Is a specific map associated with $language?

        if (Arr::key($language, self::$maps) && is_array(self::$maps[$language])) {
            // Move this map to end. This means it will have priority over others
            $langMap = self::$maps[$language];
            unset(self::$maps[$language]);
            self::$maps[$language] = $langMap;
        }

        // Reset static vars
        self::$_language = $language;
        self::$_map      = array();
        self::$_chars    = '';

        foreach (self::$maps as $map) {
            foreach ($map as $orig => $conv) {
                self::$_map[$orig] = $conv;
                self::$_chars .= $orig;
            }
        }

        self::$_regex = '/[' . self::$_chars . ']/u';
    }


    /**
     * Converts any accent characters to their equivalent normal characters and converts any other non-alphanumeric
     * characters to dashes, then converts any sequence of two or more dashes to a single dash. This function generates
     * slugs safe for use as URLs, and if you pass true as the second parameter, it will create strings safe for
     * use as CSS classes or IDs.
     *
     * @param   string  $string    A string to convert to a slug
     * @param   string  $separator The string to separate words with
     * @param   boolean $cssMode   Whether or not to generate strings safe for CSS classes/IDs (Default to false)
     * @return  string
     */
    public static function filter($string, $separator = '-', $cssMode = false)
    {
        $slug = preg_replace('/([^a-z0-9]+)/', $separator, strtolower(self::removeAccents($string)));
        $slug = trim($slug, $separator);

        if ($cssMode) {
            $digits = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');

            if (is_numeric(substr($slug, 0, 1))) {
                $slug = $digits[substr($slug, 0, 1)] . substr($slug, 1);
            }
        }

        return $slug;
    }

    /**
     * Checks to see if a string is utf8 encoded.
     * NOTE: This function checks for 5-Byte sequences, UTF8 has Bytes Sequences with a maximum length of 4.
     * Written by Tony Ferrara <http://blog.ircmaxwell.com>
     *
     * @param  string $string The string to be checked
     * @return boolean
     */
    public static function seemsUTF8($string)
    {
        if (Str::isMBString()) {
            // If mbstring is available, this is significantly faster than
            // using PHP regexps.
            return mb_check_encoding($string, Str::$encoding);
        }

        // @codeCoverageIgnoreStart
        return self::_seemsUtf8Regex($string);
        // @codeCoverageIgnoreEnd
    }

    /**
     * A non-Mbstring UTF-8 checker.
     * @link http://stackoverflow.com/a/11709412/430062
     *
     * @param $string
     * @return bool
     */
    protected static function _seemsUtf8Regex($string)
    {
        // @codeCoverageIgnoreStart
        $regex = '/(
            [\xC0-\xC1]                                                         # Invalid UTF-8 Bytes
            | [\xF5-\xFF]                                                       # Invalid UTF-8 Bytes
            | \xE0[\x80-\x9F]                                                   # Overlong encoding of prior code point
            | \xF0[\x80-\x8F]                                                   # Overlong encoding of prior code point
            | [\xC2-\xDF](?![\x80-\xBF])                                        # Invalid UTF-8 Sequence Start
            | [\xE0-\xEF](?![\x80-\xBF]{2})                                     # Invalid UTF-8 Sequence Start
            | [\xF0-\xF4](?![\x80-\xBF]{3})                                     # Invalid UTF-8 Sequence Start
            | (?<=[\x0-\x7F\xF5-\xFF])[\x80-\xBF]                               # Invalid UTF-8 Sequence Middle
            | (?<![\xC2-\xDF]|[\xE0-\xEF]|[\xE0-\xEF][\x80-\xBF]|[\xF0-\xF4]|'
            . '[\xF0-\xF4][\x80-\xBF]|[\xF0-\xF4][\x80-\xBF]{2})[\x80-\xBF]     # Overlong Sequence
            | (?<=[\xE0-\xEF])[\x80-\xBF](?![\x80-\xBF])                        # Short 3 byte sequence
            | (?<=[\xF0-\xF4])[\x80-\xBF](?![\x80-\xBF]{2})                     # Short 4 byte sequence
            | (?<=[\xF0-\xF4][\x80-\xBF])[\x80-\xBF](?![\x80-\xBF])             # Short 4 byte sequence (2)
        )/x';

        return !preg_match($regex, $string);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Transliterates characters to their ASCII equivalents.
     * Part of the URLify.php Project <https://github.com/jbroadway/urlify/>
     * @see https://github.com/jbroadway/urlify/blob/master/URLify.php
     *
     * @param  string $text     Text that might have not-ASCII characters
     * @param  string $language Specifies a priority for a specific language.
     * @return string Filtered string with replaced "nice" characters
     */
    public static function downcode($text, $language = '')
    {
        self::_initLanguageMap($language);

        if (self::seemsUTF8($text)) {
            if (preg_match_all(self::$_regex, $text, $matches)) {
                $matchesCount = count($matches[0]);
                for ($i = 0; $i < $matchesCount; $i++) {
                    $char = $matches[0][$i];

                    if (Arr::key($char, self::$_map)) {
                        $text = str_replace($char, self::$_map[$char], $text);
                    }
                }
            }

        } else {
            // Not a UTF-8 string so we assume its ISO-8859-1
            $search = "\x80\x83\x8a\x8e\x9a\x9e\x9f\xa2\xa5\xb5\xc0\xc1\xc2\xc3\xc4\xc5\xc7\xc8\xc9\xca\xcb\xcc\xcd";
            $search .= "\xce\xcf\xd1\xd2\xd3\xd4\xd5\xd6\xd8\xd9\xda\xdb\xdc\xdd\xe0\xe1\xe2\xe3\xe4\xe5\xe7\xe8\xe9";
            $search .= "\xea\xeb\xec\xed\xee\xef\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xff";
            $text = strtr($text, $search, 'EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');

            // These latin characters should be represented by two characters so
            // we can't use strtr
            $complexSearch  = array("\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe");
            $complexReplace = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $text           = str_replace($complexSearch, $complexReplace, $text);
        }

        return $text;
    }

    /**
     * Converts all accent characters to ASCII characters.
     * If there are no accent characters, then the string given is just returned.
     *
     * @param  string $string   Text that might have accent characters
     * @param  string $language Specifies a priority for a specific language.
     * @return string Filtered  string with replaced "nice" characters
     */
    public static function removeAccents($string, $language = '')
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        return self::downcode($string, $language);
    }
}
