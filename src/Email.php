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
 * @author    Luís Nóbrega <luis.barros.nobrega@gmail.com>
 */

namespace JBZoo\Utils;

/**
 * Class Email
 * @package JBZoo\Utils
 */
class Email
{
    /**
     * Check if email(s) is(are) valid. You can send one or an array of emails.
     *
     * @param string|array $emails
     * @return array
     */
    public static function check($emails)
    {
        $result = array();

        if (empty($emails)) {
            return $result;
        }

        $emails = self::_handleEmailsInput($emails);

        foreach ($emails as $email) {
            if (self::_isValid($email) === false) {
                continue;
            }
            if (!in_array($email, $result)) {
                $result[] = $email;
            }
        }

        return $result;
    }

    /**
     * Check for DNS MX records of the email domain. Notice that a
     * (temporary) DNS error will have the same result as no records
     * were found.
     *
     * @param string $email
     * @return bool
     */
    public static function checkDns($email)
    {
        if (empty($email) || self::_isValid($email) === false) {
            return false;
        }

        $domain = self::_extractDomain($email);

        if (checkdnsrr($domain, "MX") === false) {
            return false;
        }

        return true;
    }

    /**
     * Get domains from email addresses. The not valid email addresses
     * will be skipped.
     *
     * @param string|array $emails
     * @return array
     */
    public static function getDomain($emails)
    {
        $result = array();

        if (empty($emails)) {
            return $result;
        }

        $emails = self::_handleEmailsInput($emails);

        foreach ($emails as $email) {
            if (self::_isValid($email) === false) {
                continue;
            }
            $domain = self::_extractDomain($email);
            if (!empty($domain) && !in_array($domain, $result)) {
                $result[] = $domain;
            }
        }

        return $result;
    }

    /**
     * Get domains from email addresses in alphabetical order.
     *
     * @param array $emails
     * @return array
     */
    public static function getDomainInAlphabeticalOrder(array $emails)
    {
        $domains = self::getDomain($emails);

        if (count($domains) < 2) {
            return $domains;
        }

        sort($domains, SORT_STRING);

        return $domains;
    }

    /**
     * @param string $email
     * @return bool
     */
    private static function _isValid($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_STRING);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        return true;
    }

    /**
     * @param string $email
     * @return string
     */
    private static function _extractDomain($email)
    {
        $parts  = explode('@', $email);
        $domain = array_pop($parts);

        if (Sys::isFunc('idn_to_ascii')) {
            return idn_to_ascii($domain);
        }

        return $domain;
    }

    /**
     * Transforms strings in array, and remove duplicates.
     * Using array_keys array_flip because is faster than array_unique:
     * array_unique O(n log n)
     * array_flip O(n)
     *
     * @link http://stackoverflow.com/questions/8321620/array-unique-vs-array-flip
     * @param string|array $emails
     * @return array
     */
    private static function _handleEmailsInput($emails)
    {
        if (is_array($emails)) {
            $result = array_keys(array_flip($emails));
        } else {
            $result = array($emails);
        }

        return $result;
    }
}
