# Utiliser l'image officielle MySQL comme base
FROM mysql:8.0

# Définir le mot de passe root (tu peux aussi définir d'autres variables d'environnement)
ENV MYSQL_ROOT_PASSWORD=Lawliet2004

# Copier ton fichier de dump SQL dans le répertoire d'initialisation de MySQL
COPY ./full_mysql_backup.sql /docker-entrypoint-initdb.d/

# Copier les fichiers de configuration MySQL dans le conteneur
COPY ./mysql-config/ /etc/mysql/

# Copier les données MySQL dans le répertoire des données de MySQL
COPY ./mysql-data/ /var/lib/mysql/

# Exposer le port MySQL
EXPOSE 3306

# Démarrer MySQL
CMD ["mysqld"]
