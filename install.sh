mv config/config.ini.backup config/config.ini

mkdir aliases/active/
mkdir aliases/inactive/
mkdir mail/outbox/
mkdir mail/sent/
mkdir mail/error/

chmod 777 aliases/active/
chmod 777 aliases/inactive/
chmod 777 mail/outbox/
chmod 777 mail/sent/
chmod 777 mail/error/

mkdir logs/
chmod 777 logs/
touch logs/email.log
chmod 777 logs/email.log

mkdir temp/
chmod 777 temp/

php composer.phar install
