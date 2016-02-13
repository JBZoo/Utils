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

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Image;

/**
 * Class ImageTest
 * @package JBZoo\PHPUnit
 */
class ImageTest extends PHPUnit
{
    public function testCheckSystem()
    {
        isTrue(Image::checkGD());
    }

    public function testisJpeg()
    {
        isTrue(Image::isJpeg('jpeg'));
        isTrue(Image::isJpeg('JPG'));
        isTrue(Image::isJpeg('image/JPG'));
        isTrue(Image::isJpeg('image/JPeG'));

        isFalse(Image::isJpeg('png'));
        isFalse(Image::isJpeg('gif'));
    }

    public function testIsGif()
    {
        isTrue(Image::isGif('gif'));
        isTrue(Image::isGif('image/gif'));

        isFalse(Image::isGif('png'));
        isFalse(Image::isGif('jpeg'));
        isFalse(Image::isGif('jpg'));
    }

    public function testIsPng()
    {
        isTrue(Image::isPng('PnG'));
        isTrue(Image::isPng('image/PNG'));

        isFalse(Image::isPng('jpg'));
        isFalse(Image::isPng('jpeg'));
        isFalse(Image::isPng('gif'));
    }

    public function testNormalizeColor()
    {
        isSame(
            array(0, 136, 204, 0),
            Image::normalizeColor('#0088cc')
        );

        isSame(
            array(0, 136, 204, 0),
            Image::normalizeColor('0088cc')
        );

        isSame(
            array(0, 136, 204, 0),
            Image::normalizeColor('08c')
        );

        isSame(
            array(0, 136, 204, 0),
            Image::normalizeColor('#08c')
        );


        isSame(
            array(0, 136, 204, 0),
            Image::normalizeColor(array('r' => 0, 'g' => '136', 'b' => '204'))
        );

        isSame(
            array(0, 136, 204, 0),
            Image::normalizeColor(array('r' => '0', 'g' => '   136   ', 'b' => ' 204 '))
        );

        isSame(
            array(0, 136, 204, 0),
            Image::normalizeColor(array('r' => '0', 'g' => '   136   ', 'b' => ' 204 ', 'a' => '0'))
        );

        isSame(
            array(0, 136, 204, 0),
            Image::normalizeColor(array('r' => '0', 'g' => '   136   ', 'b' => ' 204 ', 'a' => '0'))
        );

        isSame(
            array(0, 136, 204, 1),
            Image::normalizeColor(array('0', '   136   ', ' 204 ', '1'))
        );

        isSame(
            array(255, 255, 255, 127),
            Image::normalizeColor(array('1000', '   1036   ', ' 2004 ', '1000'))
        );

        isSame(
            array(0, 136, 204, 0),
            Image::normalizeColor(array(0, 136, 204, 0))
        );

    }

    public function testOpacity()
    {
        isSame(0, Image::opacity(-10));
        isSame(0, Image::opacity(0));
        isSame(1, Image::opacity(0.01));
        isSame(99, Image::opacity(0.99));
        isSame(100, Image::opacity(1));
        isSame(2, Image::opacity(2));
        isSame(10, Image::opacity(10));
        isSame(100, Image::opacity(200));

        isSame(80, Image::opacity(0.8));
        isSame(100, Image::opacity(800));
    }

    public function testOpacity2Alpha()
    {
        isSame(127, Image::opacity2Alpha(-200));
        isSame(127, Image::opacity2Alpha(-127));

        isSame(127, Image::opacity2Alpha(-50));
        isSame(127, Image::opacity2Alpha(-25));
        isSame(127, Image::opacity2Alpha(-1));
        isSame(127, Image::opacity2Alpha(-0.5));

        isSame(127, Image::opacity2Alpha(0));

        isSame(63, Image::opacity2Alpha(0.5));
        isSame(125, Image::opacity2Alpha(0.01));
        isSame(1, Image::opacity2Alpha(0.99));
        isSame(0, Image::opacity2Alpha(1));
        isSame(124, Image::opacity2Alpha(2));
        isSame(95, Image::opacity2Alpha(25));
        isSame(63, Image::opacity2Alpha(50));
        isSame(1, Image::opacity2Alpha(99));
        isSame(0, Image::opacity2Alpha(100));

        isSame(0, Image::opacity2Alpha(127));
        isSame(0, Image::opacity2Alpha(200));
    }

