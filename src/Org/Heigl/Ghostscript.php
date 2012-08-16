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
 * @category  Org_Heigl
 * @package   Org_Heigl_Ghostscript
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   SVN: $Revision$
 * @since     03.06.2009
 */

/**
 * This class contains a wrapper around the Ghostscript-Application.
 *
 * This needs the Ghostscript application to be installed on the server. If the
 * gs-executable is not available the class will not be able to execute anything
 *
 * A working example might look like the following code:
 * <code>
 *
 * // First we describe the output-format
 * $device = new Org_Heigl_Ghostscript_Device_Jpeg ();
 *
 * // Set the JPEG-Quality to 100
 * $device -> setQuality ( 100 );
 *
 * // Next we Create the ghostscript-Wrapper
 * $gs = new Org_Heigl_Ghostscript ();
 *
 * // Set the device
 * $gs -> setDevice ( $device )
 * // Set the input file
 *     -> setInputFile ( 'path/to/my/ps/or/pdf/file' )
 * // Set the output file that will be created in the same directory as the input
 *     -> setOutputFile ( 'output' )
 * // Set the resolution to 96 pixel per inch
 *     -> setResolution ( 96 )
 * // Set Text-antialiasing to the highest level
 *     -> setTextAntiAliasing ( Org_Heigl_Ghostscript::ANTIALIASING_HIGH );
 *
 * // convert the input file to an image
 * if ( true === $gs -> render () ) {
 *     echo 'success';
 * } else {
 *     echo 'some error occured';
 * }
 * </code>
 *
 * Alternatively the example could read as follows
 * <code>
 *
 * // Create the ghostscript-Wrapper
 * $gs = new Org_Heigl_Ghostscript ();
 *
 * // Set the device
 * $gs -> setDevice ( 'jpeg' )
 * // Set the input file
 *     -> setInputFile ( 'path/to/my/ps/or/pdf/file' )
 * // Set the output file that will be created in the same directory as the input
 *     -> setOutputFile ( 'output' )
 * // Set the resolution to 96 pixel per inch
 *     -> setResolution ( 96 )
 * // Set Text-antialiasing to the highest level
 *     -> setTextAntiAliasing ( Org_Heigl_Ghostscript::ANTIALIASING_HIGH );
 *
 * // Set the jpeg-quality to 100
 * $gs -> getDevice () -> setQuality ( 100 );
 *
 * // convert the input file to an image
 * if ( true === $gs -> render () ) {
 *     echo 'success';
 * } else {
 *     echo 'some error occured';
 * }
 * </code>
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Ghostscript
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   SVN: $Revision$
 * @since     03.06.2009
 */
class Org_Heigl_Ghostscript
{
    /**
     * This property contains the path to the Ghostscript-Application
     *
     * This is set when the class is first loaded
     *
     * @var string PATH
     */
    private static $PATH = null;

    /**
     * This property stores the file to process.
     *
     * @var SplFileInfo $_infile
     */
    protected $_infile = null;

    /**
     * This property stores the output-filename.
     *
     * This is NOT necessarily the filename that can be used for retrieving the
     * file as Ghostscript can use this name for more than one file if a
     * placeholder is defined.
     *
     * @var string $_outfile
     */
    protected $_outfile = 'output';

    /**
     * Which MIME-Types are supported
     *
     * @var array $supportedMimeTypes
     */
    private static $supportedMimeTypes = array (
                                          'application/eps',
                                          'application/pdf',
                                          'application/ps',
                                         );

    /**
     * Set the path to the gs-executable and return it.
     *
     * This method will be called on load of the class and needs not to be
     * called during normal operation.
     *
     * If you have Ghostscript installed in a non-standard-location that can not
     * be found via the 'which gs' command, you have to set the path manualy
     *
     * @param string|null $path The path to set
     *
     * @return string
     */
    public static function setGsPath ( $path = null ) {
        if ( null === $path ) {
            $path = exec( 'which gs' );
        }
        if ( $path ) {
            Org_Heigl_Ghostscript::$PATH = $path;
        }
        return Org_Heigl_Ghostscript::$PATH;
    }

    /**
     * Get the currently set path for the ghostscript-app
     *
     * @return string
     */
    public static function getGsPath () {
        return Org_Heigl_Ghostscript::$PATH;
    }


