#!/bin/sh

cd $2

if [ -z $1 ]; then
    echo 'Must set db name!'
    exit 1
fi

echo > api/runtime/logs/$1.lock
./yii migrate/up --interactive=0 --db=$1
echo $? > api/runtime/logs/$1.lock
