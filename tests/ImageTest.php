<?php

/**
 * JBZoo Toolbox - Utils
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Utils
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Utils
 */

declare(strict_types=1);

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Image;

/**
 * Class ImageTest
 *
 * @package JBZoo\PHPUnit
 */
class ImageTest extends PHPUnit
{
    public function testCheckSystem(): void
    {
        isTrue(Image::checkGD());
    }

    public function testisJpeg(): void
    {
        isTrue(Image::isJpeg('jpeg'));
        isTrue(Image::isJpeg('JPG'));
        isTrue(Image::isJpeg('image/JPG'));
        isTrue(Image::isJpeg('image/JPeG'));

        isFalse(Image::isJpeg('png'));
        isFalse(Image::isJpeg('gif'));
    }

    public function testIsGif(): void
    {
        isTrue(Image::isGif('gif'));
        isTrue(Image::isGif('image/gif'));

        isFalse(Image::isGif('png'));
        isFalse(Image::isGif('jpeg'));
        isFalse(Image::isGif('jpg'));
    }

    public function testIsPng(): void
    {
        isTrue(Image::isPng('PnG'));
        isTrue(Image::isPng('image/PNG'));

        isFalse(Image::isPng('jpg'));
        isFalse(Image::isPng('jpeg'));
        isFalse(Image::isPng('gif'));
    }

    public function testNormalizeColor(): void
    {
        isSame(
            [0, 136, 204, 0],
            Image::normalizeColor('#0088cc')
        );

        isSame(
            [0, 136, 204, 0],
            Image::normalizeColor('0088cc')
        );

        isSame(
            [0, 136, 204, 0],
            Image::normalizeColor('08c')
        );

        isSame(
            [0, 136, 204, 0],
            Image::normalizeColor('#08c')
        );


        isSame(
            [0, 136, 204, 0],
            Image::normalizeColor(['r' => 0, 'g' => '136', 'b' => '204'])
        );

        isSame(
            [0, 136, 204, 0],
            Image::normalizeColor(['r' => '0', 'g' => '   136   ', 'b' => ' 204 '])
        );

        isSame(
            [0, 136, 204, 0],
            Image::normalizeColor(['r' => '0', 'g' => '   136   ', 'b' => ' 204 ', 'a' => '0'])
        );

        isSame(
            [0, 136, 204, 0],
            Image::normalizeColor(['r' => '0', 'g' => '   136   ', 'b' => ' 204 ', 'a' => '0'])
        );

        isSame(
            [0, 136, 204, 1],
            Image::normalizeColor(['0', '   136   ', ' 204 ', '1'])
        );

        isSame(
            [255, 255, 255, 127],
            Image::normalizeColor(['1000', '   1036   ', ' 2004 ', '1000'])
        );

        isSame(
            [0, 136, 204, 0],
            Image::normalizeColor([0, 136, 204, 0])
        );

    }

    public function testOpacity(): void
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

    public function testOpacity2Alpha(): void
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

    public function testRotate(): void
    {
        isSame(-360, Image::rotate(-700));
        isSame(0, Image::rotate(0));
        isSame(360, Image::rotate(700));
    }

    public function testBrightness(): void
    {
        isSame(-255, Image::brightness(-700));
        isSame(0, Image::brightness(0));
        isSame(255, Image::brightness(700));
    }

    public function testContrast(): void
    {
        isSame(-100, Image::contrast(-700));
        isSame(0, Image::contrast(0));
        isSame(100, Image::contrast(700));
    }

    public function testColorize(): void
    {
        isSame(-255, Image::colorize(-700));
        isSame(0, Image::colorize(0));
        isSame(255, Image::colorize(700));
    }

    public function testSmooth(): void
    {
        isSame(1, Image::smooth(0));
        isSame(10, Image::smooth(700));
    }

    public function testBlur(): void
    {
        isSame(1, Image::blur(0));
        isSame(3, Image::blur(3));
        isSame(10, Image::blur(10));
    }

