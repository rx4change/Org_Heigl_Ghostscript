<?php
/**
 * $Id$
 *
 * Copyright (c) 2008-2009 Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * This is the ant-buildfile
 *
 * @category   Org_Heigl
 * @package    Org_Heigl_Ghostscript
 * @subpackage UnitTests
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    SVN: $Revision$
 * @since      03.06.2009
 */

/** PHPUnit_Framework_TestSuite */
require_once 'PHPUnit/Framework/TestSuite.php';

/** PHPUnit_TextUI_TestRunner */
require_once 'PHPUnit/TextUI/TestRunner.php';

/**
 * This class Provides all tests of the Org_Heigl Package
 *
 * @category   Org_Heigl
 * @package    Org_Heigl_Ghostscript
 * @subpackage UnitTests
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    SVN: $Revision$
 * @since      03.06.2009
 */
class Org_Heigl_Ghostscript_Device_AllTests
{
    public static function main () {
        PHPUnit_TextUI_TestRunner::run ( self::suite () );
    }

    public static function suite () {

        $suite = new PHPUnit_Framework_TestSuite ( 'Org_Heigl_Ghostscript_Device' );

        require_once 'Org/Heigl/Ghostscript/Device/PngTest.php';
        require_once 'Org/Heigl/Ghostscript/Device/JpegTest.php';
        require_once 'Org/Heigl/Ghostscript/Device/AbstractTest.php';

        $suite->addTestSuite ( 'Org_Heigl_Ghostscript_Device_AbstractTest' );
        $suite->addTestSuite ( 'Org_Heigl_Ghostscript_Device_PngTest' );
        $suite->addTestSuite ( 'Org_Heigl_Ghostscript_Device_JpegTest' );

        return $suite;

    }
}