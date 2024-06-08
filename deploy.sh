#!/bin/sh
echo "### RSYNC ###"
rsync  -e 'ssh -X' -S -av ./ kidoly@172.16.128.24:/var/www/Astraso  --include="public/uploads/images/ASTRASO.png" --include="public/uploads/images/default.jpg" --include="public/.htaccess" --include=".env"  --include=".env.vm.local" --exclude-from=".gitignore" --exclude=".*"

echo "### CONNECTION SSH ###"
ssh kidoly@172.16.128.24 -o "StrictHostKeyChecking=no" <<'eof'

echo "### CD cd /var/www/Astraso ###"
cd /var/www/Astraso

echo "### RENOMMAGE .env.local ###"
mv .env.vm.local .env.local

echo "### COMPOSER INSTALL ###"
composer install

echo "### DOCTRINE MIGRATION ###"
symfony console --no-interaction doctrine:database:create --if-not-exists
symfony console --no-interaction doctrine:migrations:migrate

echo "### CHMOD public"
chmod 777 /var/www/Astraso/public/uploads/images/

echo "### Add of reasons ###"
sudo mariadb
USE astraso;
INSERT INTO reason (id, name) VALUES (1, "Spam ou Robot");
INSERT INTO reason (id, name) VALUES (2, "Incitation au Suicide");
INSERT INTO reason (id, name) VALUES (3, "Harcèlement");
INSERT INTO reason (id, name) VALUES (4, "Images ou propos Choquant");

INSERT INTO user (id, image_id, institution_id, first_name, last_name, username, password, biography, created_at, roles, email, is_verified)
VALUES (
    1, 
    NULL, 
    NULL, 
    'Admin', 
    NULL, 
    'Admin', 
    '$2y$13$ZQPIdBswQRSOpM/OwowCru/VC.Dt7Y62h/RRBBc3qXeZGTR54KIEm', 
    NULL, 
    '2020-06-07 09:30:29', 
    '[]', 
    'admin@gmail.com', 
    0
);
UPDATE user SET roles = JSON_SET(roles, '$[0]', 'ROLE_ADMIN') WHERE id = 1;

INSERT INTO user (id, image_id, institution_id, first_name, last_name, username, password, biography, created_at, roles, email, is_verified)
VALUES (
    2, 
    NULL, 
    NULL, 
    'User', 
    NULL, 
    'User', 
    '$2y$13$ppAZmwJnEB3DXJZwB0InL.dR6GYY75i9xIwbW2ojzhvkpXZ/dwauK', 
    NULL, 
    '2020-06-07 09:30:29', 
    '[]', 
    'user@gmail.com', 
    0
);












INSERT INTO user (id, image_id, institution_id, first_name, last_name, username, password, biography, created_at, roles, email, is_verified)
VALUES (
    3, 
    NULL, 
    NULL, 
    'Jane', 
    'Doe', 
    'jane_doe', 
    '$2y$13$eQ5/uhF0s5R.B7DQHnclTuJ.aHIFhY1wM8b5ZyA1u4YQv5OB.jUtS', 
    'This is a dummy biography for Jane Doe.', 
    '2024-06-07 09:30:29', 
    '[]', 
    'jane.doe@example.com', 
    1
);


INSERT INTO user (id, image_id, institution_id, first_name, last_name, username, password, biography, created_at, roles, email, is_verified)
VALUES (
    3, 
    NULL, 
    NULL, 
    'Thomas', 
    'Try', 
    'thomas_try', 
    '$2y$13$eQ5/uhF0s5R.B7DQHnclTuJ.aHIFhY1wM8b5ZyA1u4YQv5OB.jUtS', 
    'This is a dummy biography for Thomas Try.', 
    '2023-06-07 09:30:29', 
    '[]', 
    'thomas.try@example.com', 
    1
);
