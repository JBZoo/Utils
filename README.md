# JBZoo / Utils

[![Build Status](https://travis-ci.org/JBZoo/Utils.svg)](https://travis-ci.org/JBZoo/Utils)    [![Coverage Status](https://coveralls.io/repos/JBZoo/Utils/badge.svg)](https://coveralls.io/github/JBZoo/Utils)    [![Psalm Coverage](https://shepherd.dev/github/JBZoo/Utils/coverage.svg)](https://shepherd.dev/github/JBZoo/Utils)    [![PHP Strict Types](https://img.shields.io/badge/strict__types-%3D1-brightgreen)](https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.strict)    
[![Stable Version](https://poser.pugx.org/jbzoo/utils/version)](https://packagist.org/packages/jbzoo/utils)    [![Latest Unstable Version](https://poser.pugx.org/jbzoo/utils/v/unstable)](https://packagist.org/packages/jbzoo/utils)    [![Dependents](https://poser.pugx.org/jbzoo/utils/dependents)](https://packagist.org/packages/jbzoo/utils/dependents?order_by=downloads)    [![GitHub Issues](https://img.shields.io/github/issues/jbzoo/utils)](https://github.com/JBZoo/Utils/issues)    [![Total Downloads](https://poser.pugx.org/jbzoo/utils/downloads)](https://packagist.org/packages/jbzoo/utils/stats)    [![GitHub License](https://img.shields.io/github/license/jbzoo/utils)](https://github.com/JBZoo/Utils/blob/master/LICENSE)



Collection of PHP functions, mini classes and snippets for everyday developer's routine life.


## Install
```sh
composer require jbzoo/utils
```

## Usage

### Smart functions
```php
use function JBZoo\Utils\alias;
use function JBZoo\Utils\alpha;
use function JBZoo\Utils\alphanum;
use function JBZoo\Utils\bool;
use function JBZoo\Utils\digits;
use function JBZoo\Utils\float;
use function JBZoo\Utils\int;

int(' 10.0 ') === 10;
float(' 10.0 ') === 10.0;

bool(' yes ') === true;
bool(' no ') === false;
bool('1') === true;
bool('0') === false;

alias('Qwer ty') === 'qwer-ty';
digits('Qwer 1 ty2') === '12';
alpha('Qwer 1 ty2') === 'Qwerty';
alphanum(' #$% Qwer 1 ty2') === 'Qwer1ty2';
```


### JBZoo\Utils\Arr

```php
Arr::addEachKey(array $array, string $prefix): array; // Add some prefix to each key

Arr::clean(array $haystack): array; // Clean array by custom rule

Arr::cleanBeforeJson(array $array): array; // Clean array before serialize to JSON

Arr::first(array $array); // Returns the first element in an array.

Arr::firstKey(array $array); // Returns the first key in an array.

// Flatten a multi-dimensional array into a one dimensional array.
//                            overwrite keys from shallow nested arrays
Arr::flat(array $array, bool $preserveKeys = true): array;

Arr::getField(array $arrayList, string $fieldName = 'id'): array; // Get one field from array of arrays (array of objects)

Arr::groupByKey(array $arrayList, string $key = 'id'): array; // Group array by key

Arr::implode(string $glue, array $array): string; // Array imploding for nested array

Arr::in($value, array $array, bool $returnKey = false); // Check is value exists in the array

Arr::isAssoc(array $array): bool; // Check is array is type assoc

Arr::key($key, array $array, bool $returnValue = false); // Check if key exists

Arr::last(array $array); // Returns the last element in an array.

Arr::lastKey(array $array); // Returns the last key in an array.

Arr::map(Closure $function, array $array): array; // Recursive array mapping

// Returns an array containing all the elements of arr1 after applying
// the callback function to each one.
//                             (Objects, resources, etc)
Arr::mapDeep(array $array, callable $callback, bool $onNoScalar = false): array;

// Searches for a given value in an array of arrays, objects and scalar values. You can optionally specify
// a field of the nested arrays and objects to search in.
Arr::search(array $array, $search, ?string $field = null);

Arr::sortByArray(array $array, array $orderArray): array; // Sort an array by keys based on another array

Arr::toComment(array $data): string; // Convert assoc array to comment style

Arr::unique(array $array, bool $keepKeys = false): array; // Remove the duplicates from an array.

Arr::unshiftAssoc(array $array, $key, $value): array; // Add cell to the start of assoc array

// Wraps its argument in an array unless it is already an array
//   Arr.wrap(null)      # => []
//   Arr.wrap([1, 2, 3]) # => [1, 2, 3]
//   Arr.wrap(0)         # => [0]
Arr::wrap($object): array;

```


### JBZoo\Utils\Cli

```php
Cli::build(string $command, array $args = []): string; // Build params for cli options

Cli::check(): bool; // Is command line mode

Cli::err(string $message, bool $addEol = true): bool; // Print line to std error

Cli::exec(string $command, array $args = [], ?string $cwd = null, bool $verbose = false): string; // Execute cli commands

Cli::getNumberOfColumns(): int; // Returns the number of columns of the terminal.

// Returns true if STDOUT supports colorization.
// This code has been copied and adapted from
// Symfony\Component\Console\Output\OutputStream.
Cli::hasColorSupport(): bool;

Cli::isInteractive($fileDescriptor = 1): bool; // Returns if the file descriptor is an interactive terminal or not.

Cli::out(string $message, bool $addEol = true): bool; // Print line to std out

```


### JBZoo\Utils\Csv

```php
Csv::parse(string $csvFile, string $delimiter = ';', string $enclosure = '"', bool $hasHeader = true): array; // Simple parser for CSV files

```


### JBZoo\Utils\Dates

```php
Dates::factory($time = null, $timeZone = null): DateTime; // Build PHP \DateTime object from mixed input

Dates::formatTime(float $seconds): string; // Convert seconds to human readable format "H:i:s"

Dates::human($date, string $format = 'd M Y H:i'): string; // Convert date string ot unix timestamp to human readable date format

Dates::is(?string $date): bool; // Check if string is date

Dates::isThisMonth($time): bool; // Returns true if date passed is within this month.

Dates::isThisWeek($time): bool; // Returns true if date passed is within this week.

Dates::isThisYear($time): bool; // Returns true if date passed is within this year.

Dates::isToday($time): bool; // Returns true if date passed is today.

Dates::isTomorrow($time): bool; // Returns true if date passed is tomorrow.

Dates::isYesterday($time): bool; // Returns true if date passed was yesterday.

Dates::sql($time = null): string; // Convert time for sql format

Dates::timezone($timezone = null): DateTimeZone; // Returns a DateTimeZone object based on the current timezone.

Dates::toStamp($time = null, bool $currentIsDefault = true): int; // Convert to timestamp

```


### JBZoo\Utils\Email

```php
Email::check($emails): array; // Check if email(s) is(are) valid. You can send one or an array of emails.

// Check for DNS MX records of the email domain. Notice that a
// (temporary) DNS error will have the same result as no records
// were found. Code coverage ignored because this method requires
// DNS requests that could not be reliable.
Email::checkDns(string $email): bool;

// Get domains from email addresses. The not valid email addresses
// will be skipped.
Email::getDomain($emails): array;

Email::getDomainSorted(array $emails): array; // Get domains from email addresses in alphabetical order.

Email::getGravatarBuiltInImages(): array; // Returns gravatar supported placeholders

// Generates an Gravatar URL.
// Size of the image:
// * The default size is 32px, and it can be anywhere between 1px up to 2048px.
// * If requested any value above the allowed range, then the maximum is applied.
// * If requested any value bellow the minimum, then the default is applied.
// Default image:
// * It can be an URL to an image.
// * Or one of built in options that Gravatar has. See Email::getGravatarBuiltInImages().
// * If none is defined then a built in default is used. See Email::getGravatarBuiltInDefaultImage().
Email::getGravatarUrl(string $email, int $size = 32, string $defaultImage = 'identicon'): ?string;

Email::isValid(?string $email): bool; // Returns true if string has valid email format

Email::random(int $userNameLength = 10): string; // Create random email

```


### JBZoo\Utils\Env

```php
Env::bool(string $envVarName, bool $default = false): bool; // Convert value of environment variable to strict bool value

Env::convert(?string $value, int $options = 16); // Converts the type of values like "true", "false", "null" or "123".

Env::float(string $envVarName, float $default = 0): float; // Convert value of environment variable to strict float value

Env::get(string $envVarName, $default = null, int $options = 16); // Returns an environment variable.

Env::int(string $envVarName, int $default = 0): int; // Convert value of environment variable to strict integer value

Env::isExists(string $envVarName): bool; // Returns true if environment variable exists

Env::string(string $envVarName, string $default = ''): string; // Convert value of environment variable to clean string

```


### JBZoo\Utils\FS

```php
FS::base(?string $path): string; // Returns name of file with ext from FS pathname

FS::clean(?string $path, string $dirSep = '/'): string; // Function to strip trailing / or \ in a pathname

FS::dirName(?string $path): string; // Returns name for directory from FS pathname

FS::dirSize(string $dir): int; // Returns size of a given directory in bytes.

FS::executable(string $filename, bool $executable = true): bool; // Set the executable bit on a file to the minimum value that allows the user running PHP to read to it.

FS::ext(?string $path): string; // Returns

FS::filename(?string $path): string; // Returns filename without ext from FS pathname

FS::firstLine(string $filepath): ?string; // Quickest way for getting first file line

FS::format(int $bytes, int $decimals = 2): string; // Nice formatting for computer sizes (Bytes).

FS::getRelative(string $path, ?string $rootPath = null, string $forceDS = '/'): string; // Find relative path of file (remove root part)

FS::isDir(string $path): bool; // Check is current path directory

FS::isFile(string $path): bool; // Check is current path regular file

FS::isReal(?string $path): bool; // Returns clean realpath if file or directory exists

FS::ls(string $dir): array; // Returns all paths inside a directory.

FS::openFile(string $filepath): ?string; // Binary safe to open file

FS::perms(string $file, ?int $perms = null): string; // Returns the file permissions as a nice string, like -rw-r--r-- or false if the file is not found.

FS::readable(string $filename, bool $readable = true): bool; // Set the readable bit on a file to the minimum value that allows the user running PHP to read to it.

FS::real(?string $path): ?string; // Returns realpath (smart analog of PHP \realpath())

// Removes a directory (and its contents) recursively.
// Contributed by Askar (ARACOOL) <https://github.com/ARACOOOL>
FS::rmDir(string $dir, bool $traverseSymlinks = true): bool;

FS::stripExt(string $path): string; // Strip off the extension if it exists.

FS::writable(string $filename, bool $writable = true): bool; // Set the writable bit on a file to the minimum value that allows the user running PHP to write to it.

```


### JBZoo\Utils\Filter

```php
Filter::_($value, $filters = 'raw'); // Apply custom filter to variable

Filter::alias(string $string): string; // Get safe string

Filter::alpha(?string $value): string; // Returns only alpha chars

Filter::alphanum(?string $value): string; // Returns only alpha and digits chars

Filter::arr($value, $filter = null): array; // Cleanup array

Filter::base64(string $value): string; // Returns only chars for base64

Filter::bool($variable): bool; // Converts many english words that equate to true or false to boolean.

Filter::className(string $input): string; // Convert words to PHP Class name

Filter::clean(string $string): string; // Alias of "Str::clean($string, true, true)"

Filter::cmd(string $value): string; // Cleanup system command

Filter::data($data): JBZoo\Data\Data; // Returns JSON object from array

Filter::digits(?string $value): string; // Returns only digits chars

Filter::esc(string $string): string; // Alias of "Str::esc($string)"

Filter::float($value, int $round = 10): float; // Smart converter string to float

Filter::html(string $string): string; // Alias of "Str::htmlEnt($string)"

Filter::int($value): int; // Smart convert any string to int

Filter::low(string $string): string; // String to lower and trim

Filter::parseLines($input): array; // Parse lines to assoc list

Filter::path(string $value): string; // Remove whitespaces

Filter::raw($string); // RAW placeholder

Filter::strip(string $string): string; // Get safe string

Filter::stripQuotes(string $value): string; // Strip quotes.

Filter::stripSpace(string $string): string; // Strip spaces

Filter::trim(string $value): string; // Remove whitespaces

Filter::trimExtend(string $value): string; // Remove whitespaces

Filter::ucFirst(string $input): string; // First char to upper, other to lower

Filter::up(string $string): string; // String to upper and trim

Filter::xml(string $string): string; // Alias of "Xml::escape($string)"

```


### JBZoo\Utils\Http

```php
// Transmit headers that force a browser to display the download file dialog.
// Cross browser compatible. Only fires if headers have not already been sent.
Http::download(string $filename): bool;

Http::getHeaders(): array; // Get all HTTP headers

// Sets the headers to prevent caching for the different browsers.
// Different browsers support different nocache headers, so several
// headers must be sent so that all of them get the point that no caching should occur
Http::nocache(): bool;

Http::utf8(string $contentType = 'text/html'): bool; // Transmit UTF-8 content headers if the headers haven't already been sent.

```


### JBZoo\Utils\IP

```php
IP::getNetMask(string $ipAddress): string; // Return network mask. For example, '192.0.0.0' => '255.255.255.0'

// Returns the IP address of the client.
//                         ONLY use if your server is behind a proxy that sets these values
IP::getRemote(bool $trustProxy = false): string;

IP::v4InRange(string $ipAddress, string $range): bool; // Check if a given ip is in a network

```


### JBZoo\Utils\Image

```php
Image::addAlpha($image, bool $isBlend = true): void; // Add alpha chanel to image resource

Image::alpha(float $color): int; // Returns valid value of alpha-channel

Image::blur(float $blur): int; // Return valid value to blur image (1-10)

Image::brightness(float $brightness): int; // Returns valid value to make image bright (-255..255)

Image::checkGD(bool $throwException = true): bool; // Require GD library

Image::color(float $color): int; // Returns valid value to change color segment of a image (0..255)

Image::colorize(float $colorize): int; // Returns valid value to change color segment of a image (-255..255)

Image::contrast(float $contrast): int; // Returns valid value to change contrast of a image (-100..100)

Image::direction(string $direction): string; // Returns valid value of image direction: 'x', 'y', 'xy', 'yx'

Image::getInnerCoords(string $position, array $canvas, array $box, array $offset = []): ?array; // Determine position

Image::imageCopyMergeAlpha($dstImg, $srcImg, array $dist, array $src, array $srcSizes, int $opacity): void; // Same as PHP's imagecopymerge() function, except preserves alpha-transparency in 24-bit PNGs

Image::isGdRes($image): bool; // Check is var image GD resource

Image::isGif(?string $format = null): bool; // Checks if image has GIF format

Image::isJpeg(?string $format = null): bool; // Checks if image has JPEG/JPG format

Image::isPng(?string $format = null): bool; // Checks if image has PNG format

Image::isSupportedFormat(string $format): bool; // Check is format supported by lib

Image::isWebp(?string $format = null): bool; // Checks if image has WEBP format

// Converts a hex color value to its RGB equivalent
//                                Where red, green, blue - integers 0-255, alpha - integer 0-127
Image::normalizeColor($origColor): array;

Image::opacity(float $opacity): int; // Check opacity value

Image::opacity2Alpha(float $opacity): int; // Convert opacity value to alpha

Image::percent(float $percent): int; // Return valid value of percent (0-100)

Image::position(string $position): string; // Check position name

Image::quality(float $percent): int; // Returns valid value of image quality (0..100)

Image::rotate(float $color): int; // Returns valid value of image rotation (-360..360)

Image::smooth(float $smooth): int; // Returns valid value to change smoothness of a image (1..10)

Image::strToBin(string $imageString): string; // Convert string to binary data

```


### JBZoo\Utils\PhpDocs

```php
// Simple parse of PHPDocs.
// Example or return value
//  [
//      'description' => 'Simple parse of PHPDocs. Example or return value',
//      'params'      => [
//          'param'  => ['string $phpDoc'],
//          'return' => ['array']
//      ]
//  ]
PhpDocs::parse(string $phpDoc): array;

```


### JBZoo\Utils\Ser

```php
// UnSerializes partially-corrupted arrays that occur sometimes. Addresses
// specifically the `unserialize(): Error at offset xxx of yyy bytes` error.
// NOTE: This error can *frequently* occur with mismatched character sets and higher-than-ASCII characters.
// Contributed by Theodore R. Smith of PHP Experts, Inc. <http://www.phpexperts.pro/>
Ser::fix(string $brokenSerializedData): string;

// Check value to find if it was serialized.
// If $data is not an string, then returned value will always be false. Serialized data is always a string.
Ser::is($data): bool;

Ser::maybe($data); // Serialize data, if needed.

Ser::maybeUn(string $data); // Unserialize value only if it is serialized.

```


### JBZoo\Utils\Slug

```php
// Transliterates characters to their ASCII equivalents.
// Part of the URLify.php Project <https://github.com/jbroadway/urlify/>
Slug::downCode(string $text, string $language = ''): string;

// Converts any accent characters to their equivalent normal characters and converts any other non-alphanumeric
// characters to dashes, then converts any sequence of two or more dashes to a single dash. This function generates
// slugs safe for use as URLs, and if you pass true as the second parameter, it will create strings safe for
// use as CSS classes or IDs.
Slug::filter(?string $string, string $separator = '-', bool $cssMode = false): string;

// Converts all accent characters to ASCII characters.
// If there are no accent characters, then the string given is just returned.
Slug::removeAccents(string $string, string $language = ''): string;

// Checks to see if a string is utf8 encoded.
// NOTE: This function checks for 5-Byte sequences, UTF8 has Bytes Sequences with a maximum length of 4.
// Written by Tony Ferrara <http://blog.ircmaxwell.com>
Slug::seemsUTF8(string $string): bool;

```


### JBZoo\Utils\Stats

```php
// Generate a histogram.
// Note this is not a great function, and should not be relied upon
// for serious use.
// For a better implementation copy:
//   http://docs.scipy.org/doc/numpy-1.10.1/reference/generated/numpy.histogram.html
Stats::histogram(array $values, int $steps = 10, ?float $lowerBound = null, ?float $upperBound = null): array;

Stats::linSpace(float $min, float $max, int $num = 50, bool $endpoint = true): array; // Returns an array populated with $num numbers from $min to $max.

Stats::mean(?array $values): float; // Returns the mean (average) value of the given values.

Stats::renderAverage(array $values, int $rounding = 3): string; // Render human readable string of average value and system error

Stats::stdDev(array $values, bool $sample = false): float; // Returns the standard deviation of a given population.

Stats::variance(array $values, bool $sample = false): float; // Returns the variance for a given population.

```


### JBZoo\Utils\Str

```php
// Make string safe
// - Remove UTF-8 chars
// - Remove all tags
// - Trim
// - Add Slashes (opt)
// - To lower (opt)
Str::clean(string $string, bool $toLower = false, bool $addSlashes = false, bool $removeAccents = true): string;

Str::esc(string $string): string; // Escape UTF-8 strings

// Escape string before save it as xml content.
// The function is moved. Please, use \JBZoo\Utils\Xml::escape($string). It'll be deprecated soon.
Str::escXml(string $string): string;

Str::getClassName($object, bool $toLower = false): string; // Get class name without namespace

Str::htmlEnt(string $string, bool $encodedEntities = false): string; // Convert >, <, ', " and & to html entities, but preserves entities that are already encoded.

Str::iPos(string $haystack, string $needle, int $offset = 0): ?int; // Finds position of first occurrence of a string within another, case insensitive

Str::iStr(string $haystack, string $needle, bool $beforeNeedle = false): string; // Finds first occurrence of a string within another, case insensitive

// Increments a trailing number in a string.
// Used to easily create distinct labels when copying objects. The method has the following styles:
//  - default: "Label" becomes "Label (2)"
//  - dash:    "Label" becomes "Label-2"
Str::inc(string $string, string $style = 'default', int $next = 0): string;

Str::isEnd(string $haystack, string $needle, bool $caseSensitive = false): bool; // Checks if the $haystack ends with the text in the $needle. Case sensitive.

Str::isMBString(): bool; // Check is mbstring loaded

Str::isOverload(): bool; // Check is mbstring overload standard functions

Str::isStart(string $haystack, string $needle, bool $caseSensitive = false): bool; // Checks if the $haystack starts with the text in the $needle.

Str::len(string $string): int; // Get string length

Str::like(string $pattern, string $haystack, bool $caseSensitive = true): bool; // Check if a given string matches a given pattern.

Str::limitChars(string $string, int $limit = 100, string $append = '...'): string; // Truncate the string to given length of characters.

Str::limitWords(string $string, int $limit = 100, string $append = '...'): string; // Truncate the string to given length of words.

Str::listToDescription(array $data, bool $alignByKeys = false): ?string; // Convert array of strings to list as pretty print description

Str::low($string): string; // Make a string lowercase

Str::parseLines(string $text, bool $toAssoc = true): array; // Parse text by lines

Str::pos(string $haystack, string $needle, int $offset = 0): ?int; // Find position of first occurrence of string in a string

Str::rChr(string $haystack, string $needle, bool $part = false): string; // Finds the last occurrence of a character in a string within another

Str::rPos(string $haystack, string $needle, int $offset = 0): ?int; // Find position of last occurrence of a string in a string

Str::random(int $length = 10, bool $isReadable = true): string; // Generate readable random string

Str::slug(string $text = '', bool $isCache = false): string; // Converts any accent characters to their equivalent normal characters

Str::splitCamelCase(string $input, string $separator = '_', bool $toLower = true): string; // Convert camel case to human readable format

// Splits a string of multiple queries into an array of individual queries.
// Single line or line end comments and multi line comments are stripped off.
Str::splitSql(string $sql): array;

Str::strStr(string $haystack, string $needle, bool $beforeNeedle = false): string; // Finds first occurrence of a string within another

Str::stripSpace(string $string): string; // Strip all whitespaces from the given string.

Str::sub(string $string, int $start, int $length = 0): string; // Get part of string

Str::subCount(string $haystack, string $needle): int; // Count the number of substring occurrences

Str::testName2Human(string $input): string; // Convert test name to human readable string

Str::trim(string $value, bool $extendMode = false): string; // Trim whitespaces and other special chars

Str::truncateSafe(string $string, int $length, string $append = '...'): string; // Truncate a string to a specified length without cutting a word off.

Str::unique(string $prefix = 'unique'): string; // Get unique string

Str::up($string): string; // Make a string uppercase

// Generates a universally unique identifier (UUID v4) according to RFC 4122
// Version 4 UUIDs are pseudo-random!
// Returns Version 4 UUID format: xxxxxxxx-xxxx-4xxx-Yxxx-xxxxxxxxxxxx where x is
// any random hex digit and Y is a random choice from 8, 9, a, or b.
Str::uuid(): string;

Str::zeroPad(string $number, int $length): string; // Pads a given string with zeroes on the left.

```


### JBZoo\Utils\Sys

```php
// Returns true when Xdebug is supported or
// the runtime used is PHPDBG (PHP >= 7.0).
Sys::canCollectCodeCoverage(): bool;

// Returns the path to the binary of the current runtime.
// Appends ' --php' to the path when the runtime is HHVM.
Sys::getBinary(): string;

Sys::getDocRoot(): ?string; // Returns current document root

Sys::getHome(): ?string; // Returns a home directory of current user.

Sys::getMemory(bool $isPeak = true): string; // Get usage memory

Sys::getName(): string; // Returns type of PHP

Sys::getNameWithVersion(): string; // Return type and version of current PHP

Sys::getUserName(): ?string; // Returns current linux user who runs script

Sys::getVendorUrl(): string; // Return URL of PHP official web-site. It depends of PHP vendor.

Sys::getVersion(): ?string; // Returns current PHP version

// Returns true when the runtime used is PHP with the PHPDBG SAPI
// and the phpdbg_*_oplog() functions are available (PHP >= 7.0).
Sys::hasPHPDBGCodeCoverage(): bool;

Sys::hasXdebug(): bool; // Returns true when the runtime used is PHP and Xdebug is loaded.

Sys::iniGet(string $varName): string; // Alias fo ini_get function

Sys::iniSet(string $phpIniKey, string $newValue): bool; // Alias fo ini_set function

Sys::isFunc($funcName): bool; // Checks if function exists and callable

Sys::isHHVM(): bool; // Returns true when the runtime used is HHVM.

Sys::isPHP(string $version, string $current = '7.2.31'): bool; // Compares PHP versions

Sys::isPHPDBG(): bool; // Returns true when the runtime used is PHP with the PHPDBG SAPI.

Sys::isRealPHP(): bool; // Returns true when the runtime used is PHP without the PHPDBG SAPI.

Sys::isRoot(): bool; // Check is current user ROOT

Sys::isWin(): bool; // Check is current OS Windows

Sys::setMemory(string $newLimit = '256M'): void; // Set new memory limit

Sys::setTime(int $newLimit = 0): void; // Set PHP execution time limit (doesn't work in safe mode)

```


### JBZoo\Utils\Timer

```php
Timer::format(float $milliSeconds): string; // Formats the elapsed time as a string.

Timer::formatMS(float $seconds): string; // Formats the elapsed time as a string.

Timer::getRequestTime(): float; // Get request time

Timer::timeSinceStart(): float; // Formats the elapsed time since the start of the request as a string.

```


### JBZoo\Utils\Url

```php
Url::addArg(array $newParams, ?string $uri = null): string; // Add or remove query arguments to the URL.

Url::build(array $queryParams): string; // Builds HTTP query from array

// Build a URL. The parts of the second URL will be merged into the first according to the flags argument.
//                                or associative array like parse_url() returns
//                                would return
Url::buildAll($sourceUrl, $destParts = [], int $flags = 1, array $newUrl = []): string;

Url::create(array $parts = []): string; // Create URL from array params

Url::current(bool $addAuth = false): ?string; // Returns the current URL.

Url::delArg($keys, ?string $uri = null): string; // Removes an item or list from the query string.

Url::getAuth(): ?string; // Get current auth info

Url::isAbsolute(string $path): bool; // Is absolute url

Url::isHttps(bool $trustProxyHeaders = false): bool; // Checks to see if the page is being server over SSL or not

// Turns all of the links in a string into HTML links.
// Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
Url::parseLink(string $text): string;

Url::path(): ?string; // Returns the current path

Url::pathToRel(string $path): string; // Convert file path to relative URL

Url::pathToUrl(string $path): string; // Convert file path to absolute URL

Url::root(bool $addAuth = false): ?string; // Returns current root URL

```


### JBZoo\Utils\Vars

```php
Vars::isEven(int $number): bool; // Is the current value even?

Vars::isIn(float $number, float $min, float $max): bool; // Returns true if the number is within the min and max.

Vars::isNegative(float $number): bool; // Is the current value negative; less than zero.

Vars::isOdd(int $number): bool; // Is the current value odd?

Vars::isPositive(float $number, bool $zero = true): bool; // Is the current value positive; greater than or equal to zero.

Vars::limit(float $number, float $min, float $max): int; // Limits the number between two bounds.

Vars::max(float $number, float $max): int; // Decrease the number to the maximum if above threshold.

Vars::min(float $number, float $min): int; // Increase the number to the minimum if below threshold.

Vars::out(float $number, float $min, float $max): bool; // Returns true if the number is outside the min and max.

// Ensures $value is always within $min and $max range.
// If lower, $min is returned. If higher, $max is returned.
Vars::range(float $value, float $min, float $max): int;

Vars::relativePercent(float $normal, float $current): string; // Get relative percent

```


### JBZoo\Utils\Xml

```php
// Convert array to PHP DOMDocument object.
// Format of input array
// $source = [
//     '_node'     => '#document',
//     '_text'     => null,
//     '_cdata'    => null,
//     '_attrs'    => [],
//     '_children' => [
//         [
//             '_node'     => 'parent',
//             '_text'     => "Content of parent tag",
//             '_cdata'    => null,
//             '_attrs'    => ['parent-attribute' => 'value'],
//             '_children' => [
//                 [
//                     '_node'     => 'child',
//                     '_text'     => "Content of child tag",
//                     '_cdata'    => null,
//                     '_attrs'    => [],
//                     '_children' => [],
//                 ],
//             ]
//         ]
//     ]
// ];
// Format of output
//     <?xml version="1.0" encoding="UTF-8"?>
//     <parent parent-attribute="value">Content of parent tag<child>Content of child tag</child></parent>
Xml::array2Dom(array $xmlAsArray, ?DOMElement $domElement = null, ?DOMDocument $document = null): DOMDocument;

Xml::createFromString(?string $source = null, bool $preserveWhiteSpace = false): DOMDocument; // Create DOMDocument object from XML-string

// Convert PHP \DOMDocument or \DOMNode object to simple array
// Format of input XML (as string)
//     <?xml version="1.0" encoding="UTF-8"?>
//     <parent parent-attribute="value">Content of parent tag<child>Content of child tag</child></parent>
// Format of output array
// $result = [
//     '_node'     => '#document',
//     '_text'     => null,
//     '_cdata'    => null,
//     '_attrs'    => [],
//     '_children' => [
//         [
//             '_node'     => 'parent',
//             '_text'     => "Content of parent tag",
//             '_cdata'    => null,
//             '_attrs'    => ['parent-attribute' => 'value'],
//             '_children' => [
//                 [
//                     '_node'     => 'child',
//                     '_text'     => "Content of child tag",
//                     '_cdata'    => null,
//                     '_attrs'    => [],
//                     '_children' => [],
//                 ],
//             ]
//         ]
//     ]
// ];
Xml::dom2Array(DOMNode $element): array;

Xml::escape($rawXmlContent): string; // Escape string before save it as xml content

```



## Links (ideas and some functions)
 * utilphp - https://github.com/brandonwamboldt/utilphp
 * PHPBinString - https://github.com/Grandt/PHPBinString
 * URLify for PHP - https://github.com/jbroadway/urlify
 * LinkifyURL Project https://github.com/jmrware/LinkifyURL
 * http://www.phpexperts.pro
 * http://stackoverflow.com/a/11709412/430062
 * http://stackoverflow.com/questions/8321620/array-unique-vs-array-flip
 * http://shiflett.org/blog/2006/mar/server-name-versus-http-host
 * https://github.com/joomla-framework/string
 * Askar (ARACOOL) https://github.com/ARACOOOL
 * Sebastian Bergmann https://github.com/sebastianbergmann/php-timer
 * Sebastian Bergmann https://github.com/sebastianbergmann/environment
 * Oscar Otero https://github.com/oscarotero/env


## Unit tests and check code style
```sh
make
make test-all
```


## License

MIT


## See Also

- [CI-Report-Converter](https://github.com/JBZoo/CI-Report-Converter) - Converting different error reports for deep compatibility with popular CI systems.
- [Composer-Diff](https://github.com/JBZoo/Composer-Diff) - See what packages have changed after `composer update`.
- [Composer-Graph](https://github.com/JBZoo/Composer-Graph) - Dependency graph visualization of composer.json based on mermaid-js.
- [Mermaid-PHP](https://github.com/JBZoo/Mermaid-PHP) - Generate diagrams and flowcharts with the help of the mermaid script language.
- [Image](https://github.com/JBZoo/Image) - Package provides object-oriented way to manipulate with images as simple as possible.
- [Data](https://github.com/JBZoo/Data) - Extended implementation of ArrayObject. Use files as config/array. 
- [Retry](https://github.com/JBZoo/Retry) - Tiny PHP library providing retry/backoff functionality with multiple backoff strategies and jitter support.
- [SimpleTypes](https://github.com/JBZoo/SimpleTypes) - Converting any values and measures - money, weight, exchange rates, length, ...
