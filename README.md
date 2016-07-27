# JBZoo Utils  [![Build Status](https://travis-ci.org/JBZoo/Utils.svg?branch=master)](https://travis-ci.org/JBZoo/Utils)      [![Coverage Status](https://coveralls.io/repos/JBZoo/Utils/badge.svg?branch=master&service=github)](https://coveralls.io/github/JBZoo/Utils?branch=master)

A collection of useful PHP functions, mini classes and snippets that you need or could use every day.

[![License](https://poser.pugx.org/JBZoo/Utils/license)](https://packagist.org/packages/JBZoo/Utils)
[![Latest Stable Version](https://poser.pugx.org/JBZoo/Utils/v/stable)](https://packagist.org/packages/JBZoo/Utils) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JBZoo/Utils/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JBZoo/Utils/?branch=master)

## Install
```sh
composer require jbzoo/utils:"1.x-dev"  # Last version
composer require jbzoo/utils            # Stable version
```


## Array helper
```php
use JBZoo\Utils\Arr;

// This is faster version than the builtin array_unique()
Arr::unique($array, $keepKeys = false)

// Check is key exists with stric mode
Arr::key($key, $array)

// Check is value exists in the array
Arr::in($key, array $array)

// Returns the first element in an array.
Arr::first(array $array)

// Returns the last element in an array.
Arr::last(array $array)

// Returns the first key in an array.
Arr::firstKey(array $array)

// Returns the last key in an array.
Arr::lastKey(array $array)

// Flatten a multi-dimensional array into a one dimensional array.
Arr::flat(array $array, $preserve_keys = true)

// Searches for a given value in an array of arrays, objects and scalar values. You can optionally specify
// a field of the nested arrays and objects to search in.
Arr::search(array $array, $search, $field = false)

// Returns an array containing all the elements of arr1 after applying
// the callback function to each one.
Arr::mapDeep(array $array, $callback, $onNoScalar = false)

// Clean array by custom rule
Arr::clean($haystack)

// Clean array before serialize to JSON
Arr::cleanBeforeJson(array $array)

// Check is array is type assoc
Arr::isAssoc($array)

// Add cell to the start of assoc array
Arr::unshiftAssoc(array &$array, $key, $value)

// Get one field from array of arrays (array of objects)
Arr::getField($arrayList, $fieldName = 'id')

// Group array by key
Arr::groupByKey(array $arrayList, $key = 'id')

// Recursive array mapping
Arr::map($function, $array)

// Sort an array by keys based on another array
Arr::sortByArray(array $array, array $orderArray)

// Add some prefix to each key
Arr::addEachKey(array $array, $prefix)

// Convert assoc array to comment style
Arr::toComment(array $data)

// Wraps its argument in an array unless it is already an array
Arr::wrap($object)
```


## Command line
```php
use JBZoo\Utils\Cli;

// Is command line
Cli::check()

// Print line to std out (or echo)
Cli::out($message, $addEol = true)

// Print line to std error (or echo)
Cli::err($message, $addEol = true)

// Execute cli command with Symfony Process Component
Cli::exec($command, $args = array(), $cwd = null, $verbose = false)

// Build params for cli
Cli::build($command, $args = array())

// Returns true if STDOUT supports colorization.
Cli::hasColorSupport()

// Returns the number of columns of the terminal.
Cli::getNumberOfColumns()

// Returns if the file descriptor is an interactive terminal or not.
Cli::isInteractive($fileDescriptor = self::STDOUT)
```


## Date helper
```php
use JBZoo\Utils\Dates;

Dates::MINUTE  //         60  seconds
Dates::HOUR    //       3600  (60 * 60)
Dates::DAY     //      86400  (60 * 60 * 24)
Dates::WEEK    //     604800  (60 * 60 * 24 * 7)
Dates::MONTH   //    2592000  (60 * 60 * 24 * 30)
Dates::YEAR    //   31536000  (60 * 60 * 24 * 365)
Dates::SQL     //   Y-m-d H:i:s

// Convert to timestamp
Dates::toStamp($time, $currentIsDefault = true)

// Factory to create DateTime object from string, timestamp etc
Dates::factory($time = null, $timeZone = null)

// Return a DateTimeZone object based on the current timezone.
Dates::timezone($timezone = null)

// Check if string is date
Dates::is($date)

// Convert time for sql format
Dates::sql($time = null)

// To human readable format
Dates::human($date, $format = 'd M Y H:i')

// Returns true if date passed is within this week
Dates::isThisWeek($time)

// Returns true if date passed is within this month
Dates::isThisMonth($time)

// Returns true if date passed is within this year
Dates::isThisYear($time)

// Returns true if date passed is tomorrow
Dates::isTomorrow($time)

// Returns true if date passed is today
Dates::isToday($time)

// Returns true if date passed was yesterday
Dates::isYesterday($time)
```


## Parse and filter simple variables
```php
use JBZoo\Utils\Filter;

// Apply some filters to variable
Filter::_($value, 'slug, trim, cmd');

// Apply custom filter to variable
Filter::_($source, function ($value) {
    $value = str_replace('-', '_', $value);
    return $value;
}));

// Converts many english words that equate to true or false to boolean.
Filter::bool($string)

// Smart convert any string to float with round
Filter::float($value, $round = 10)

// Smart convert any string to int
Filter::int($value)

// Return only digits chars
Filter::digits($value)

// Return only alpha chars
Filter::alpha($value)

// Return only alpha and digits chars
Filter::alphanum($value)

// Return only chars for base64
Filter::base64($value)

// Remove whitespaces
Filter::path($value)

// Remove whitespaces
Filter::trim($value)

// Cleanup array
Filter::arr($value, $filter = null)

// Cleanup system command
Filter::cmd($value)

// Validate email
Filter::email($email)

// Get safe string
Filter::strip($string)

// Get safe string
Filter::alias($string)

//First char to upper, other to lower
Filter::ucfirst($string)

//Convert words to PHP Class name (CamelCase)
Filter::className($string)
```


## Functions for filesystem
```php
use JBZoo\Utils\FS;

// Returns the file permissions as a nice string, like -rw-r--r-- or false if the file is not found.
FS::perms($file, $perms = null)

// Removes a directory (and its contents) recursively.
FS::rmdir($dir, $traverseSymlinks = false)

// Binary safe to open file
FS::openFile($filepath)

// Quickest way for getting first file line
FS::firstLine($filepath)

// Set the writable bit on a file to the minimum value that allows the user running PHP to write to it.
FS::writable($filename, $writable = true)

// Set the readable bit on a file to the minimum value that allows the user running PHP to read to it.
FS::readable($filename, $readable = true)

// Set the executable bit on a file to the minimum value that allows the user running PHP to read to it.
FS::executable($filename, $executable = true)

// Returns size of a given directory in bytes.
FS::dirSize($dir)

// Returns all paths inside a directory.
FS::ls($dir)

// Nice formatting for computer sizes (Bytes).
FS::format($bytes, $decimals = 0)

// Get file extention
FS::ext($path)

// Get basename
FS::base($path)

// Get file name
FS::filename($path)

// Get dirname
FS::dirname($path)

// Get full real path if file or dir exists
FS::real($path)

// Function to strip additional / or \ in a path name.
FS::clean($path, $dirSep = DIRECTORY_SEPARATOR)

// Strip off the extension if it exists.
FS::stripExt($path)

// Check is current path directory (not file)
FS::isDir($path)

// Check is current path regular file (not dir)
FS::isFile($path)
```


## Some functions with HTTP headers
```php
use JBZoo\Utils\Http;

// Transmit headers that force a browser to display the download file dialog.
// Cross browser compatible. Only fires if headers have not already been sent.
Http::download($filename, $content = false)

// Sets the headers to prevent caching for the different browsers.
// Different browsers support different nocache headers, so several
// headers must be sent so that all of them get the point that no caching should occur
Http::nocache()

// Returns the IP address of the client.
Http::IP($trustProxy = false)

// Transmit UTF-8 content headers if the headers haven't already been sent.
Http::utf8($content_type = 'text/html')

// Get all HTTP headers from $_SERVER
// @see https://github.com/symfony/http-foundation/blob/master/ServerBag.php
Http::getHeaders()
```


## Image helper
```php
use JBZoo\Utils\Image;

// Check required GD library
Image::checkGD($thowException = true)

// Check is extention or mime have JPEG format
Image::isJpeg($format)

// Check is extention or mime have GIF format
Image::isGif($format)

// Check is extention or mime have PNG format
Image::isPng($format)

// Converts a hex color value to its RGB equivalent
Image::normalizeColor($origColor)

// Ensures $value is always within $min and $max range.
// If lower, $min is returned. If higher, $max is returned.
Image::range($value, $min, $max)

// Same as PHP's imagecopymerge() function, except preserves alpha-transparency in 24-bit PNGs
// @link http://www.php.net/manual/en/function.imagecopymerge.php#88456
Image::imageCopyMergeAlpha($dstImg, $srcImg, array $dist, array $src, array $srcSizes, $opacity)

// Check opacity value (0 to 100)
Image::opacity($opacity)

// Convert opacity value to alpha (0 to 127)
Image::opacity2Alpha($opacity)

// Validate color id (0 to 255)
Image::color($color)

// Validate alpha channel value (0 to 127)
Image::alpha($color)

// Validate rotate degree (-360 to 360)
Image::rotate($color)

// Validate brightness value (-255 to 255)
Image::brightness($brightness)

// Validate contrast value (-100 to 100)
Image::contrast($contrast)

// Validate colorize id (-255 to 255)
Image::colorize($colorize)

// Validate smooth force (1 to 10)
Image::smooth($smooth)

// Validate blur passes (1 to 10)
Image::blur($blur)

// Validate percent value (0 to 100)
Image::percent($percent)

// Validate image quality (0 to 100)
Image::quality($percent)

// Convert base64 to binary data
Image::strToBin($imageString)

// Check variable is a GD resource
Image::isGdRes($image)

// Validate position name
Image::position($position)

// Determine position box that contained in big box by position name and offsets
Image::getInnerCoords($position, array $canvas, array $box, array $offset)

// Add alpha chanel and blending to image resource
Image::addAlpha($image, $isBlend = true)
```


## Helper for serialized data
```php
use JBZoo\Utils\Ser;

// Check value to find if it was serialized.
// If $data is not an string, then returned value will always be false. Serialized data is always a string.
Ser::is($data)

// Serialize data, if needed.
Ser::maybe($data)

// Unserialize value only if it is serialized.
Ser::maybeUn($data)

// Unserializes partially-corrupted arrays that occur sometimes. Addresses
// specifically the `unserialize(): Error at offset xxx of yyy bytes` error.
Ser::fix($brokenSerializedData)
```


## Sluggify
```php
use JBZoo\Utils\Slug;

// Converts any accent characters to their equivalent normal characters and converts any other non-alphanumeric
// characters to dashes, then converts any sequence of two or more dashes to a single dash. This function generates
// slugs safe for use as URLs, and if you pass true as the second parameter, it will create strings safe for
// use as CSS classes or IDs.
Slug::filter($string, $separator = '-', $cssMode = false)

// Checks to see if a string is utf8 encoded.
// NOTE: This function checks for 5-Byte sequences, UTF8 has Bytes Sequences with a maximum length of 4.
Slug::seemsUTF8($string)

// Transliterates characters to their ASCII equivalents.
// Part of the URLify.php Project <https://github.com/jbroadway/urlify/>
// @see https://github.com/jbroadway/urlify/blob/master/URLify.php
Slug::downcode($text, $language = '')

// Converts all accent characters to ASCII characters.
// If there are no accent characters, then the string given is just returned.
Slug::removeAccents($string, $language = '')
```


## Functions for strings (check mbstring module)
```php
use JBZoo\Utils\Str;

// Strip all witespaces from the given string.
Str::stripSpace($string)

// Parse text by lines
Str::parseLines($text, $toAssoc = true)

// Make string safe
// - Remove UTF-8 chars
// - Remove all tags
// - Trim
Str::clean($string, $toLower = false, $addslashes = false)

// Convert >, <, ', " and & to html entities, but preserves entities that are already encoded.
Str::htmlEnt($string, $encodedEntities = false)

// Get unique string
Str::unique($prefix = 'unique')

// Generate ridable random string
Str::random($length = 6, $isReadable = true)

// Pads a given string with zeroes on the left.
Str::zeroPad($number, $length)

// Truncate a string to a specified length without cutting a word off.
Str::truncateSafe($string, $length, $append = '...')

// Truncate the string to given length of characters.
Str::limitChars($string, $limit = 100, $append = '...')

// Truncate the string to given length of words.
Str::limitWords($string, $limit = 100, $append = '...')

// Check if a given string matches a given pattern.
Str::like($pattern, $string, $caseSensitive = true)

// Converts any accent characters to their equivalent normal characters
Str::slug($text = '', $isCache = false)

// Check is mbstring oeverload standard functions
Str::isOverload()

// Check is mbstring loaded
Str::isMBString()

// Get string length (check mbstring)
Str::len($string)

// Find position of first occurrence of string in a string (check mbstring)
Str::pos($haystack, $needle, $offset = 0)

// Find position of last occurrence of a string in a string (check mbstring)
Str::rpos($haystack, $needle, $offset = 0)

// Finds position of first occurrence of a string within another, case insensitive (check mbstring)
Str::ipos($haystack, $needle, $offset = 0)

// Finds first occurrence of a string within another (check mbstring)
Str::strstr($haystack, $needle, $beforeNeedle = false)

// Finds first occurrence of a string within another, case insensitive (check mbstring)
Str::istr($haystack, $needle, $beforeNeedle = false)

// Finds the last occurrence of a character in a string within another (check mbstring)
Str::rchr($haystack, $needle, $part = null)

// Get part of string (check mbstring)
Str::sub($string, $start, $length = 0)

// Make a string lowercase (check mbstring)
Str::low($string)

// Make a string uppercase (check mbstring)
Str::up($string)

// Count the number of substring occurrences (check mbstring)
Str::subCount($haystack, $needle)

// Checks if the $haystack starts with the text in the $needle. (check mbstring)
Str::isStart($haystack, $needle, $caseSensitive = false)

// Checks if the $haystack ends with the text in the $needle. Case sensitive. (check mbstring)
Str::isEnd($haystack, $needle, $caseSensitive = false)

// Extend version to remove whitespaces
Str::trim($value)

// Escape string before save it as xml content
Str::escXml($string)

// Escape UTF-8 strings
Str::esc($string)

// Convert camel case to human readable format
Str::splitCamelCase($input, $separator = '_', $toLower = true)

// Generates a universally unique identifier (UUID v4) according to RFC 4122
Str::uuid()
```


## Some functions for system or enviroment
```php
use JBZoo\Utils\Sys;

// Check is current OS Windows
Sys::isWin()

// Check is current user ROOT
Sys::isRoot()

// Returns a home directory of current user.
Sys::getHome()

// Alias fo ini_set function
Sys::iniSet($varName, $newValue)

// Alias fo ini_get function
Sys::iniGet($varName)

// Check is function exists and callable
Sys::isFunc($funcName)

// Set PHP execution time limit
Sys::setTime($newLimit = -1)

// Set new memory limit
Sys::setMemory($newLimit = '256M')

// Check PHP version
Sys::isPHP($version, $current = PHP_VERSION)

// Get usage memory in KB format
Sys::getMemory($isPeak = true)
```


## Functions for URL's
```php
use JBZoo\Utils\Url;

// Add or remove query arguments to the URL.
Url::addArg(array $newParams, $uri = null)

// Return the current URL.
Url::current()

// Return the current path
Url::path()

// Return current root URL
Url::root()

// Build http query
Url::build(array $queryParams)

// Build a URL. The parts of the second URL will be merged into the first according to the flags argument.
Url::buildAll($url, $parts = array(), $flags = self::URL_REPLACE, &$newUrl = array())

// Checks to see if the page is being server over SSL or not
Url::isHttps($trustProxyHeaders = false)

// Removes an item or list from the query string.
Url::delArg($keys, $uri = null)

// Turns all of the links in a string into HTML links.
// Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
Url::parseLink($text)

// Convert file path to relative URL
Url::pathToRel($path)

// Convert file path to absolute URL
Url::pathToUrl($path)
```


## Variable handlers
```php
use JBZoo\Utils\Vars;

// Access an array index, retrieving the value stored there if it exists or a default if it does not.
// This function allows you to concisely access an index which may or may not exist without raising a warning.
Vars::get(&$var, $default = null)

// Return true if the number is within the min and max.
Vars::isIn($number, $min, $max)

// Is the current value even?
Vars::isEven($number)

// Is the current value negative; less than zero.
Vars::isNegative($number)

// Is the current value odd?
Vars::isOdd($number)

// Is the current value positive; greater than or equal to zero.
Vars::isPositive($number, $zero = true)

// Limits the number between two bounds.
Vars::limit($number, $min, $max)

// Increase the number to the minimum if below threshold.
Vars::min($number, $min)

// Decrease the number to the maximum if above threshold.
Vars::max($number, $max)

// Return true if the number is outside the min and max.
Vars::out($number, $min, $max)
```

## Email 
```php
use JBZoo\Utils\Email;

// Is being always validated the email. Including DNS check for MX records.

// Can be a string or an array of emails. Always return an array with valid emails.
Email::check('test@gmail.com');
Email::check(array('test@gmail.com', 'test@hotmail.com'));
Email::getDomain('test@gmail.com');
Email::getDomain(array('test@gmail.com', 'test@hotmail.com'));

// It only can be an array of emails
Email::getDomainSorted(array('test@gmail.com', 'test@hotmail.com'));
```

## Timer
```php
use JBZoo\Utils\Timer;

// Formats the elapsed time as a string.
Timer::format($time);
Timer::formatMS($time); // Only ms format

// Formats the elapsed time since the start of the request as a string.
Timer::timeSinceStart();

// Get request time
Timer::getRequestTime();
```


## Enviroment
```php
use JBZoo\Utils\Env;

// Returns an environment variable.
Env::get($name, $options = self::VAR_STRING);

// Converts the type of values like "true", "false", "null" or "123".
Env::convert($value, $options = self::VAR_STRING);

// Returns true when Xdebug is supported or the runtime used is PHPDBG (PHP >= 7.0).
Env::canCollectCodeCoverage();

// Returns the path to the binary of the current runtime. Appends ' --php' to the path when the runtime is HHVM.
Env::getBinary();

// PHP Name and version
Env::getNameWithVersion();

// Get PHP Name
Env::getName();

// Get PHP Version
Env::getVersion();

// Returns true when the runtime used is PHP and Xdebug is loaded.
Env::hasXdebug();

// Returns true when the runtime used is HHVM.
Env::isHHVM();

// Returns true when the runtime used is PHP without the PHPDBG SAPI.
Env::isPHP();

// Returns true when the runtime used is PHP with the PHPDBG SAPI.
Env::isPHPDBG();

// Returns true when the runtime used is PHP with the PHPDBG SAPI and the phpdbg_*_oplog() functions are available (PHP >= 7.0).
Env::hasPHPDBGCodeCoverage();
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
