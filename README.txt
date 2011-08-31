Ext-PHP Version 0.1
Extjs framework for rapidly build web application using Extjs Library
build and Development By http://indowebit.com


INSTALATION
==============================================
1. Make sure the file config_sistem.php writable for public (chmod 777)
2. Browse with firefox to installation folder 
   Example : http://localhost/<your_folder>/install
   fit site title, menu title with your application that you would to build

RUNNING WEBSITE eg (http://localhost/<your_folder>)
==================================================
USER NAME AND PASSWORD
username = admin
password = admin

HOW TO MAKE SAMPLE MODULE WORKS
- Export sample-sql file in sample-sql folder with PHPMyAdmin to test database
- Edit file app/config/config.db.php
    * make sure the mysql user and password is right with your Mysql Server 
- Click the menu once again.

Files List
start.php
- Main page that would be show after login. you can fill it with html or php script
config_ux
- the file is uses to manage ux javascript that loaded after main page completed from extjs/ux folder

HOW TO BUILD YOUR OWN MODULE. 
the whole modules located in folder app
 - app/config/config.db.php
  database configuration of yours. see adodb php doc for supported database types

app/model
 - your model file. the suffix is _m for each model

app/view_js
 - the UI extjs content file.  suffix is always _v

app/controller
 - the controller file. the suffix is _c

HOW TO CREATE NEW MENU AND EVENT MENU
Click Menu Manager 
-- "Add Menu" for create new menu 
-- "Add Sub Menu" for create new folder menu
After that click User manager to activated the menu depend User Group

More information can found at http://indowebit.com

Regards, 


Ashadi

