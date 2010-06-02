#!/usr/bin/php -q
<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL               LLLL  |
 * | LL                            LL   L  LLL   LL  LL   L             LL  LL |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL         LL  LL      LL |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL      LL  LL     LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL     LL  LL    LL   |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL      LLLL    LL    |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL      LLLL   LL     |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL        LL    LLLLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (C) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | LazyCMS is free software. This version use Apache License 2.0             |
 * | See LICENSE.txt for copyright notices and details.                        |
 * +---------------------------------------------------------------------------+
 */

echo( "\n" ) ;
echo( 'LazyCMS Releaser' . "\n" ) ;
echo( 'Copyright (C) 2009 lazycms.net All rights reserved' . "\n" ) ;
echo( "\n" ) ;

// Check the number of arguments passed. The first one is the script name.
if ( count( $argv ) > 5 )
	ExitError( 'Invalid arguments. Operation aborted.' ) ;

if ( count( $argv ) < 4 )
	ExitError( 'Please specify the source and the target directories and the version number.' ) ;

$sourceDir	= $argv[1] ;
$targetDir	= $argv[2] ;
$version	= $argv[3] ;

// Get the package definition file.
$xmlFileName = 'releaser.xml' ;

if ( isset( $argv[4] ) )
	$xmlFileName = $argv[4] ;

echo '### Release started', "\n\n" ;

// ### Copy the files.

$releaser = new Releaser( $sourceDir, $targetDir, $xmlFileName ) ;
$releaser->Run() ;

// ### Set version and build information.

VersionMarker::Mark( $targetDir, $version ) ;

echo "\n", '### Compress source', "\n\n" ;

// ### Run the packager in the target directory.

// Save the current directory.
$curDir = getcwd() ;

// Move to the target ;
chdir( $targetDir ) ;

// Run the packager.
$packager = new Packager() ;
$packager->LoadDefinitionFile( 'packager.xml' ) ;
$packager->Run() ;

// Move back to the startup dir.
chdir( $curDir ) ;

echo "\n\n", '### Release finished (version "', $version, '")', "\n" ;


function ExitError( $message, $errorNumber = 1 )
{
	user_error( $message ) ;
	exit( $errorNumber ) ;
}

function StrEndsWith( $str, $sub )
{
	return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub ) ;
}

function GetXmlAttribute( $element, $attName, $defValue = '' )
{
	if ( !isset( $element->Attributes[ $attName ] ) )
		return $defValue ;

	return $element->Attributes[ $attName ] ;
}

function CreateDir($path, $rights = 0777)
{
	$dirParts = explode( '/', $path ) ;

	$currentDir = '' ;

	foreach ( $dirParts as $dirPart )
	{
		$currentDir .= $dirPart . '/' ;

		if ( strlen( $dirPart ) > 0 && !is_dir( $currentDir ) )
			mkdir( $currentDir, $rights ) ;
	}
}

function SaveStringToFile( $strData, $filePath, $includeUtf8Bom = FALSE )
{
	$f = fopen( $filePath, 'wb' ) ;

	if ( !$f )
		return FALSE ;

	if ( $includeUtf8Bom )
		fwrite( $f, "\xEF\xBB\xBF" ) ;	// BOM

	fwrite( $f, StripUtf8Bom( $strData ) ) ;
	fclose( $f ) ;

	return TRUE ;
}

function StripUtf8Bom( $data )
{
	if ( substr( $data, 0, 3 ) == "\xEF\xBB\xBF" )
		return substr_replace( $data, '', 0, 3 ) ;

	return $data ;
}

function GetMicrotime()
{
	$timeParts = explode( ' ', microtime() ) ;

	return $timeParts[0] + $timeParts[1] ;
}

function FixDirSlash( $dirPath )
{
	$dirPath = str_replace( '\\', '/', $dirPath ) ;

	if ( substr( $dirPath, -1, 1 ) != '/' )
		$dirPath .= '/' ;

	return $dirPath ;
}

