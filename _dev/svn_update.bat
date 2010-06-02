@ECHO OFF
COLOR 0A

cd ../
svn update
SubWCRev ./ "_dev\build_release.template" "_dev\build_release.bat" -f
