<?php
/**
 * JBZoo Utils
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Utils
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Utils
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\Utils;

/**
 * Class Email
 *
 * @package JBZoo\Utils
 */
class Email
{
    /**
     * Create random email
     *
     * @param int $length
     * @return string
     */
    public static function random($length = 10): string
    {
        return Str::random($length) . '@' . Str::random(5) . '.com';
    }

    /**
     * Check if email(s) is(are) valid. You can send one or an array of emails.
     *
     * @param string|array $emails
     * @return array
     */
    public static function check($emails): array
    {
        $result = [];

        if (empty($emails)) {
            return $result;
        }

        $emails = self::handleEmailsInput($emails);

        foreach ($emails as $email) {
            if (!self::isValid($email)) {
                continue;
            }
            if (!in_array($email, $result, true)) {
                $result[] = $email;
            }
        }

        return $result;
    }

    /**
     * Check for DNS MX records of the email domain. Notice that a
     * (temporary) DNS error will have the same result as no records
     * were found. Code coverage ignored because this method requires
     * DNS requests that could not be reliable.
     *
     * @param string $email
     * @return bool
     * @codeCoverageIgnore
     */
    public static function checkDns($email): bool
    {
        if (!self::isValid($email)) {
            return false;
        }

        $domain = self::extractDomain($email);

        return !(checkdnsrr($domain, 'MX') === false);
    }

    /**
     * Get domains from email addresses. The not valid email addresses
     * will be skipped.
     *
     * @param string|array $emails
     * @return array
     */
    public static function getDomain($emails): array
    {
        $result = [];

        if (empty($emails)) {
            return $result;
        }

        $emails = self::handleEmailsInput($emails);

        foreach ($emails as $email) {
            if (!self::isValid($email)) {
                continue;
            }

            $domain = self::extractDomain($email);
            if (!empty($domain) && !in_array($domain, $result, true)) {
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
    public static function getDomainSorted(array $emails): array
    {
        $domains = self::getDomain($emails);

        if (count($domains) < 2) {
            return $domains;
        }

        sort($domains, SORT_STRING);

        return $domains;
    }

    /**
     * Generates an Gravatar URL.
     *
     * Size of the image:
     * * The default size is 32px, and it can be anywhere between 1px up to 2048px.
     * * If requested any value above the allowed range, then the maximum is applied.
     * * If requested any value bellow the minimum, then the default is applied.
     *
     * Default image:
     * * It can be an URL to an image.
     * * Or one of built in options that Gravatar has. See Email::getGravatarBuiltInImages().
     * * If none is defined then a built in default is used. See Email::getGravatarBuiltInDefaultImage().
     *
     * @param string $email
     * @param int    $size
     * @param string $defaultImage
     * @return null|string
     * @link http://en.gravatar.com/site/implement/images/
     */
    public static function getGravatarUrl($email, $size = 32, $defaultImage = 'identicon'): ?string
    {
        if (empty($email) || self::isValid($email) === false) {
            return null;
        }

        $hash = md5(strtolower(trim($email)));

        $parts = ['scheme' => 'http', 'host' => 'www.gravatar.com'];
        if (Url::isHttps()) {
            $parts = ['scheme' => 'https', 'host' => 'secure.gravatar.com'];
        }

        // Get size
        $size = Vars::limit(Filter::int($size), 32, 2048);

        // Prepare default images
        $defaultImage = trim($defaultImage);
        if (preg_match('/^(http|https)./', $defaultImage)) {
            $defaultImage = urldecode($defaultImage);
        } else {
            $defaultImage = strtolower($defaultImage);
            if (!Arr::in((string)$defaultImage, self::getGravatarBuiltInImages())) {
                $defaultImage = self::getGravatarBuiltInDefaultImage();
            }
        }

        // Build full url
        $parts['path'] = '/avatar/' . $hash . '/';
        $parts['query'] = [
            's' => $size,
            'd' => $defaultImage,
        ];

        return Url::create($parts);
    }

    /**
     * @return array
     */
    public static function getGravatarBuiltInImages(): array
    {
        return [
            '404',
            'mm',
            'identicon',
            'monsterid',
            'wavatar',
            'retro',
            'blank',
        ];
    }

    /**
     * @return string
     */
    public static function getGravatarBuiltInDefaultImage(): string
    {
        return Arr::key(2, self::getGravatarBuiltInImages(), true);
    }

    /**
     * @param string $email
     * @return bool
     */
    public static function isValid($email): bool
    {
        if (empty($email)) {
            return false;
        }

        $email = filter_var($email, FILTER_SANITIZE_STRING);

        return !(filter_var($email, FILTER_VALIDATE_EMAIL) === false);
    }

    /**
     * @param string $email
     * @return string
     */
    private static function extractDomain($email): string
    {
        $parts = explode('@', $email);
        $domain = array_pop($parts);

        if (Sys::isFunc('idn_to_utf8')) {
            return idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46);
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
    private static function handleEmailsInput($emails): array
    {
        return is_array($emails) ? array_keys(array_flip($emails)) : [$emails];
    }
}
