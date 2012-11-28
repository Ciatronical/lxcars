#!/bin/bash
#set -x
set +e
## Begin: an System, Installation anpassen
DIR_LxCars=/usr/lib/lx-office-crm/lxcars
DIR_ERP=/usr/lib/lx-office-erp
DIR_CRM=/usr/lib/lx-office-crm
## END

echo "Willkommen bei der LxCars-Installation"
echo "Verzeichnis LxCars: $DIR_LxCars"
echo "Verzeichnis ERP: $DIR_ERP"





if [ "$1" = "-f" ]; then  ## für fast
	echo "Achtung Datenbank lxcars wird nicht mit Datensätzen gefüllt"
else
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
        ln -sf $DIR_LxCars/lxc2db-x86_64 $DIR_LxCars/lxc2db
    else
        echo "32Bit-Version wird installiert"
        ln -sf $DIR_LxCars/lxc2db-i386 $DIR_LxCars/lxc2db
    fi
	/usr/bin/sudo -u www-data $DIR_LxCars/lxc2db -d lxcars -i
fi


## Prufen ob schon *.orig-Files existieren
## wenn nicht CRM-Files in *.orig umbenennen 
## fur selbige - Links zu den entsprechenden von LxCars erweiterten Dateien erzeugen 



## Logo
if [ -f $DIR_ERP/image/kivitendo.png.orig ]; then
 	echo "Error kivitendo.png.orig existiert bereits"
else
 	mv $DIR_ERP/image/kivitendo.png $DIR_ERP/image/kivitendo.png.orig
	ln -sf $DIR_LxCars/image/kivitendo.png $DIR_ERP/image/kivitendo.png
	echo "kivitendo.png als kivitendo.png.orig gesichert"
fi 
 
## Bilchen furs Menu
cp $DIR_LxCars/image/icons/16x16/*  $DIR_ERP/image/icons/16x16/
cp $DIR_LxCars/image/icons/24x24/*  $DIR_ERP/image/icons/24x24/
cp $DIR_LxCars/image/icons/32x32/*  $DIR_ERP/image/icons/32x32/

chown -R www-data: $DIR_LxCars

## Kfz-Button in Kundenmaske
patch -p1 $DIR_CRM/tpl/firma1.tpl < $DIR_LxCars/lxc-misc/button.patch -b

## Menü erzeugen
patch -p1 $DIR_ERP/menu.ini < $DIR_LxCars/lxc-misc/menu.ini.patch -b
 
echo "done!!"

exit 0
