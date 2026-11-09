#!/bin/bash
# Exécuté par l'image mysql uniquement à la création du volume :
# crée la base de test utilisée par PHPUnit (dbname_suffix "_test" dans doctrine.yaml)
# et donne les droits à l'utilisateur applicatif.
set -e
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<SQL
CREATE DATABASE IF NOT EXISTS \`${MYSQL_DATABASE}_test\`;
GRANT ALL PRIVILEGES ON \`${MYSQL_DATABASE}\_test%\`.* TO '${MYSQL_USER}'@'%';
FLUSH PRIVILEGES;
SQL
