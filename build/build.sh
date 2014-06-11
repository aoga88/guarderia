#!/usr/bin/bash

/usr/local/zend/bin/php /usr/local/zend/bin/phpcs api
/usr/local/zend/bin/php /usr/local/zend/bin/phpcs lib
/usr/local/zend/bin/php /usr/local/zend/bin/phpcs public/js/ngapp
/usr/local/zend/bin/php /usr/local/zend/bin/phpcs require.php
lastCommit=$(git log --format="%H" -n 1)
bower install --allow-root
/usr/local/zend/bin/php composer.phar update
#r.js -o public/js/build.js dir=public/$lastCommit