function RemoveDir( $path )
{
	// Add trailing slash to $path if one is not there
	if ( !preg_match( '#[/\\\\]$#', $path ) )
		$path .= '/' ;

	$all_files = array_merge(
		glob( $path . '*' ),
		glob( $path . '\.?*' ) ) ;		// Hidden files (Unix).

	foreach ( $all_files as $file )
	{
		# Skip pseudo links to current and parent dirs (./ and ../).
		if ( preg_match( '/(\.|\.\.)$/', $file ) )
			continue ;

		if ( is_file( $file ) )
			unlink( $file ) ;
		else if ( is_dir( $file ) )
			RemoveDir( $file ) ;
	}

	if ( is_dir( $path ) )
	   rmdir( $path ) ;
}

function GetFileExtension( $filePath )
{
	$info = pathinfo( $filePath ) ;
	return $info['extension'] ;
}

class Releaser
{
	var $_PreProcessExtensions = array( 'js','html','css','xml','txt','php','inc') ;

	var $SourcesDir ;
	var $TargetDir ;

	var $IgnoreDirs ;
	var $IgnoreFiles ;
	var $OriginalFiles ;

	function Releaser( $sourceDir, $targetDir, $xmlDefinitionFile )
	{
		// The source and target directories must end with a slash.
		$sourceDir = FixDirSlash( $sourceDir ) ;
		$targetDir = FixDirSlash( $targetDir ) ;

		$this->SourcesDir	= $sourceDir ;
		$this->TargetDir	= $targetDir ;

		$xmlDefinition = new XmlDocument() ;

		if ( !$xmlDefinition->LoadFile( $xmlDefinitionFile ) )
		   ExitError( 'Could not load XML definition file "' . $xmlDefinitionFile . '"' ) ;

		// Get the root "Release" element.
		$releaseNode =& $xmlDefinition->Children[ 'RELEASE' ][0] ;

		// Builds the Directories Ignore List.
		$this->IgnoreDirs = array() ;
		if ( isset( $releaseNode->Children[ 'IGNOREDIR' ] ) )
		{
			$ignoreNodes = $releaseNode->Children[ 'IGNOREDIR' ] ;

			foreach ( $ignoreNodes as $ignoreNode )
			{
				$this->IgnoreDirs[] = FixDirSlash( $sourceDir . $ignoreNode->Attributes[ 'PATH' ] ) ;
			}
		}

		// Builds the Files Ignore List.
		$this->IgnoreFiles = array() ;
		if ( isset( $releaseNode->Children[ 'IGNOREFILE' ] ) )
		{
			$ignoreNodes = $releaseNode->Children[ 'IGNOREFILE' ] ;
			foreach ( $ignoreNodes as $ignoreNode )
			{
				$this->IgnoreFiles[] = $sourceDir . $ignoreNode->Attributes[ 'PATH' ] ;
			}
		}

		// Builds the Files Ignore List.
		$this->OriginalFiles = array() ;
		if ( isset( $releaseNode->Children[ 'ORIGINALFILE' ] ) )
		{
			$originalNodes = $releaseNode->Children[ 'ORIGINALFILE' ] ;
			foreach ( $originalNodes as $originalNode )
			{
				$this->OriginalFiles[] = (object)array(
					'Source' => $originalNode->Attributes[ 'SOURCEPATH' ],
					'Target' => $originalNode->Attributes[ 'TARGETPATH' ] ) ;
			}
		}
	}

	function Run()
	{

		// Deletes the target directory if it already exists.
		if ( is_dir( $this->TargetDir ) )
		{
			echo '!!! Deleting destination folder. It already exists.', "\n\n" ;
			RemoveDir( $this->TargetDir ) ;
		}

		// Copy the entire source directory
		$this->_CopyDirectory( $this->SourcesDir, $this->TargetDir ) ;

		foreach ( $this->OriginalFiles as $originalFile )
		{
			$this->_CopyOriginalFile(
				$originalFile->Source,
				$originalFile->Target ) ;
		}
	}

