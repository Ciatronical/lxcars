#!/bin/bash


## Sicherung nach X Tagen löschen
alter=1

## Verzeichnis für die Sicherungen
## Verzeichnis muss existieren
DIR=/tmp



## Liste der zu sichernden Datenbanken erstellen
DB_LIST=$(su postgres -c "psql -l -t | cut -d '|' -f1 | cut -d ':' -f1 | sed  '/^ *$/d' | sed '/temp/d' | sed '/lxcars/d' | tr '\n' '\t'")
## Postgresql Version ermitteln
PG_VER=$(psql -V | sed '1q' | sed 's/.* \([6-9]\+\.[0-9]\+\).*/\1/')
##echo "Postgresql Version: $PG_VER"

echo "Sichere Lx-Office ..."

## Prüfen ob Verzeichnisse existiert, wenn nicht dann erstellen
if [ -d $DIR ]; then
 	echo "Verzeichnis \"$DIR\" prüfen....OK"
else
	echo "Verzeichnis \"$DIR\" existiert nicht."
	echo "Breche ab.. "
	exit -1
fi
if [ -d $DIR/lxo-dasi ]; then
 	echo "Verzeichnis \"$DIR/lxo-dasi\" prüfen....OK"
else
 	mkdir $DIR/lxo-dasi
	echo "Verzeichnis \"$DIR/lxo-dasi\" erstellt."
fi
if [ -d $DIR/lxo-dasi/db ]; then
 	echo "Verzeichnis \"$DIR/lxo-dasi/db\" prüfen....OK"
else
 	mkdir $DIR/lxo-dasi/db
	echo "Verzeichnis \"$DIR/lxo-dasi/db\" erstellt"
fi
if [ -d $DIR/lxo-dasi/etc ]; then
 	echo "Verzeichnis \"$DIR/lxo-dasi/etc\" prüfen....OK"
else
 	mkdir $DIR/lxo-dasi/etc
	echo "Verzeichnis \"$DIR/lxo-dasi/etc\" erstellt"
fi

chmod -R 777 $DIR/lxo-dasi/

echo "Paketliste wird in $DIR/lxo-dasi/paketliste.txt gesichert"
dpkg --get-selections > $DIR/lxo-dasi/paketliste.txt
echo "Info: Paket einspielen mit"
echo "dpkg --set-selections < paketliste.txt && dselect install remove"

echo "postgresql.conf, pg_hba.conf werden in $DIR/lxo-dasi gesichert"
cp /etc/postgresql/$PG_VER/main/postgresql.conf $DIR/lxo-dasi
cp /etc/postgresql/$PG_VER/main/pg_hba.conf $DIR/lxo-dasi

echo "Verzeichnis /etc/lx-office-erp $DIR/lxo-dasi gesichert"
cp -R /etc/lx-office-erp $DIR/lxo-dasi/etc

for i in $DB_LIST; do
	echo "Datenbank \"$i\" wird gesichert."
	su postgres -c "pg_dump -o -O $i > $DIR/lxo-dasi/db/$i.sql"
done

echo "Verzeichnisse und Dateien nach $DIR/lxo-dasi kopieren...."
cp -R /usr/lib/lx-office-erp $DIR/lxo-dasi
cp -R /usr/lib/lx-office-crm $DIR/lxo-dasi

echo "Erzeuge tar-File: $DIR/LX-Sicherung$dat.tgz"
dat=$(date +%F--%HUhr%MMin)
tar czf $DIR/LX-Sicherung$dat.tgz $DIR/lxo-dasi
find $DIR -iname 'LX-Sicherung*' -mtime +$alter -exec rm {} \;

echo "fertig..."