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
cd .. #nach lxcars wechseln
if [ $(uname -i) == x86_64 ]; then
   echo "64Bit-Version wird installiert"
   ln -s lxc2db-x86_64 lxc2db
else
   echo "32Bit-Version wird installiert"
   ln -s lxc2db-i386 lxc2db
fi

/usr/bin/sudo -u www-data ./lxc2db -d lxcars -i


## Prufen ob schon *.orig-Files existieren
## wenn nicht CRM-Files in *.orig umbenennen 
## fur selbige - Links zu den entsprechenden von LxCars erweiterten Dateien erzeugen 

## Menue erstellen 
if [ -f /usr/lib/lx-office-erp/menu.ini.orig ]; then
   echo "Error menue.ini.orig existiert bereits"
else
 	mv /usr/lib/lx-office-erp/menu.ini /usr/lib/lx-office-erp/menu.ini.orig
	ln -s /usr/lib/lx-office-crm/lxcars/lx-office-erp/menu.ini  /usr/lib/lx-office-erp/menu.ini
	echo "menue.ini als menue.ini.oirg gesichert"
fi


## AutoComplete 
#if [ -f /usr/lib/lx-office-crm/tpl/firmen3.tpl.orig ]; then
# 	echo "Error firmen3.tpl.orig existiert bereits"
# 	else
# 		mv /usr/lib/lx-office-crm/tpl/firmen3.tpl /usr/lib/lx-office-crm/tpl/firmen3.tpl.orig
#		ln -s /usr/lib/lx-office-crm/lxcars/tpl/firmen3.tpl  /usr/lib/lx-office-crm/tpl/firmen3.tpl 
#	echo "firmen3.tpl als firmen3.tpl.orig gesichert"
# fi

## Logo
if [ -f /usr/lib/lx-office-erp/image/lx-office-erp.png.orig ]; then
 	echo "Error lx-office-erp.png.orig existiert bereits"
else
 	mv /usr/lib/lx-office-erp/image/lx-office-erp.png /usr/lib/lx-office-erp/image/lx-office-erp.png.orig
	ln -s /usr/lib/lx-office-crm/lxcars/image/lx-office-erp.png /usr/lib/lx-office-erp/image/lx-office-erp.png
	echo "lx-office-erp.png als lx-office-erp.png.orig gesichert"
fi 
 
## Bilchen furs Menu
cp /usr/lib/lx-office-crm/lxcars/image/icons/16x16/*  /usr/lib/lx-office-erp/image/icons/16x16/
cp /usr/lib/lx-office-crm/lxcars/image/icons/24x24/*  /usr/lib/lx-office-erp/image/icons/24x24/
cp /usr/lib/lx-office-crm/lxcars/image/icons/32x32/*  /usr/lib/lx-office-erp/image/icons/32x32/

chown -R www-data: /usr/lib/lx-office-crm/lxcars

## Kfz-Button in Kundenmaske
cd .. # /usr/lib/lx-office-crm
patch -p1 < lxcars/lxc-misc/button.patch -b

 
echo "done!!"

exit 0