    public function testRotate()
    {
        isSame(-360, Image::rotate(-700));
        isSame(0, Image::rotate(0));
        isSame(360, Image::rotate(700));
    }

    public function testBrightness()
    {
        isSame(-255, Image::brightness(-700));
        isSame(0, Image::brightness(0));
        isSame(255, Image::brightness(700));
    }

    public function testContrast()
    {
        isSame(-100, Image::contrast(-700));
        isSame(0, Image::contrast(0));
        isSame(100, Image::contrast(700));
    }

    public function testColorize()
    {
        isSame(-255, Image::colorize(-700));
        isSame(0, Image::colorize(0));
        isSame(255, Image::colorize(700));
    }

    public function testSmooth()
    {
        isSame(1, Image::smooth(0));
        isSame(10, Image::smooth(700));
    }

    public function testBlur()
    {
        isSame(1, Image::blur(0));
        isSame(3, Image::blur(3));
        isSame(10, Image::blur(10));
    }

    public function testDirection()
    {
        isSame('x', Image::direction(''));
        isSame('x', Image::direction('X'));
        isSame('y', Image::direction('Y'));
        isSame('xy', Image::direction('xy'));
        isSame('yx', Image::direction('Yx'));
    }

    public function testPercent()
    {
        isSame(0, Image::percent('0'));
        isSame(100, Image::percent('100'));
        isSame(50, Image::percent('50'));
        isSame(0, Image::percent('-1'));
        isSame(100, Image::percent('200'));
    }

    public function testQuality()
    {
        isSame(0, Image::quality('0'));
        isSame(100, Image::quality('100'));
        isSame(50, Image::quality('50'));
        isSame(0, Image::quality('-1'));
        isSame(100, Image::quality('200'));
    }

    public function testStrToBin()
    {
        $str    = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
        $base64 = 'data:image/gif;base64,' . $str;
        $bin    = base64_decode($str, true);

        isSame($bin, Image::strToBin($str));
        isSame($bin, Image::strToBin($base64));
        isSame($bin, Image::strToBin($bin));
    }

    public function testIsSupportedFormat()
    {
        isTrue(Image::isSupportedFormat('png'));
        isTrue(Image::isSupportedFormat('image/png'));

        isTrue(Image::isSupportedFormat('jpg'));
        isTrue(Image::isSupportedFormat('image/jpg'));
        isTrue(Image::isSupportedFormat('jpeg'));
        isTrue(Image::isSupportedFormat('image/jpeg'));

        isTrue(Image::isSupportedFormat('gif'));
        isTrue(Image::isSupportedFormat('image/gif'));

        isFalse(Image::isSupportedFormat('bmp'));
        isFalse(Image::isSupportedFormat('image/bmp'));
        isFalse(Image::isSupportedFormat(''));
        isFalse(Image::isSupportedFormat(false));
        isFalse(Image::isSupportedFormat(null));
    }

    public function testIsGdRes()
    {
        $imgRes = imagecreatefromgif(PROJECT_TESTS . '/resources/1x1.gif');
        isTrue(Image::isGdRes($imgRes));
        isFalse(Image::isGdRes(''));
    }

    public function testPositionUtils()
    {
        // Top Right
        isSame(Image::TOP_RIGHT, Image::position('   tr  '));
        isSame(Image::TOP_RIGHT, Image::position('RT'));
        isSame(Image::TOP_RIGHT, Image::position(' TOP RIGHT '));
        isSame(Image::TOP_RIGHT, Image::position('top right'));
        isSame(Image::TOP_RIGHT, Image::position('top-right'));
        isSame(Image::TOP_RIGHT, Image::position('top_right'));
    }

