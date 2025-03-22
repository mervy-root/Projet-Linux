FROM node:18 AS build

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers package.json et package-lock.json (si présents)
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier tout le reste du code source
COPY . .

# Construire l'application Angular pour la production
RUN npm run build --prod

# Étape 2 : Servir l'application avec un serveur léger comme Nginx
FROM nginx:alpine

# Copier le fichier de configuration nginx personnalisé
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copier les fichiers construits par Angular dans le répertoire de Nginx
COPY --from=build /app/dist/transport-app/browser /usr/share/nginx/html

# Exposer le port par défaut de Nginx
EXPOSE 80

# Lancer Nginx
CMD ["nginx", "-g", "daemon off;"]

