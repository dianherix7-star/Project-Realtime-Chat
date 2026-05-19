@echo off
cd /d "%~dp0"

for /d %%i in ("D:\laragon\bin\php\php-*") do set "PHP_EXE=%%i\php.exe"

if not defined PHP_EXE (
    echo [ERROR] PHP Laragon tidak ditemukan.
    pause
    exit /b 1
)

echo Server: http://127.0.0.1:8000
"%PHP_EXE%" artisan serve