    public function testDirection(): void
    {
        isSame('x', Image::direction(''));
        isSame('x', Image::direction('X'));
        isSame('y', Image::direction('Y'));
        isSame('xy', Image::direction('xy'));
        isSame('yx', Image::direction('Yx'));
    }

    public function testPercent(): void
    {
        isSame(0, Image::percent(0));
        isSame(100, Image::percent(100));
        isSame(50, Image::percent(50));
        isSame(50, Image::percent(50.5));
        isSame(0, Image::percent(-1));
        isSame(100, Image::percent(200));
    }

    public function testQuality(): void
    {
        isSame(0, Image::quality(0));
        isSame(100, Image::quality(100));
        isSame(50, Image::quality(50));
        isSame(0, Image::quality(-1));
        isSame(100, Image::quality(200));
    }

    public function testStrToBin(): void
    {
        $str = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
        $base64 = 'data:image/gif;base64,' . $str;
        $bin = base64_decode($str, true);

        isSame($bin, Image::strToBin($str));
        isSame($bin, Image::strToBin($base64));
        isSame($bin, Image::strToBin($bin));
    }

    public function testIsSupportedFormat(): void
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
    }

    public function testPositionUtils(): void
    {
        // Top Right
        isSame(Image::TOP_RIGHT, Image::position('   tr  '));
        isSame(Image::TOP_RIGHT, Image::position('RT'));
        isSame(Image::TOP_RIGHT, Image::position(' TOP RIGHT '));
        isSame(Image::TOP_RIGHT, Image::position('top right'));
        isSame(Image::TOP_RIGHT, Image::position('top-right'));
        isSame(Image::TOP_RIGHT, Image::position('top_right'));
    }

    public function testPosition(): void
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

    public function testGetInnerCoords(): void
    {
        isSame([18, 6], Image::getInnerCoords('c', [72, 36], [36, 24], [0, 0]));
        isSame([18, 0], Image::getInnerCoords('t', [72, 36], [36, 24], [0, 0]));
        isSame([36, 0], Image::getInnerCoords('tr', [72, 36], [36, 24], [0, 0]));
        isSame([36, 6], Image::getInnerCoords('r', [72, 36], [36, 24], [0, 0]));
        isSame([36, 12], Image::getInnerCoords('br', [72, 36], [36, 24], [0, 0]));
        isSame([18, 12], Image::getInnerCoords('b', [72, 36], [36, 24], [0, 0]));
        isSame([0, 12], Image::getInnerCoords('bl', [72, 36], [36, 24], [0, 0]));
        isSame([0, 6], Image::getInnerCoords('l', [72, 36], [36, 24], [0, 0]));
        isSame([0, 0], Image::getInnerCoords('tl', [72, 36], [36, 24], [0, 0]));

        isSame([19, 7], Image::getInnerCoords('с', [72, 36], [36, 24], [1, 1]));
        isSame([17, 5], Image::getInnerCoords('с', [72, 36], [36, 24], [-1, -1]));
    }

    public function testAddAlpha(): void
    {
        $imgRes = imagecreatefromgif(PROJECT_TESTS . '/resources/1x1.gif');
        Image::addAlpha($imgRes);
        Image::addAlpha($imgRes, false);
        Image::addAlpha($imgRes, true);
        isTrue(true);
    }

    public function testImageCopyMergeAlpha(): void
    {
        $image = imagecreatefromgif(PROJECT_TESTS . '/resources/1x1.gif');

        $width = imagesx($image);
        $height = imagesy($image);

        $newImage = imagecreatetruecolor($width, $height);

        // Set a White & Transparent Background Color
        $background = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $background);

        // Copy and merge
        Image::imageCopyMergeAlpha(
            $newImage,
            $image,
            [0, 0],
            [0, 0],
            [$width, $height],
            50
        );

        imagedestroy($image);
        imagedestroy($newImage);
        isTrue(true);
    }
}