    /**
     * Set the file that shall be processes
     *
     * This should be a PostScript (ps), Enhanced Postscript (eps) or
     * PortableDocumentformat (pdf) File.
     *
     * @param string|SplFileInfo $file The File to use as input.
     *
     * @throws InvalidArgumentException when the provided file is not supported
     * @return Org_Heigl_Ghostscript
     */
    public function setInputFile ( $file ) {
        if ( ! $file instanceof SplFileInfo ) {
            $file = new SplFileInfo ( (string) $file );
        }
        if ( extension_loaded ( 'fileinfo' ) ) {
            $finfo = new finfo ();
            $mime = $finfo -> file ( $file -> getPathName (), FILEINFO_MIME );
            if ( ! in_array ( $mime, Org_Heigl_Ghostscript::$supportedMimeTypes ) ) {
                throw new InvalidArgumentException ( 'The provided file seems not to be of a supported MIME-Type' );
            }
        }
        $this -> _infile = $file;
        return $this;
    }

    /**
     * Get the file that shall be processed
     *
     * @return SplFileInfo
     */
    public function getInputFile () {
        return $this -> _infile;
    }

    /**
     * Set the name of the output file(s)
     *
     * This name does not need a file-extension as that is set from the output
     * format.
     *
     * The name can contain a placeholder like '%d' or '%02d'. This will be
     * replaced by the pagenumber of the processed page. For more information
     * on the format see the PHP documentation for sprintf
     *
     * @param string $name The filename
     *
     * @return Org_Heigl_Ghostscript
     */
    public function setOutputFile ( $name = 'output' ) {
        if ( 0 !== strpos ( $name, DIRECTORY_SEPARATOR ) ) {
            $name = $this -> getBasePath () . DIRECTORY_SEPARATOR . $name;
        }
        $this -> _outfile = $name;
    }

    /**
     * Get the output filename.
     *
     * This is NOT the name the file can be retrieved with as Ghostscript can
     * modify the filename, but the returned string containes the directory the
     * file(s) reside in.
     *
     * @return string
     */
    public function getOutputFile () {
        if ( 0 !== strpos ( $this -> _outfile, DIRECTORY_SEPARATOR ) ) {
            return $this -> getBasePath () . DIRECTORY_SEPARATOR . $this -> _outfile;
        }
        return $this -> _outfile;
    }

    /**
     * Get the basepath of the execution.
     *
     * Thisis set to the directory containing <var>$_infile</var>.
     *
     * If <var>$_infile</var> is not set, it is set to the systems default
     * tmp-directory.
     *
     * @return string
     */
    public function getBasePath () {
        if ( null !== $this -> _infile ) {
            return dirname ( $this -> _infile );
        }
        return sys_get_temp_dir ();
    }

    /**
     * Render the input file via Ghostscript
     *
     * @return bool
     */
    public function render () {

        $renderString = $this -> getRenderString ();

        // We can't render anything without a render string
        if ( '' == $renderString ) {
            return false;
        }

        exec ( $renderString , $returnArray, $returnValue );

        if ( 0 !== $returnValue ) {
            return false;
        }
        return true;
    }

    /**
     * Get the command-line that can be executed via exec
     *
     * @return string
     */
    public function getRenderString () {
        if ( null === $this -> getInputFile () ) {
            return '';
        }
        $string  = Org_Heigl_Ghostscript::getGsPath ();
        $string .= ' -dSAFER -dQUIET -dNOPLATFONTS -dNOPAUSE -dBATCH';
	$string .= ' sPaperSize' . $this -> getPapersize();
        $string .= ' -sOutputFile="' . $this -> getOutputFile () . '.' . $this -> getDevice () -> getFileEnding () . '"';
        $string.=  $this -> getDevice () -> getParameterString ();
        $string .= ' -r' . $this -> getResolution ();
        if ( $this -> isTextAntiAliasingSet () ) {
            $string .= ' -dTextAlphaBits=' . $this -> getTextAntiAliasing ();
        }
        if ( $this -> isGraphicsAntiAliasingSet () ) {
            $string .= ' -dGraphicsAlphaBits=' . $this -> getGraphicsAntiAliasing ();
        }
        $string .= ' "' . $this -> getInputFile () . '"';
        return $string;
    }
    /**
     * Check whether Anti ALiasing for graphics is set
     *
     * @return boolean
     */
    public function isGraphicsAntiAliasingSet () {
        if ( 0 < $this -> _graphicsAntiAliasing ) {
            return true;
        }
        return false;
    }

    /**
     * Set graphics-AntiAliasing
     *
     * @param int $level The AntiaAliasing level to set.
     *
     * @return Org_Heigl_Ghostscript
     */
    public function setGraphicsAntiAliasing ( $level ) {

        if ( $level === 0 || $level === 1 || $level === 2 || $level === 4 ) {
            $this -> _graphicsAntiAliasing = $level;
        }
        return $this;
    }

