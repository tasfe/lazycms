@ECHO OFF

:: Update this variable for each new release.
SET RELEASER_VERSION=2.0.532 (SVN)

CLS

:: Look for the environment variable FCK_RELEASER_PATH.
SET RELEASER_PATH=%FCK_RELEASER_PATH%

:: If we have a command line argument, use it as the target path.
IF NOT (%1)==() SET RELEASER_PATH=%1

:: If not defined, set it to the default value.
IF (%RELEASER_PATH%)==() SET RELEASER_PATH=../../release/

D:\apmxe\php5\php.exe releaser.php ../ "%RELEASER_PATH%" "%RELEASER_VERSION%"

:End

:: Delete custom variables.
SET RELEASER_VERSION=
SET RELEASER_PATH=
