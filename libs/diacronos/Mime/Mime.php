<?php
/**
 * Mime - Comprehensive MIME type mapping API for PHP.
 *
 * @author      Rafael García <rafaelgarcia@profesionaldiacronos.com>
 * @copyright   2011 Rafael García
 * @link        http://phpsprockets.profesionaldiacronos.com
 * @license     http://phpsprockets.profesionaldiacronos.com/license
 * @version     1.0.0
 * @package     Mime
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace diacronos\Mime;

/**
 * Mime
 * 
 * Comprehensive MIME type mapping API.
 * Can include all 600+ types and 800+ extensions defined by the Apache project and others.
 *  
 * His interface consists of all static methods:
 * 
 * defineMap( array $map )
 * load( string $file )
 * lookup( string $path, mixed $fallback )
 * getExtension( string $mimeType )
 * lookupCharset( string $mimeType, mixed $fallback )
 * setDefaultType( string $defaultType )
 * getDefaultType( )
 * getType( string $type )
 * 
 * @package Mime
 * @author  Rafael García
 * @since   1.0.0
 */
class Mime
{
	/**
	 * Map of extension -> mime type
	 * @static
	 * @var array 
	 */
	static private $_types = array();
	
	/**
	 * Map of mime type -> extension
	 * @static
	 * @var array
	 */
	static private $_extensions = array();
	
	/**
	 * Default mime type
	 * @static
	 * @var string 
	 */
	static private $_defaultType;
	
	/**
   	 * Define mimetype -> extension mappings. Each key is a mime-type that maps
   	 * to an array of extensions associated with the type. The first extension is
   	 * used as the default extension for the type.
   	 *
   	 * e.g. mime.define(array('audio/ogg' => array('oga', 'ogg', 'spx')));
   	 *
   	 * @static
   	 * @access public
   	 * @param  array $map type definitions
   	 * @return void
     */
	static public function defineMap($map)
	{
		foreach ($map as $type => $exts) {
			for ($i = 0, $ci=count($exts); $i < $ci; $i++) {
				self::$_types[$exts[$i]] = $type;
			}
			
			self::$_extensions[$type] = $exts[0];
		}
	}
	
	/**
   	 * Load an Apache2-style ".types" file
   	 *
   	 * This may be called multiple times (it's expected).  Where files declare
   	 * overlapping types/extensions, the last file wins.
   	 *
   	 * @static
   	 * @access public
   	 * @param  string $file path of file to load.
   	 * @return void
   	 */
   	static public function load($file) {
   		// Read file and split into lines
   		$lines = file($file);
        $map = array();
		
		foreach ($lines as $lineno => $line) {
			$line = trim($line);
			
			// Clean up blank lines
			if (strlen($line) == 0) {
				continue;
			}
			
			// Clean up whitespace/comments
			$line = preg_replace('/\s*#.*|^\s*|\s*$/', '', $line);
			if (strlen($line) == 0) {
				continue;
			}
			
			// And split into fields
			$fields = preg_split('/\s+/', $line);
      		
      		$map[array_shift($fields)] = $fields;
		}
		
		self::defineMap($map);
	}

	/**
	 * Lookup a mime type based on extension
	 * 
	 * @static
	 * @access public
	 * @param  string $path
	 * @param  mixed  $fallback (Optional)
	 * @return mixed
	 */
	static public function lookup($path, $fallback = null)
	{
		$ext = strtolower(preg_replace('/.*[\.\/]/', '', $path));
		if (isset(self::$_types[$ext]) == 1) {
			return self::$_types[$ext];
		}
		
		if (!!$fallback) {
			return $fallback;
		}
		
		return self::getDefaultType();
	}
	
	/**
	 * Return file extension associated with a mime type
	 * 
	 * @static
	 * @access public
	 * @param  string		$mimeType
	 * @return string|null
	 */
	static public function getExtension($mimeType)
	{
		return (isset(self::$_extensions[$mimeType]) == 0) ? null : self::$_extensions[$mimeType];
	}
	
	/**
	 * Lookup a charset based on mime type.
	 * 
	 * @static
	 * @access public
	 * @param  string $path
	 * @param  mixed  $fallback (Optional)
	 * @return mixed
	 */
	static public function lookupCharset($mimeType, $fallback = null)
	{
		// Assume text types are utf8.  Modify mime logic as needed.
		return (preg_match('/^text\//', $mimeType) > 0) ? 'UTF-8' : $fallback; 
    }
	
	/**
	 * Set a default mime type.
	 * 
	 * @static
	 * @access public
	 * @param  string $defaultType (Optional) sets `bin` type as default 
	 * @return void
	 */
	static public function setDefaultType($defaultType = null)
	{
		if ($defaultType === null) {
			if (isset(self::$_types['bin']) == 1) {
				self::$_defaultType = self::$_types['bin'];
			}
		} else {
			self::$_defaultType = (string) $defaultType;
		}
    }
	
	/**
	 * Get a default mime type.
	 * 
	 * @static
	 * @access public
	 * @return string
	 */
	static public function getDefaultType()
	{
		return empty(self::$_defaultType) ? false : self::$_defaultType;
    }
    
    /**
     * Get a mime type.
     * 
     * @static
	 * @access public
	 * @param  string		$path
	 * @return string|null
     */
    static public function getType($type)
    {
    	return (isset(self::$_types[$type]) == 0) ? null : self::$_types[$type];
    }
}