#!/bin/bash
#set -x

## Begin: an System, Installation anpassen
DIR_LxCars=/usr/lib/lx-office-crm/lxcars
DIR_ERP=/usr/lib/lx-office-erp
DIR_CRM=/usr/lib/lx-office-crm
## END

echo "Willkommen bei der LxCars-Installation"
echo "Verzeichnis LxCars: $DIR_LxCars"
echo "Verzeichnis ERP: $DIR_ERP"
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
if [ $(uname -i) == x86_64 ]; then
   echo "64Bit-Version wird installiert"
   ln -s $DIR_LxCars/lxc2db-x86_64 $DIR_LxCars/lxc2db
else
   echo "32Bit-Version wird installiert"
   ln -s $DIR_LxCars/lxc2db-i386 $DIR_LxCars/lxc2db
fi

/usr/bin/sudo -u www-data $DIR_LxCars/lxc2db -d lxcars -i


## Prufen ob schon *.orig-Files existieren
## wenn nicht CRM-Files in *.orig umbenennen 
## fur selbige - Links zu den entsprechenden von LxCars erweiterten Dateien erzeugen 

## Menue erstellen 
if [ -f $DIR_ERP/menu.ini.orig ]; then
   echo "Error menue.ini.orig existiert bereits"
else
 	mv $DIR_ERP/menu.ini $DIR_ERP/menu.ini.orig
	ln -s $DIR_LxCars/lx-office-erp/menu.ini  $DIR_ERP/menu.ini
	echo "menue.ini als menue.ini.oirg gesichert"
fi


## AutoComplete 
#if [ -f /usr/lib/lx-office-crm/tpl/firmen3.tpl.orig ]; then
# 	echo "Error firmen3.tpl.orig existiert bereits"
# 	else
# 		mv /usr/lib/lx-office-crm/tpl/firmen3.tpl /usr/lib/lx-office-crm/tpl/firmen3.tpl.orig
#		ln -s $DIR_LxCars/tpl/firmen3.tpl  /usr/lib/lx-office-crm/tpl/firmen3.tpl 
#	echo "firmen3.tpl als firmen3.tpl.orig gesichert"
# fi

## Logo
if [ -f $DIR_ERP/image/lx-office-erp.png.orig ]; then
 	echo "Error lx-office-erp.png.orig existiert bereits"
else
 	mv $DIR_ERP/image/lx-office-erp.png $DIR_ERP/image/lx-office-erp.png.orig
	ln -s $DIR_LxCars/image/lx-office-erp.png $DIR_ERP/image/lx-office-erp.png
	echo "lx-office-erp.png als lx-office-erp.png.orig gesichert"
fi 
 
## Bilchen furs Menu
cp $DIR_LxCars/image/icons/16x16/*  $DIR_ERP/image/icons/16x16/
cp $DIR_LxCars/image/icons/24x24/*  $DIR_ERP/image/icons/24x24/
cp $DIR_LxCars/image/icons/32x32/*  $DIR_ERP/image/icons/32x32/

chown -R www-data: $DIR_LxCars

## Kfz-Button in Kundenmaske
patch -p1 $DIR_CRM/tpl/firma1.tpl < $DIR_LxCars/lxc-misc/button.patch -b
patch -p1 $DIR_CRM/inc/stdLib.php < $DIR_LxCars/lxc-misc/stdLib.patch -b
 
echo "done!!"

exit 0
