@echo off
cd /d "C:\xampp\htdocs\zaiproty_v4.6\source-code"
"C:\xampp\php\php.exe" artisan schedule:run >> "C:\xampp\htdocs\zaiproty_v4.6\scheduler.log" 2>&1
