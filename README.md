# Divertitartas
Trabajo de final de grado de Matías, Arantxa y Noelia

Se debe tener instalado: 
  -php
  -composer
  -symfony cli
Clonar el proyecto 
  -git clone
Instalar las dependencias del proyecto
  -composer install
Configurar .env.dev(.local) con la url de la base de datos
Inicializar bbdd (?)
  -php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
     ó
  -php bin/console doctrine:schema:update --force
Inicializar el servidor
  -symfony serve
Probar en el navegador
  -localhost:8000
