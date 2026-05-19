@echo off
cd /d "%~dp0"

for /d %%i in ("D:\laragon\bin\php\php-*") do set "PHP_EXE=%%i\php.exe"

if not defined PHP_EXE (
    echo [ERROR] PHP Laragon tidak ditemukan.
    echo Jalankan dari Terminal Laragon: Menu ^> Laragon ^> Terminal
    pause
    exit /b 1
)

echo === Setup Chat App ===
echo.

echo [1/4] Membuat database...
"%PHP_EXE%" -r "$pdo=new PDO('mysql:host=127.0.0.1;port=3306','root','');$pdo->exec('CREATE DATABASE IF NOT EXISTS realtime_chatapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');echo 'OK';"

echo.
echo [2/4] Menjalankan migrasi...
"%PHP_EXE%" artisan migrate --force

echo.
echo [3/4] Mengisi data demo...
"%PHP_EXE%" artisan db:seed --force

echo.
echo [4/4] Membersihkan cache...
"%PHP_EXE%" artisan optimize:clear

echo.
echo ========================================
echo   SELESAI! Buka salah satu URL ini:
echo   http://project_realtime_chat.test
echo   http://127.0.0.1:8000  (setelah serve.bat)
echo.
echo   Login demo:
echo   Email    : test@example.com
echo   Password : password
echo ========================================
pause
