<?php

/**
 * JBZoo Toolbox - Utils.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Utils
 */

declare(strict_types=1);

namespace JBZoo\Utils;

final class Email
{
    /**
     * Create random email.
     */
    public static function random(int $userNameLength = 10): string
    {
        return Str::random($userNameLength) . '@' . Str::random(5) . '.com';
    }

    /**
     * Check if email(s) is(are) valid. You can send one or an array of emails.
     */
    public static function check(mixed $emails): array
    {
        $emails = (array)$emails;

        $result = [];

        if (\count($emails) === 0) {
            return $result;
        }

        $emails = self::handleEmailsInput($emails);

        foreach ($emails as $email) {
            if (!self::isValid($email)) {
                continue;
            }

            if (!\in_array($email, $result, true)) {
                $result[] = $email;
            }
        }

        return $result;
    }

    /**
     * Check for DNS MX records of the email domain.
     * Notice that a (temporary) DNS error will have the same result as no records were found.
     * Code coverage ignored because this method requires DNS requests that could not be reliable.
     */
    public static function checkDns(string $email): bool
    {
        if (!self::isValid($email)) {
            return false;
        }

        $domain = self::extractDomain($email);

        return \checkdnsrr($domain);
    }

    /**
     * Get domains from email addresses. The not valid email addresses will be skipped.
     */
    public static function getDomain(mixed $emails): array
    {
        $emails = (array)$emails;

        $result = [];

        if (\count($emails) === 0) {
            return $result;
        }

        $emails = self::handleEmailsInput($emails);

        foreach ($emails as $email) {
            if (!self::isValid($email)) {
                continue;
            }

            $domain = self::extractDomain($email);
            if (!isStrEmpty($domain) && !\in_array($domain, $result, true)) {
                $result[] = $domain;
            }
        }

        return $result;
    }

    /**
     * Get domains from email addresses in alphabetical order.
     */
    public static function getDomainSorted(array $emails): array
    {
        $domains = self::getDomain($emails);
        \sort($domains, \SORT_STRING);

        return $domains;
    }

    /**
     * Generates a Gravatar URL.
     *
     * Size of the image:
     * * The default size is 32px, and it can be anywhere between 1px up to 2048px.
     * * If requested any value above the allowed range, then the maximum is applied.
     * * If requested any value bellow the minimum, then the default is applied.
     *
     * Default image:
     * * It can be a URL to an image.
     * * Or one of built-in options that Gravatar has. See Email::getGravatarBuiltInImages().
     * * If none is defined then a built-in default is used. See Email::getGravatarBuiltInDefaultImage().
     *
     * @see http://en.gravatar.com/site/implement/images/
     */
    public static function getGravatarUrl(string $email, int $size = 32, string $defaultImage = 'identicon'): ?string
    {
        if (isStrEmpty($email) || !self::isValid($email)) {
            return null;
        }

        $hash = \md5(\strtolower(\trim($email)));

        $parts = ['scheme' => 'http', 'host' => 'www.gravatar.com'];
        if (Url::isHttps()) {
            $parts = ['scheme' => 'https', 'host' => 'secure.gravatar.com'];
        }

        // Get size
        $size = Vars::limit(Filter::int($size), 32, 2048);

        // Prepare default images
        $defaultImage = \trim($defaultImage);
        if (\preg_match('/^(http|https)./', $defaultImage) > 0) {
            $defaultImage = \urldecode($defaultImage);
        } else {
            $defaultImage = \strtolower($defaultImage);
            if (!\in_array($defaultImage, self::getGravatarBuiltInImages(), true)) {
                $defaultImage = self::getGravatarBuiltInImages()[2];
            }
        }

        // Build full url
        $parts['path']  = '/avatar/' . $hash . '/';
        $parts['query'] = ['s' => $size, 'd' => $defaultImage];

        return Url::create($parts);
    }

    /**
     * Returns gravatar supported placeholders.
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
     * Returns true if string has valid email format.
     */
    public static function isValid(?string $email): bool
    {
        if ($email === null) {
            return false;
        }

        $email = \trim($email);

        if (isStrEmpty($email)) {
            return false;
        }

        $email = \htmlspecialchars($email);

        return !(\filter_var($email, \FILTER_VALIDATE_EMAIL) === false);
    }

    private static function extractDomain(string $email): string
    {
        $parts  = \explode('@', $email);
        $domain = \array_pop($parts);

        if (Sys::isFunc('idn_to_utf8')) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            return (string)\idn_to_ascii($domain, 0, \INTL_IDNA_VARIANT_UTS46);
        }

        return $domain;
    }

    /**
     * Transforms strings in array, and remove duplicates.
     * Using array_keys array_flip because is faster than array_unique:
     * array_unique O(n log n)
     * array_flip O(n).
     *
     * @see http://stackoverflow.com/questions/8321620/array-unique-vs-array-flip
     */
    private static function handleEmailsInput(array|string $emails): array
    {
        $emails = (array)$emails;
        $emails = \array_filter($emails);

        return \array_keys(\array_flip($emails));
    }
}
