#!/bin/sh
echo "### RSYNC ###"
rsync  -e 'ssh -X' -S -av ./ seonay@172.16.128.21:/var/www/AstrasoBDD  --include="public/uploads/images/ASTRASO.png" --include="public/uploads/images/default.jpg" --include="public/.htaccess" --include=".env"  --include=".env.vm.local" --exclude-from=".gitignore" --exclude=".*"

echo "### CONNECTION SSH ###"
ssh seonay@172.16.128.21 -o "StrictHostKeyChecking=no" <<'eof'

echo "### CD cd /var/www/Astraso ###"
cd /var/www/AstrasoBDD

echo "### RENOMMAGE .env.local ###"
mv .env.vm.local .env.local

echo "### COMPOSER INSTALL ###"
composer install

echo "### DOCTRINE MIGRATION ###"
symfony console --no-interaction doctrine:database:create --if-not-exists
symfony console --no-interaction doctrine:migrations:migrate

echo "### CHMOD public"
chmod 777 /var/www/AstrasoBDD/public/uploads/images/

