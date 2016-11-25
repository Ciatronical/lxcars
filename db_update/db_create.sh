#!/bin/bash

#######################################
# Sicherung kivitendo-DB
#######################################

# Verzeichnis fuer die Sicherung
# Verzeichnis muss existieren
TMPDIR=/tmp/dasi-db

# Liste der zu sichernden Datenbanken erstellen
##########DB_LIST=$(su postgres -c "psql -l -t | cut -d '|' -f1 | cut -d ':' -f1 | sed  '/^ *$/d' | sed '/temp/d' | sed '/lxcars/d' | tr '\n' '\t'")

##########echo "sichere Lx-Office ..."

# Pruefen ob Verzeichnis existiert, wenn nicht dann erstellen
##########if [ -d $TMPDIR ]; then
##########    echo "Verzeichnis \"$TMPDIR\" prüfen....OK"
##########else
##########    echo "Verzeichnis \"$TMPDIR\" existiert nicht."
##########    echo "Lege Verzeichnis \"$TMPDIR\" an ..."
##########    mkdir $TMPDIR
##########    echo "Verzeichnis \"$TMPDIR\" angelegt"
##########fi

chmod 777 $TMPDIR

# Sicherung der Datenbanken
##########for i in $DB_LIST; do
##########    echo "Datenbank \"$i\" wird gesichert."
##########    su postgres -c "pg_dump -o -O $i > $TMPDIR/$i.sql"
##########done

##########cp -R $TMPDIR/* /root/dasi-db/
# rsync -avzuPE /tmp/dasi-db/ /root/dasi-db



#######################################
# Loeschen und Neuerstellung der db's
#######################################

# Services neu starten
##########service apache2 restart
service postgresql restart
# ronny
# su postgres -c "dropdb auto-spar-dev-ronny"
# su postgres -c "createdb auto-spar-dev-ronny"
# su postgres -c "psql auto-spar-dev-ronny < $TMPDIR/auto-spar.sql"
# kevin
# su postgres -c "dropdb auto-spar-dev-kevin"
# su postgres -c "createdb auto-spar-dev-kevin"
# su postgres -c "psql auto-spar-dev-kevin < $TMPDIR/auto-spar.sql"
# jens
su postgres -c "dropdb auto-spar-dev-jens"
su postgres -c "createdb auto-spar-dev-jens"
su postgres -c "psql auto-spar-dev-jens < $TMPDIR/auto-spar.sql"

echo "Auftrag abgearbeitet!" # | mail -s "Datensicherung" info@auto-spar.de

exit 0
