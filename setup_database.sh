#!/bin/bash
cf=$(cat ./mysql_config.php | grep \$MYSQL_DNS)
IFS=: read -r x cf <<< "$cf"
IFS=\' read -r cf x <<< "$cf"
IFS=\; read -r host port dbname charset <<< "$cf"
IFS== read -r x host <<< "$host"
IFS== read -r x port <<< "$port"
IFS== read -r x dbname <<< "$dbname"
IFS== read -r x charset <<< "$charset"
user=$(cat ./mysql_config.php | grep \$MYSQL_USER)
IFS=\' read -r x user y <<< "$user"
password=$(cat ./mysql_config.php | grep \$MYSQL_PASSWORD)
IFS=\' read -r x password y <<< "$password"
mysql --user=$user --password=$password --database=$dbname --default-character-set=$charset --host=$host --port=$port < ./dump/accounts.sql
mysql --user=$user --password=$password --database=$dbname --default-character-set=$charset --host=$host --port=$port < ./dump/kanban.sql
mysql --user=$user --password=$password --database=$dbname --default-character-set=$charset --host=$host --port=$port < ./dump/membres.sql
mysql --user=$user --password=$password --database=$dbname --default-character-set=$charset --host=$host --port=$port < ./dump/colonnes.sql
mysql --user=$user --password=$password --database=$dbname --default-character-set=$charset --host=$host --port=$port < ./dump/taches.sql
