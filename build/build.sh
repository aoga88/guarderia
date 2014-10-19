#!/bin/bash

#git stash
#git pull --rebase
#/usr/local/zend/bin/php /usr/local/zend/bin/phpcs api
#/usr/local/zend/bin/php /usr/local/zend/bin/phpcs lib
#/usr/local/zend/bin/php /usr/local/zend/bin/phpcs public/js/ngapp
#/usr/local/zend/bin/php /usr/local/zend/bin/phpcs require.php
lastCommit=$(git log --format="%H" -n 1)
bower install --allow-root
/usr/local/zend/bin/php composer.phar update
mkdir public/$lastCommit
cp -r public/js/* public/$lastCommit/
#r.js -o build/app.build.js dir="public/$lastCommit"
echo $lastCommit > lastCommit.txt
