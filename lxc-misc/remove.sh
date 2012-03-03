#!/bin/sh
set -e

echo "*******************************************" 
echo "Datenbank lxcars wird gelöscht."
echo "*******************************************"
/usr/bin/sudo -u postgres dropdb lxcars
echo "Datenbankuser www-data wird gelöscht."
echo "*******************************************"
/usr/bin/sudo -u postgres dropuser www-data
echo "*******************************************"

#Links löschen und *.org in * umbenennen

 if [ -f /usr/lib/lx-office-erp/menu.ini.org ]; then
 	 	rm /usr/lib/lx-office-erp/menu.ini
		mv /usr/lib/lx-office-erp/menu.ini.org /usr/lib/lx-office-erp/menu.ini
	else
		echo "Error menue.ini.org nicht gefunden"
 fi
 
	if [ -f /usr/lib/lx-office-crm/tpl/firma1.tpl.org ]; then
 		rm /usr/lib/lx-office-crm/tpl/firma1.tpl
 		mv /usr/lib/lx-office-crm/tpl/firma1.tpl.org /usr/lib/lx-office-crm/tpl/firma1.tpl
 	else
 		echo "Error firma1.tpl.org nicht gefunden"
 fi 

if [ -f /usr/lib/lx-office-crm/inc/stdLib.php.org ]; then
 		rm /usr/lib/lx-office-crm/inc/stdLib.php
 		mv /usr/lib/lx-office-crm/inc/stdLib.php.org /usr/lib/lx-office-crm/inc/stdLib.php
 	else
 		echo "Error login.php.org nicht gefunden"
 fi 
 
 if [ -f /usr/lib/lx-office-crm/inc/login.php.org ]; then
 		rm /usr/lib/lx-office-crm/inc/login.php
 		mv /usr/lib/lx-office-crm/inc/login.php.org /usr/lib/lx-office-crm/inc/login.php
 	else
 		echo "Error login.php.org nicht gefunden"
 fi 

if [ -f /usr/lib/lx-office-crm/tpl/firmen3.tpl.org ]; then
 		rm /usr/lib/lx-office-crm/tpl/firmen3.tpl
 		mv /usr/lib/lx-office-crm/tpl/firmen3.tpl.org /usr/lib/lx-office-crm/tpl/firmen3.tpl
 	else
 		echo "Error firmen3.tpl.org nicht gefunden"
 fi 
 
 if [ -f /usr/lib/lx-office-erp/image/lx-office-erp.png.org ]; then
 		rm /usr/lib/lx-office-erp/image/lx-office-erp.png  
 		mv /usr/lib/lx-office-erp/image/lx-office-erp.png.org /usr/lib/lx-office-erp/image/lx-office-erp.png
 	else
 		echo "Error lx-office-erp.png.org nicht gefunden"
 fi 
 
 
 ## Icons löschen
 if [ -f /usr/lib/lx-office-erp/image/icons/16x16/LxCars--Schnellsuche.png ]; then
 		rm /usr/lib/lx-office-erp/image/icons/16x16/LxCars*  
 		
 	else
 		echo "Error /usr/lib/lx-office-erp/image/icons/16x16/LxCars* nicht gefunden"
 fi

if [ -f /usr/lib/lx-office-erp/image/icons/24x24/LxCars--Schnellsuche.png ]; then
 		rm /usr/lib/lx-office-erp/image/icons/24x24/LxCars*  
 		
 	else
 		echo "Error /usr/lib/lx-office-erp/image/icons/24x24/LxCars* nicht gefunden"
 fi
 
 if [ -f /usr/lib/lx-office-erp/image/icons/32x32/LxCars--Schnellsuche.png ]; then
 		rm /usr/lib/lx-office-erp/image/icons/32x32/LxCars*  
 		
 	else
 		echo "Error /usr/lib/lx-office-erp/image/icons/32x32/LxCars* nicht gefunden"
 fi


echo "done!!"

exit 0