    public function testPosition()
    {
        // Top
        isSame(Image::TOP, Image::position('t'));
        isSame(Image::TOP, Image::position('top'));
        isSame(Image::TOP, Image::position(Image::TOP));

        // Bottom
        isSame(Image::BOTTOM, Image::position('b'));
        isSame(Image::BOTTOM, Image::position('bottom'));
        isSame(Image::BOTTOM, Image::position(Image::BOTTOM));

        // Right
        isSame(Image::RIGHT, Image::position('r'));
        isSame(Image::RIGHT, Image::position('right'));
        isSame(Image::RIGHT, Image::position(Image::RIGHT));

        // Left
        isSame(Image::LEFT, Image::position('l'));
        isSame(Image::LEFT, Image::position('left'));
        isSame(Image::LEFT, Image::position(Image::LEFT));

        // Top Right
        isSame(Image::TOP_RIGHT, Image::position('tr'));
        isSame(Image::TOP_RIGHT, Image::position('rt'));
        isSame(Image::TOP_RIGHT, Image::position('top right'));
        isSame(Image::TOP_RIGHT, Image::position('right top'));
        isSame(Image::TOP_RIGHT, Image::position(Image::TOP_RIGHT));

        // Bottom Right
        isSame(Image::BOTTOM_RIGHT, Image::position('br'));
        isSame(Image::BOTTOM_RIGHT, Image::position('rb'));
        isSame(Image::BOTTOM_RIGHT, Image::position('bottom right'));
        isSame(Image::BOTTOM_RIGHT, Image::position('right bottom'));
        isSame(Image::BOTTOM_RIGHT, Image::position(Image::BOTTOM_RIGHT));

        // Top Left
        isSame(Image::TOP_LEFT, Image::position('tl'));
        isSame(Image::TOP_LEFT, Image::position('lt'));
        isSame(Image::TOP_LEFT, Image::position('top left'));
        isSame(Image::TOP_LEFT, Image::position('left top'));
        isSame(Image::TOP_LEFT, Image::position(Image::TOP_LEFT));

        // Bottom Left
        isSame(Image::BOTTOM_LEFT, Image::position('bl'));
        isSame(Image::BOTTOM_LEFT, Image::position('lb'));
        isSame(Image::BOTTOM_LEFT, Image::position('bottom left'));
        isSame(Image::BOTTOM_LEFT, Image::position('left bottom'));
        isSame(Image::BOTTOM_LEFT, Image::position(Image::BOTTOM_LEFT));

        // Center
        isSame(Image::CENTER, Image::position('c'));
        isSame(Image::CENTER, Image::position('center'));
        isSame(Image::CENTER, Image::position(Image::CENTER));
    }

    public function testGetInnerCoords()
    {
        isSame(array(18, 6), Image::getInnerCoords('c', array(72, 36), array(36, 24), array(0, 0)));
        isSame(array(18, 0), Image::getInnerCoords('t', array(72, 36), array(36, 24), array(0, 0)));
        isSame(array(36, 0), Image::getInnerCoords('tr', array(72, 36), array(36, 24), array(0, 0)));
        isSame(array(36, 6), Image::getInnerCoords('r', array(72, 36), array(36, 24), array(0, 0)));
        isSame(array(36, 12), Image::getInnerCoords('br', array(72, 36), array(36, 24), array(0, 0)));
        isSame(array(18, 12), Image::getInnerCoords('b', array(72, 36), array(36, 24), array(0, 0)));
        isSame(array(0, 12), Image::getInnerCoords('bl', array(72, 36), array(36, 24), array(0, 0)));
        isSame(array(0, 6), Image::getInnerCoords('l', array(72, 36), array(36, 24), array(0, 0)));
        isSame(array(0, 0), Image::getInnerCoords('tl', array(72, 36), array(36, 24), array(0, 0)));

        isSame(array(19, 7), Image::getInnerCoords('с', array(72, 36), array(36, 24), array(1, 1)));
        isSame(array(17, 5), Image::getInnerCoords('с', array(72, 36), array(36, 24), array(-1, -1)));
    }

    public function testAddAlpha()
    {
        $imgRes = imagecreatefromgif(PROJECT_TESTS . '/resources/1x1.gif');
        Image::addAlpha($imgRes);
        Image::addAlpha($imgRes, false);
        Image::addAlpha($imgRes, true);
    }

    public function testImageCopyMergeAlpha()
    {
        $image = imagecreatefromgif(PROJECT_TESTS . '/resources/1x1.gif');

        $width  = imagesx($image);
        $height = imagesy($image);

        $newImage = imagecreatetruecolor($width, $height);

        // Set a White & Transparent Background Color
        $bg = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $bg);

        // Copy and merge
        Image::imageCopyMergeAlpha(
            $newImage,
            $image,
            array(0, 0),
            array(0, 0),
            array($width, $height),
            50
        );

        imagedestroy($image);
        imagedestroy($newImage);
    }
}