	function _CopyDirectory( $sourceDir, $targetDir )
	{
		$sourceDir = FixDirSlash( $sourceDir ) ;
		$targetDir = FixDirSlash( $targetDir ) ;

		// Check the ignore list.
		if ( in_array( $sourceDir, $this->IgnoreDirs ) )
			return ;

		echo 'Copying folder ', $sourceDir, "\n" ;
		//echo 'Copying folder ', $sourceDir, ' to ', $targetDir, "\n" ;

		if ( !is_dir( $targetDir ) )
			CreateDir( $targetDir ) ;

		// Copy files and directories.
		$sourceDirHandler = opendir( $sourceDir ) ;

		while ( $file = readdir( $sourceDirHandler ) )
		{
			// Skip ".", ".." and hidden fields (Unix).
			if ( substr( $file, 0, 1 ) == '.' )
				continue ;

			$sourcefilePath = $sourceDir . $file ;
			$targetFilePath = $targetDir . $file ;

			if ( is_dir( $sourcefilePath ) )
			{
				$this->_CopyDirectory( $sourcefilePath, $targetFilePath ) ;
				continue ;
			}

			if ( !is_file( $sourcefilePath ) )
				continue ;

			if ( in_array( $sourcefilePath, $this->IgnoreFiles ) )
				continue ;

			// echo '  Copying file ', $file, ' to ', $targetFilePath, "\n" ;

			if ( in_array( GetFileExtension( $file ), $this->_PreProcessExtensions ) )
				PreProcessor::ProcessFile( $sourcefilePath, $targetFilePath ) ;
			else
				copy( $sourcefilePath, $targetFilePath ) ;
		}

		closedir( $sourceDirHandler ) ;
	}

	function _CopyOriginalFile( $sourceFile, $destinationFile )
	{
		$sourceFile			= $this->SourcesDir . $sourceFile ;
		$destinationFile	= $this->TargetDir . $destinationFile ;

		$destDir = dirname( $destinationFile ) ;

		if ( !is_dir( $destDir ) )
			CreateDir( $destDir ) ;

		echo 'Copying original file ', $sourceFile, "\n" ;

		copy( $sourceFile, $destinationFile ) ;
	}
}

class PreProcessor
{
	function PreProcessor()
	{

    }

	// Call it statically. E.g.: PreProcessor::ProcessFile( ... )
	function ProcessFile( $sourceFilePath, $destinationFilePath, $onlyHeader = FALSE )
	{
        SaveStringToFile(
            PreProcessor::Process( file_get_contents( $sourceFilePath ), $onlyHeader ),
            $destinationFilePath,
            StrEndsWith( $sourceFilePath, '.js' ) ) ;	// Only JavaScript files require the BOM.

		// Set the destination file Last Access and Last Write times.
		// It seams we can't change the creation time with PHP.
		touch( $destinationFilePath, filemtime( $sourceFilePath ), fileatime( $sourceFilePath ) ) ;
	}

	// Call it statically. E.g.: PreProcessor::Process( ... )
	function Process( $data, $onlyHeader = false )
	{
		if ( ! $onlyHeader )
		{
			// Remove everything between the @Packager.Remove.Start and
			// @Packager.Remove.End clauses including the clauses lines.
			$data = preg_replace(
				'/(?m-s:^.*?@Packager\.Remove\.Start).*?(?m-s:@Packager\.Remove\.End.*?$\n?)/is',
				'', $data ) ;

			// Remove all lines containing the @Packager.RemoveLine clause.
			$data = preg_replace(
				'/^.*@Packager\.RemoveLine.*$\n?/im',
				'', $data ) ;
		}

		// Fix invalid line breaks (must be all CRLF).
		$data = preg_replace(
			'/(?:(?<!\r)\n)|(?:\r(?!\n))/im',
			"\r\n", $data ) ;

		return $data ;
	}
}

class VersionMarker
{
	function VersionMarker()
	{}

	function Mark( $targetDir, $version )
	{
		echo "\n", 'Marking with version "', $version, '"', "\n" ;

		$targetDir = FixDirSlash( $targetDir ) ;

		$files = array(
			'common/defines.php'
        ) ;

		foreach ( $files as $file )
		{
			$data = file_get_contents( $targetDir . $file ) ;

			$data = str_replace( '[Development]', $version , $data ) ;

			SaveStringToFile( $data, $targetDir . $file ) ;
		}
	}
}



class Packager
{
	var $PackageFiles ;
	var $RemoveDeclaration ;

	var $_TotalFiles ;

