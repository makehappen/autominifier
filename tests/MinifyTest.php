<?php
namespace Makehappen\AutoMinifier;

use PHPUnit\Framework\TestCase;

/**
 * @covers Minify
 */
final class MinifyTest extends TestCase
{
    public function testFilesCanBeCreated()
    {
        require_once __DIR__ . '/../src/Minify.php';
        require_once __DIR__ . '/../vendor/autoload.php';

        // Instantiate auto minifier
        $objAutoMinifier = (new Minify())->setDev()->setTest();

        // test both file types
        foreach (['js', 'css'] as $strType) {
            // setup test directory
            mkdir("build/$strType");

            // generate min file
            $strFile = $objAutoMinifier->$strType();

            // get file details
            $arrFileDetails = explode('?', $strFile);

            // must be an array
            $this->assertTrue(is_array($arrFileDetails));

            // must have exactly 2 elements
            $this->assertTrue(count($arrFileDetails) == 2);

            // must match name
            $this->assertTrue($arrFileDetails[0] == "/$strType/app.min.$strType");

            // must have a cache must of md5 length size
            $this->assertTrue(strlen($arrFileDetails[1]) == 32);

            // must be found on disk
            $this->assertTrue(file_exists($objAutoMinifier->getPublicFolder() .  $arrFileDetails[0]));

            // clean up test files
            unlink($objAutoMinifier->getPublicFolder() .  $arrFileDetails[0]);
            unlink($objAutoMinifier->getPublicFolder() . '/' . $strType .  '/autominifier-cache-bust.txt');

            // remove test directory
            rmdir("build/$strType");
        }
    }
}

