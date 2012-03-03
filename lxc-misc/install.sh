#!/bin/bash
#set -x

echo "Willkommen bei der LxCars-Installation"
sleep 3

echo "************************************************"
echo "Erzeuge Datenbankbenutzer www-data"
echo "************************************************" 
echo ""
## Rolle www-data fur lxc2db erstellen
/usr/bin/sudo -u postgres createuser -s www-data
echo "************************************************"
echo "Erzeuge Datenbank lxcars"
echo "************************************************" 
## Datenbank für lxc2db erstellen
/usr/bin/sudo -u postgres createdb lxcars


## Prufen ob schon *.org-Files existieren
## wenn nicht CRM-Files in *.org umbenennen 
## fur selbige - Links zu den entsprechenden von LxCars erweiterten Dateien erzeugen 

## Menue erstellen 
if [ -f /usr/lib/lx-office-erp/menu.ini.org ]; then
 	echo "Error menue.ini.org existiert bereits"
 	else
 	mv /usr/lib/lx-office-erp/menu.ini /usr/lib/lx-office-erp/menu.ini.org
	ln -s /usr/lib/lx-office-crm/lxcars/lx-office-erp/menu.ini  /usr/lib/lx-office-erp/menu.ini
	echo "menue.ini als menue.ini.org gesichert"
 fi

## Kfz-Button in Kundenmaske

if [ -f /usr/lib/lx-office-crm/tpl/firma1.tpl.org ]; then
 	echo "Error firma1.tpl.org existiert bereits"
 	else
 		mv /usr/lib/lx-office-crm/tpl/firma1.tpl /usr/lib/lx-office-crm/tpl/firma1.tpl.org
		ln -s /usr/lib/lx-office-crm/lxcars/tpl/lxcfirma1.tpl /usr/lib/lx-office-crm/tpl/firma1.tpl
	echo "firma1.tpl als firma1.tpl.org gesichert"
 fi

## Menueprobleme nach dem Login beheben
if [ -f /usr/lib/lx-office-crm/inc/stdLib.php.org ]; then
 	echo "Error stdLib.php.org existiert bereits"
 	else
 		mv /usr/lib/lx-office-crm/inc/stdLib.php /usr/lib/lx-office-crm/inc/stdLib.php.org
		ln -s /usr/lib/lx-office-crm/lxcars/inc/stdLib.php /usr/lib/lx-office-crm/inc/stdLib.php 
	echo "stdLib.php als stdLib.php.org gesichert"
 fi

if [ -f /usr/lib/lx-office-crm/inc/login.php.org ]; then
 	echo "Error login.php.org existiert bereits"
 	else
 		mv /usr/lib/lx-office-crm/inc/login.php /usr/lib/lx-office-crm/inc/login.php.org
		ln -s /usr/lib/lx-office-crm/lxcars/inc/login.php /usr/lib/lx-office-crm/inc/login.php 
	echo "login.php als login.php.org gesichert"
 fi

## AutoComplete 
if [ -f /usr/lib/lx-office-crm/tpl/firmen3.tpl.org ]; then
 	echo "Error firmen3.tpl.org existiert bereits"
 	else
 		mv /usr/lib/lx-office-crm/tpl/firmen3.tpl /usr/lib/lx-office-crm/tpl/firmen3.tpl.org
		ln -s /usr/lib/lx-office-crm/lxcars/tpl/firmen3.tpl  /usr/lib/lx-office-crm/tpl/firmen3.tpl 
	echo "firmen3.tpl als firmen3.tpl.org gesichert"
 fi

## Logo
if [ -f /usr/lib/lx-office-erp/image/lx-office-erp.png.org ]; then
 	echo "Error lx-office-erp.png.org existiert bereits"
 	else
 	mv /usr/lib/lx-office-erp/image/lx-office-erp.png /usr/lib/lx-office-erp/image/lx-office-erp.png.org
	ln -s /usr/lib/lx-office-crm/lxcars/image/lx-office-erp.png /usr/lib/lx-office-erp/image/lx-office-erp.png
	echo " lx-office-erp.png als lx-office-erp.png.org gesichert"
 fi 
 
## Bilchen furs Menu
cp /usr/lib/lx-office-crm/lxcars/image/icons/16x16/*  /usr/lib/lx-office-erp/image/icons/16x16/
cp /usr/lib/lx-office-crm/lxcars/image/icons/24x24/*  /usr/lib/lx-office-erp/image/icons/24x24/
cp /usr/lib/lx-office-crm/lxcars/image/icons/32x32/*  /usr/lib/lx-office-erp/image/icons/32x32/

chown -R www-data: /usr/lib/lx-office-crm/lxcars
 
 
echo "done!!"

exit 0
