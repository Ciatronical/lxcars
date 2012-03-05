#!/bin/sh
set +e

DIR_CRM=/usr/lib/lx-office-crm
DIR_ERP=/usr/lib/lx-office-erp

echo "*******************************************" 
echo "Deinstallation LxCars."
echo "Verzeichnis CRM: $DIR_CRM"
echo "Verzeichnis ERP: $DIR_ERP"
echo "*******************************************"
/usr/bin/sudo -u postgres dropdb lxcars
echo "Datenbankuser www-data wird gelöscht."
echo "*******************************************"
/usr/bin/sudo -u postgres dropuser www-data
echo "*******************************************"

#Links löschen und *.orig in * umbenennen
## Menü löschen
if [ -f $DIR_ERP/menu.ini.orig ]; then
   rm $DIR_ERP/menu.ini
   mv $DIR_ERP/menu.ini.orig $DIR_ERP/menu.ini
else
   echo "Error menue.ini.orig nicht gefunden"
fi
 
#if [ -f $DIR_CRM/tpl/firmen3.tpl.orig ]; then
#  rm $DIR_CRM/tpl/firmen3.tpl
#  mv $DIR_CRM/tpl/firmen3.tpl.orig $DIR_CRM/tpl/firmen3.tpl
#else
#  echo "Error firmen3.tpl.orig nicht gefunden"
#fi 

## Bild löschen 
if [ -f $DIR_ERP/image/lx-office-erp.png.orig ]; then
   rm $DIR_ERP/image/lx-office-erp.png  
   mv $DIR_ERP/image/lx-office-erp.png.orig $DIR_ERP/image/lx-office-erp.png
else
   echo "Error lx-office-erp.png.orig nicht gefunden"
fi 
 
## Icons löschen
if [ -f $DIR_ERP/image/icons/16x16/LxCars--Schnellsuche.png ]; then
   rm $DIR_ERP/image/icons/16x16/LxCars*  
else
   echo "Error $DIR_ERP/image/icons/16x16/LxCars* nicht gefunden"
fi
if [ -f $DIR_ERP/image/icons/24x24/LxCars--Schnellsuche.png ]; then
   rm $DIR_ERP/image/icons/24x24/LxCars*  
else
   echo "Error $DIR_ERP/image/icons/24x24/LxCars* nicht gefunden"
fi
if [ -f $DIR_ERP/image/icons/32x32/LxCars--Schnellsuche.png ]; then
   rm $DIR_ERP/image/icons/32x32/LxCars*  
else
   echo "Error $DIR_ERP/image/icons/32x32/LxCars* nicht gefunden"
fi

## Patch für Button rückgängig machen
if [ -f $DIR_CRM/tpl/firma1.tpl.orig ]; then
   rm $DIR_CRM/tpl/firma1.tpl
   mv $DIR_CRM/tpl/firma1.tpl.orig $DIR_CRM/tpl/firma1.tpl
else
   echo "Error firma1.tpl.orig nicht gefunden"
fi 

## Patch für stdlib rückgängig machen
if [ -f $DIR_CRM/inc/stdLib.php.orig ]; then
   rm $DIR_CRM/inc/stdLib.php
   mv $DIR_CRM/inc/stdLib.php.orig $DIR_CRM/inc/stdLib.php
else
   echo "Error stdLib.php.orig nicht gefunden"
fi 


## Link für Architektur löschen
rm $DIR_CRM/lxcars/lxc2db


echo "done!!"

exit 0
