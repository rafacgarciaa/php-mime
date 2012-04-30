# mime

Comprehensive MIME type mapping API. Can include all 600+ types and 800+ extensions defined by the Apache project and others.

## Install

Before use must be load the extensions map, in my case, like following:

    require_once '../libs/diacronos/Mime/Mime.php';
    use \diacronos\Mime\Mime;

    // Load our local copy of http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
    Mime::load('../etc/mime.types');

    // Overlay enhancements we've had requests for (and that seem to make sense)
    Mime::load('../etc/extra.types');

## API - Queries

### Mime::lookup( string $path, mixed $fallback )
Get the mime type associated with a file. Performs a case-insensitive lookup using the extension in `path` (the substring after the last '/' or '.').  E.g.

    Mime::lookup('/path/to/file.txt');         // => 'text/plain'
    Mime::lookup('file.txt');                  // => 'text/plain'
    Mime::lookup('.TXT');                      // => 'text/plain'
    Mime::lookup('htm');                       // => 'text/html'

### Mime::getExtension( string $mimeType )
Get the default extension for `$mimeType`

    Mime::getExtension('text/html');                 // => 'html'
    Mime::getExtension('application/octet-stream');  // => 'bin'

### Mime::lookupCharset( string $mimeType, mixed $fallback )

Map mime-type to charset

    Mime::lookupCharset('text/plain');        // => 'UTF-8'

(The logic for charset lookups is pretty rudimentary.  Feel free to suggest improvements.)

## API - Defining Custom Types

The following APIs allow you to add your own type mappings within your project.

### Mime::defineMap( array $map )

Add custom mime/extension mappings

    Mime::defineMap(array(
        'text/x-some-format' => array('x-sf', 'x-sft', 'x-sfml'),
        'application/x-my-type'=> array('x-mt', 'x-mtt'),
        // etc ...
    });

    Mime::lookup('x-sft');                 // => 'text/x-some-format'

The first entry in the extensions array is returned by `Mime::getExtension()`. E.g.

    Mime::getExtension('text/x-some-format'); // => 'x-sf'

### Mime::load( string $file )

Load mappings from an Apache ".types" format file

    Mime::load('./my_project.types');

The .types file format is simple -  See the `etc` dir for examples.

