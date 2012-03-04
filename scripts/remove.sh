#!/bin/sh
set -e
echo "*******************************************" 
echo "Deinstallation LxCars."
echo "*******************************************"
/usr/bin/sudo -u postgres dropdb lxcars
echo "Datenbankuser www-data wird gelöscht."
echo "*******************************************"
/usr/bin/sudo -u postgres dropuser www-data
echo "*******************************************"

#Links löschen und *.orig in * umbenennen
## Menü löschen
if [ -f /usr/lib/lx-office-erp/menu.ini.orig ]; then
   rm /usr/lib/lx-office-erp/menu.ini
   mv /usr/lib/lx-office-erp/menu.ini.orig /usr/lib/lx-office-erp/menu.ini
else
   echo "Error menue.ini.orig nicht gefunden"
fi
 
#if [ -f /usr/lib/lx-office-crm/tpl/firmen3.tpl.orig ]; then
#  rm /usr/lib/lx-office-crm/tpl/firmen3.tpl
#  mv /usr/lib/lx-office-crm/tpl/firmen3.tpl.orig /usr/lib/lx-office-crm/tpl/firmen3.tpl
#else
#  echo "Error firmen3.tpl.orig nicht gefunden"
#fi 

## Bild löschen 
if [ -f /usr/lib/lx-office-erp/image/lx-office-erp.png.orig ]; then
   rm /usr/lib/lx-office-erp/image/lx-office-erp.png  
   mv /usr/lib/lx-office-erp/image/lx-office-erp.png.orig /usr/lib/lx-office-erp/image/lx-office-erp.png
else
   echo "Error lx-office-erp.png.orig nicht gefunden"
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

## Patch rückgängig machen
if [ -f /usr/lib/lx-office-crm/tpl/firma1.tpl.orig ]; then
   rm /usr/lib/lx-office-crm/tpl/firma1.tpl
 	mv /usr/lib/lx-office-crm/tpl/firma1.tpl.orig /usr/lib/lx-office-crm/tpl/firma1.tpl
else
   echo "Error firma1.tpl.orig nicht gefunden"
fi 

## Link für Architektur löschen
rm /usr/lib/lx-office-crm/lxcars/lxc2db


echo "done!!"

exit 0
