@echo off
REM PrestoMVC Test Runner
REM Run PHPUnit tests for the framework

echo Running PrestoMVC Tests...
echo.

REM Check if phpunit.phar exists
if not exist "phpunit.phar" (
    echo Downloading PHPUnit...
    powershell -Command "Invoke-WebRequest -Uri 'https://phar.phpunit.de/phpunit-9.phar' -OutFile 'phpunit.phar'"
    echo PHPUnit downloaded.
    echo.
)

REM Run tests
echo Starting test execution...
php phpunit.phar --configuration=tests/phpunit.xml

echo.
echo Test execution completed.
pause