#!/bin/bash



VER="1.5.0"
NR="0"
SRC=/usr/lib/lx-office-crm/lxcars
DST=/tmp/packages/lxcars_$VER-$NR
CRM_PATH=/usr/lib/lx-office-crm

##mkdir -p $DST
mkdir -p $DST$CRM_PATH
cp -a $SRC $DST$CRM_PATH/
cd $DST
cp -Rf $SRC/paketbau/DEBIAN $DST/
cp $SRC/scripts/install.sh $DST/DEBIAN/postinst
cp $SRC/scripts/remove.sh $DST/DEBIAN/postrm


find . -name '*~' -exec rm -rf {} \;
find . -name '*#' -exec rm -rf {} \;
find . -name '#*' -exec rm -rf {} \;
find . -name '.git*' -exec rm -rf {} \;



#Größe feststellen:
SIZE=$(du -scb . | grep insgesamt | cut -f1)

#Controlfile updaten:
cat $DST/DEBIAN/control | sed --expression "s/Installed-Size: 0/Installed-Size: $SIZE/g" > $DST/DEBIAN/1.tmp
mv $DST/DEBIAN/1.tmp $DST/DEBIAN/control
cat $DST/DEBIAN/control | sed --expression "s/Version: 0/Version: $VER-$NR/g" > $DST/DEBIAN/1.tmp
mv $DST/DEBIAN/1.tmp $DST/DEBIAN/control

#Paket bauen:

dpkg -b $DST lxcars-$VER-$NR.deb

echo "Done"
