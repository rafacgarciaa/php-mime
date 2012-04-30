<?php

/** @see \diacronos\Mime\Mime */
require_once '../libs/diacronos/Mime/Mime.php';
use \diacronos\Mime\Mime;

require_once('../libs/simpletest/autorun.php');

// Load our local copy of http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
Mime::load('../etc/mime.types');

// Overlay enhancements we've had requests for (and that seem to make sense)
Mime::load('../etc/extra.types');

// Set the default type
Mime::setDefaultType();

/**
 * Tests for \Mime\Mime class
 *
 * @package Mime
 * @author  Rafael García
 * @since   1.0.0
 */
class TestOfMime extends UnitTestCase
{
	public function testMimeLookup()
	{
		// easy
		$this->assertEqual('text/plain', Mime::lookup('text.txt'));
		
		// hidden file or multiple periods
		$this->assertEqual('text/plain', Mime::lookup('.text.txt'));
		
		// just an extension
		$this->assertEqual('text/plain', Mime::lookup('.txt'));
		
		// just an extension without a dot
		$this->assertEqual('text/plain', Mime::lookup('txt'));
		
		// default
		$this->assertEqual('application/octet-stream', Mime::lookup('text.nope'));
		
		// fallback
		$this->assertEqual('fallback', Mime::lookup('text.fallback', 'fallback'));
	}
	
	public function testExtensionLookup()
	{
		// easy
		$this->assertEqual('txt', Mime::getExtension(Mime::getType('text')));
		$this->assertEqual('html', Mime::getExtension(Mime::getType('htm')));
		$this->assertEqual('bin', Mime::getExtension('application/octet-stream'));
	}
	
	public function testMimeLookupUppercase()
	{
		// easy
		$this->assertEqual('text/plain', Mime::lookup('TEXT.TXT'));
	
		// just an extension
		$this->assertEqual('text/plain', Mime::lookup('.TXT'));
	
		// just an extension without a dot
		$this->assertEqual('text/plain', Mime::lookup('TXT'));
	
		// default
		$this->assertEqual('application/octet-stream', Mime::lookup('TEXT.NOPE'));
	
		// fallback
		$this->assertEqual('fallback', Mime::lookup('TEXT.FALLBACK', 'fallback'));
	}
	
	public function testCustomTypes()
	{
		$this->assertEqual('application/octet-stream', Mime::lookup('file.buffer'));
		$this->assertEqual('audio/mp4', Mime::lookup('file.m4a'));
	}
	
	public function testCharsetLookup()
	{
		// easy
		$this->assertEqual('UTF-8', Mime::lookupCharset('text/plain'));
	
		// none
		$this->assertNull(Mime::lookupCharset(Mime::getType('js')));
	
		// fallback
		$this->assertEqual('fallback', Mime::lookupCharset('application/octet-stream', 'fallback'));
	}
}
