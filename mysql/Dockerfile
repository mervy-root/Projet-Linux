FROM mysql:8.0

# Copie d’un script SQL de démarrage (facultatif)
COPY init.sql /docker-entrypoint-initdb.d/

# Expose le port MySQL (facultatif, le compose le gère)
EXPOSE 3306