	function Packager()
	{
		$this->PackageFiles = array() ;
		$this->RemoveDeclaration = true ;

		$this->_TotalFiles = 0 ;
	}

	function LoadDefinitionFile( $packageDefinitionXmlPath )
	{
		$XML = new XmlDocument() ;

		if ( !$XML->LoadFile( $packageDefinitionXmlPath ) )
		   ExitError( 'Could not load XML definition file "' . $packageDefinitionXmlPath . '"' ) ;

		$this->LoadDefinitionFileXmlDocument( $XML ) ;
	}

	function LoadDefinitionXml( $packageDefinitionXml )
	{
		$XML = new XmlDocument() ;

		if ( !$XML->LoadXml( $packageDefinitionXml ) )
		   ExitError( 'Could not load XML data' ) ;

		$this->RunXmlDocument( $XML ) ;
	}

	function LoadDefinitionFileXmlDocument( $packageDefinitionXmlDocument )
	{
		// Get the root "Package" element.
		$packageNode = &$packageDefinitionXmlDocument->Children[ 'PACKAGE' ][0] ;

		// Get the Header text.
		if ( isset( $packageNode->Children[ 'HEADER' ] ) )
			$header = $packageNode->Children[ 'HEADER' ][0]->Value ;
		else
			$header = 0 ;

		// Get the Package Files definitions.
		$packageFileNodes = $packageNode->Children[ 'PACKAGEFILE' ] ;

		if ( isset( $packageFileNodes ) )
		{
			$this->_TotalFiles += count( $packageFileNodes ) ;

			// Loop through the package files.
			foreach ( $packageFileNodes as $packageFileNode )
			{
				// Create the package file instance.
				$file = new PackageFile( $packageFileNode->Attributes[ 'PATH' ] ) ;
				$file->CompactJavaScript	= ( GetXmlAttribute( $packageFileNode, 'COMPACTJAVASCRIPT', 'true' ) == 'true' ) ;
				$file->Header				= $header ;

				// Get all files defined for that package file.
				$fileNodes = $packageFileNode->Children[ 'FILE' ] ;

				if ( isset( $fileNodes ) )
				{
					// Loop throwgh the files.
					foreach ( $fileNodes as $fileNode )
					{
						$file->AddFile( $fileNode->Attributes[ 'PATH' ] ) ;
					}
				}

				$this->PackageFiles[] = $file ;
			}
		}
	}

	function Run()
	{
		$startTime = GetMicrotime() ;

		foreach ( $this->PackageFiles as $packageFile )
		{
			$packageFile->CreateFile() ;
		}

		$execTime = GetMicrotime() - $startTime ;
		$execTime = number_format( $execTime, 10 ) ;

		switch ( $this->_TotalFiles )
		{
			case 0 :
				echo( 'No files defined' ) ;
				break;
			case 1 :
				echo( 'The generation of the package file has been completed in ' . $execTime . ' seconds.' ) ;
				break;
			default :
				echo( 'The generation of ' . $this->_TotalFiles . ' files has been completed in ' . $execTime . ' seconds.' ) ;
				break;
		}
	}
}

class PackageFile
{
	// Public properties.
	var $CompactJavaScript ;
	var $Header ;

	// Private properties.
	var $_OutputPath ;
	var $_Files ;

	function PackageFile( $outputPath )
	{
		$this->CompactJavaScript = TRUE ;
		$this->Header = '' ;

		$this->_OutputPath = $outputPath ;
		$this->_Files = array() ;
	}

	function AddFile( $sourceFilePath )
	{
		$this->_Files[] = $sourceFilePath ;
	}

