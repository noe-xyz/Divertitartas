# Divertitartas
## Trabajo de final de grado de Matías, Arantxa y Noelia

#### Se debe tener instalado:
  * php (activar en xampp apache y mysql)
  * composer (descargar composer-setup.exe)
  * symfony cli (desde la web oficial, descargar scoop y seguir las instrucciones)

1. Clonar el proyecto
   * Borrar cualquier carpeta creada de antes con el repositorio clonado
   * `git clone enlace_del_repositorio`
2. Instalar las dependencias del proyecto
   * `composer install`
3. Configurar el archivo .env(.dev.local) con la URL de la base de datos
   * `DATABASE_URL="mysql://usuario:contraseña@127.0.0.1:3306/Divertitartas"`
4. Inicializar base de datos
     * `php bin/console doctrine:database:create`
     * `php bin/console doctrine:migrations:migrate`
5. Inicializar el servidor
     * `symfony serve` ó `symfony serve -d` ó `symfony server:start`
6. Probar en el navegador
     * `localhost:8000`