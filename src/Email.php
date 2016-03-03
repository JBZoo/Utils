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
     * @param string|array $emails
     * @return array
     */
    public static function check($emails)
    {
        $result = array();

        if (empty($emails)) {
            return $result;
        }

        $emails = !is_array($emails) ? array($emails) : $emails;

        foreach ($emails as $email) {
            if (self::isValid($email) === false) {
                continue;
            }
            if (!in_array($email, $result)) {
                $result[] = $email;
            }
        }

        return $result;
    }

    /**
     * Get domains from email addresses. The not valid email addresses will be skipped.
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

        $emails = !is_array($emails) ? array($emails) : $emails;

        foreach ($emails as $email) {
            if (self::isValid($email) === false) {
                continue;
            }
            $domain = self::extractDomain($email);
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
     * @param $email
     * @return bool
     */
    private static function isValid($email)
    {

        $email = filter_var($email, FILTER_SANITIZE_STRING);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        $domain = self::extractDomain($email);

        if (checkdnsrr($domain, "MX") === false) {
            return false;
        }

        return true;
    }

    /**
     * @param $email
     * @return string
     */
    private static function extractDomain($email)
    {
        $parts = explode('@', $email);
        return idn_to_ascii(array_pop($parts));
    }
}