	function CreateFile()
	{
        global $curDir;
		echo 'Packaging file ' . basename( $this->_OutputPath ) . "\n" ;

		// Extract the directory from the output file path.
		$destDir = dirname( $this->_OutputPath );

		// Create the directory if it doesn't exist.
		if ( !@is_dir( $destDir ) )
			CreateDir( $destDir ) ;

		// Create the StringBuilder that will hold the output data.
		$outputData = '' ;

		$uncompressedSize = 0 ;

		// Loop through the files.
		foreach ( $this->_Files as $file )
		{
			// Read the file.
			$data = file_get_contents( $file ) ;

			// Strip the UTF-8 BOM, if available.
			$data = StripUtf8Bom( $data ) ;

			$dataSize = strlen( $data ) ;
			$uncompressedSize += $dataSize ;

			echo '    Adding ' . basename( $file ) . "\n" ;

			$outputData .= PreProcessor::Process( $data ) ;

			// Each file terminates with a CRLF, even if compressed.
			$outputData .= "\r\n" ;
		}

		if ( !SaveStringToFile( $outputData, $this->_OutputPath, TRUE ) )
			ExitError( 'It was not possible to save the file "' . $this->_OutputPath . '".' ) ;

        // Compress (if needed) and process its contents.
        if ( $this->CompactJavaScript )
            exec("java -jar {$curDir}\yuicompressor-2.4.2.jar --type js --charset utf-8 {$this->_OutputPath} -o {$this->_OutputPath}");

        // Write the output file.
		 if ( strlen( $this->Header ) > 0 )
            $outputData = file_get_contents( $this->_OutputPath );
			$outputData = $this->Header . $outputData ;
            if ( !SaveStringToFile( $outputData, $this->_OutputPath, TRUE ) )
			    ExitError( 'It was not possible to save the file "' . $this->_OutputPath . '".' ) ;

        // Compress file size
        $compressedSize = filesize($this->_OutputPath);

		echo( "\n" );
		echo( '    Number of files processed: ' . count( $this->_Files ) . "\n" ) ;
		echo( '    Original size............: ' . number_format( $uncompressedSize ) . ' bytes' . "\n" ) ;
		echo( '    Output file size.........: ' . number_format( $compressedSize ) . ' bytes (' . round( $compressedSize / $uncompressedSize * 100, 2 ) . '% of original)' . "\n" ) ;
		echo( "\n" );
	}
}

class XmlDocument
{
	// Public properties.
	var $Children ;

	// Private properties.
	var $_XmlParser ;
	var $_CurrentNode ;

	function XmlDocument()
	{
		$this->Children = array() ;
	}

	function LoadFile( $filePath )
	{
		$this->Children = array() ;
		$this->_CurrentNode = &$this ;

		return $this->LoadXml( file_get_contents( $filePath ) ) ;
	}

	function LoadXml( $xml )
	{
		// Create the XML Parser.
		$this->_XmlParser = xml_parser_create( '' ) ;

		// Setup the parser.
		xml_parser_set_option( $this->_XmlParser, XML_OPTION_SKIP_WHITE, 1 ) ;
		xml_set_object( $this->_XmlParser, $this ) ;
		xml_set_element_handler( $this->_XmlParser, '_ElementOpen', '_ElementClosed' ) ;

		xml_set_character_data_handler( $this->_XmlParser, '_ElementData' ) ;

		// Parse it.
		if( !xml_parse( $this->_XmlParser, $xml ) )
		{
		   ExitError( sprintf( "XML error: %s at line %d",
				xml_error_string(xml_get_error_code( $this->_XmlParser ) ),
				xml_get_current_line_number( $this->_XmlParser ) ) ) ;
		}

		// Release the parser.
		xml_parser_free( $this->_XmlParser ) ;

		unset( $this->_XmlParser ) ;
		unset( $this->_CurrentNode ) ;

		// For debug purposes:
		// SaveStringToFile( print_r( $this, TRUE ), 'parsed.txt' ) ;
		// print_r( $this ) ;
		// exit ;

		return TRUE ;
	}

	function _ElementOpen( $parser, $name, $attrs )
	{
		$newNode = (object)array(
			'Parent' => &$this->_CurrentNode,
			'Name' => $name,
			'Attributes' => $attrs,
			'Value' => '',
			'Children' => array() ) ;

		$this->_CurrentNode->Children[ $name ][] = &$newNode ;

		$this->_CurrentNode = &$newNode ;
	}

	function _ElementClosed( $parser, $name )
	{
		$this->_CurrentNode = &$this->_CurrentNode->Parent ;
	}

	function _ElementData( $parser, $data )
	{
		$this->_CurrentNode->Value .= $data ;
	}
}
