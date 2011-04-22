Ext-PHP Versi 0.1
Framework Extjs untuk membangun aplikasi dengan cepat menggunakan libray Extjs
build and Development By http://indowebit.co.cc


INSTALATION
==============================================
1. Make config_sistem.php writable (chmod 777)
2. Browse with firefox to installation folder 
   Example : http://localhost/<your_folder>/install
   Sesuaikan site title, menu title dengan aplikasi yang akan anda bangun

RUNNING WEBSITE eg (http://localhost/<your_folder>)
==================================================
USER NAME AND PASSWORD
username = admin
password = admin

Menjalankan Sample Modul   
- Dumping sample-sql di folder sample-sql dengan PHPMyadmin ke database test
- Edit file app/config/config.db.php
    * pastikan password dan username dari mysql anda benar
- Klik menu sekali lagi

Daftar File
start.php
- Halaman awal yang akan ditampilan pada waktu setelah login. bisa berisi html biasa ataupun php script
config_ux
- file yang mengatur untuk meload ux javascript dari folder extjs/ux

Membangun aplikasi
semua modul akan diletakkan difolder app
app/config/config.db.php
 - Konfigurasi dari database anda. lihat dokumentasi adodb php untuk jenis-jenis database yang didukung

app/model
 - file model anda. usahakan dengan akhiran _m untuk akhir setiap file

app/view_js
 - file UI content Extjs. usahakan dengan akhiran _v untuk setiap file

app/controller
 - controller request dari UI view. usahakan dengan akiran _c untuk setiap file

Membuat Menu dan Event
klik menu manager
 -Add Menu untuk membuat menu
 -Add Sub Menu untuk membuat folder menu

Setelah Membuat Menu klik User Manager untuk mengatur agar menu tersebut dapat diakses oleh Group User

