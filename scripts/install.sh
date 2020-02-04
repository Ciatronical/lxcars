#!/bin/bash
#set -x
set +e
## Begin: an System, Installation anpassen
DIR_LxCars=.
DIR_ERP=../../kivitendo-erp
DIR_CRM=..
## END

echo "Willkommen bei der LxCars-Installation"
echo "Aufruf: ./scripts/install.sh"
echo "Verzeichnis LxCars: $DIR_LxCars"
echo "Verzeichnis ERP: $DIR_ERP"

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
## Link für lxc2db erstellen
VERSION=$(uname -i) 
case "$VERSION" in
    x86_64) echo "64Bit-Version wird installiert"
            ln -sf $DIR_LxCars/lxc2db-x86_64-libpqxx-4.1 $DIR_LxCars/lxc2db
    ;;
    i386)   echo "32Bit-Version wird installiert"
            ln -sf $DIR_LxCars/lxc2db-i386 $DIR_LxCars/lxc2db
    ;;
    *)      echo "Architektur konnte nicht ermittelt werden"
	    echo "1) 32Bit"
	    echo "2) 64Bit"
	    echo -n "Treffen Sie eine Auswahl: "
	    read -n 1 arch
	    echo

	    case "$arch" in 
	         1) echo "32Bit-Version wird installiert"
                    ln -sf $DIR_LxCars/lxc2db-i386 $DIR_LxCars/lxc2db
	         ;;
                 2)  echo "64Bit-Version wird installiert"
                    ln -sf $DIR_LxCars/lxc2db-x86_64 $DIR_LxCars/lxc2db
	         ;;
		 *)  echo "Script wird beendet!"
		     echo "info@lxcars.de"
		     exit -1
		 ;;
	     esac
	;;
    esac

## Datenbank mit Datensätzen füllen
/usr/bin/sudo -u www-data $DIR_LxCars/lxc2db -d lxcars -i


## Prufen ob schon *.orig-Files existieren
## wenn nicht CRM-Files in *.orig umbenennen 
## fur selbige - Links zu den entsprechenden von LxCars erweiterten Dateien erzeugen 



## Logo
if [ -f $DIR_ERP/image/kivitendo.png.orig ]; then
 	echo "Error kivitendo.png.orig existiert bereits"
else
 	mv $DIR_ERP/image/kivitendo.png $DIR_ERP/image/kivitendo.png.orig
	cp $DIR_LxCars/image/lxcars.png $DIR_ERP/image/kivitendo.png
	echo "kivitendo.png als kivitendo.png.orig gesichert"
fi 
 
## Bildchen furs Menu
cp $DIR_LxCars/image/icons/16x16/*  $DIR_ERP/image/icons/16x16/
cp $DIR_LxCars/image/icons/24x24/*  $DIR_ERP/image/icons/24x24/
cp $DIR_LxCars/image/icons/32x32/*  $DIR_ERP/image/icons/32x32/

chown -R www-data: $DIR_LxCars

## CRM Menü erweitern
echo "Menü erweitern..."

ln -s ../../../kivitendo-crm/lxcars/menu/20-lxcars-menu.yaml ../../kivitendo-erp/menus/user/20-lxcars-menu.yaml
chown -R www-data: ../../kivitendo-erp/menus/user/20-lxcars-menu.yaml

echo "done!!"

exit 0