    /**
     * Stores the anti aliasing level
     *
     * @var int $_graphicsAntiAliasing
     */
    protected $_graphicsAntiAliasing = 0;

    /**
     * Get the text-AntiAliasing level
     *
     * @return int
     */
    public function getGraphicsAntiAliasing () {
        return $this -> _graphicsAntiAliasing;
    }


    /**
     * Check whether Anti ALiasing for text is set
     *
     * @return boolean
     */
    public function isTextAntiAliasingSet () {
        if ( 0 < $this -> _textAntiAliasing ) {
            return true;
        }
        return false;
    }

    /**
     * Set text-AntiAliasing
     *
     * @param int $level The AntiaAliasing level to set.
     *
     * @return Org_Heigl_Ghostscript
     */
    public function setTextAntiAliasing ( $level ) {

        if ( $level === 0 || $level === 1 || $level === 2 || $level === 4 ) {
            $this -> _textAntiAliasing = $level;
        }
        return $this;
    }

    /**
     * Stores the anti aliasing level
     *
     * @var int $_textAntiAliasing
     */
    protected $_textAntiAliasing = 0;

    /**
     * Get the text-AntiAliasing level
     *
     * @return int
     */
    public function getTextAntiAliasing () {
        return $this -> _textAntiAliasing;
    }

    const ANTIALIASING_NONE   = 0;
    const ANTIALIASING_LOW    = 1;
    const ANTIALIASING_MEDIUM = 2;
    const ANTIALIASING_HIGH   = 4;

    /**
     * Set the resolution for the rendering
     *
     * @param int The horizontal resolution to set
     * @param int The vertical resolution to set
     *
     * @return Org_Heigl_Ghostscript
     */
    public function setResolution ( $horizontal, $vertical = null ) {
        if ( null !== $vertical ) {
            $this -> _resolution = $horizontal . 'x' . $vertical;
        } else {
            $this -> _resolution = $horizontal;
        }

        return $this;
    }

    /**
     * Store the resolution
     *
     * @var string $_resolution
     */
    protected $_resolution = 72;

    /**
     * Get the resolution
     *
     * @return string
     */
    public function getResolution () {
        return $this -> _resolution;
    }

    /**
     * Set the paer size
     *
     * @param string papersize
     *
     * @return Org_Heigl_Ghostscript
     */
    public function setPapersize ( $size ) {

        $this -> _papersize = $size;
        return $this;

    }

    /**
     * Store the paper size
     *
     * @var string $_papersize
     */
    protected $_papersize = "letter";

    /**
     * Get the paper size
     *
     * @return string
     */
    public function getPapersize () {
        return $this -> _papersize;
    }


    /**
     * Set the output-device
     *
     * @param Org_Heigl_Ghostscript_Device_Abstract $device
     *
     * @return Org_Heigl_Ghostscript
     */
    public function setDevice ( $device ) {

        if ( ! $device instanceof Org_Heigl_Ghostscript_Device_Abstract ) {
            $classname = 'Org_Heigl_Ghostscript_Device_' . ucfirst ( strtolower ( $device ) );
            include_once str_replace ( '_', '/', $classname) . '.php';
            $device = new  $classname();
        }
        $this -> _device = $device;
    }

    /**
     * Which device shall be used to render the input file
     *
     * @var Org_Heigl_Ghostscript_Device_Abstract $_device
     */
    protected $_device = null;

    /**
     * Create a new Instance of the Ghostscript wrapper.
     *
     * The new Instance will use a jpeg-device as default
     *
     * @return void
     */
    public function __construct () {
        $this -> setDevice ( 'png' );
    }

    /**
     * Get the device-object
     *
     * @return Org_Heigl_Ghostscript_Device_Abstract
     */
    public function getDevice () {
        return $this -> _device;
    }

    /**
     * Set whether to use the CIE-Map for conversion between CMYK and RGB or not
     *
     * @param boolean $useCIE
     *
     * @return Org_Heigl_Ghostscript
     */
     public function setUseCie ( $useCie = true ) {
         $this -> _useCie = (bool) $useCie;
         return $this;
     }

     /**
      * Shall we use the CIE map for color-conversions?
      *
      * @return boolean
      */
     public  function useCie () {
         return (bool) $this -> _useCie;
     }

     /**
      * Store whether to use CIE for color conversion or not
      *
      * @var boolean $_useCie
      */
     protected $_useCie = false;
}

Org_Heigl_Ghostscript::setGsPath();
