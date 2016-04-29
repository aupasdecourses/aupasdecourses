#!/bin/sh

THEHOST="mp222483-001.privatesql"
THESITE="apdc"
THEDB="aupasdecln_apdc"
THEDBUSER="mp222483-ovh"
THEDBPW="Apennins38"
THEDATE=`date +%d%m%y%H%M`
THEPORT=35120

echo "Launch database dump ..."
mysqldump -h $THEHOST -u $THEDBUSER -p${THEDBPW} --port=${THEPORT} $THEDB | gzip > /homez.239/aupasdecln/save/dbbackup_${THEDB}_${THEDATE}.sql.gz
echo 'Database dump done!'
echo "Launch compression archive ..."
tar -czif /homez.239/aupasdecln/save/sitebackup_${THESITE}_${THEDATE}.tar.gz /homez.239/aupasdecln/www
echo "Compression done!"