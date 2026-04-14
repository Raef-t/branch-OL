@echo off
cd /d c:\xampp\htdocs\Olamaa_institute\backend
c:\xampp\php\php.exe artisan db:seed --class=Modules\Students\Database\Seeders\DummyStudentsSeeder
