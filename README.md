# Divertitartas
## Trabajo de final de grado de Matías, Arantxa y Noelia

#### Se debe tener instalado (Windows):
  * Git Bash
  * PHP (activar en XAMPP Apache y MySql)
    * Asegurar que PHP existe como variable de entorno en el PATH.
  * [Composer](https://getcomposer.org/download/) (descargar composer-setup.exe desde su web)
  * [Symfony CLI](https://symfony.com/download) (desde la web oficial, descargar Scoop y seguir las instrucciones de ambos sitios)

1. Clonar el proyecto
   * Crear una carpeta donde guardar el repositorio clonado y acceder a ella desde la terminal
   * `git clone enlace_del_repositorio`
2. Instalar las dependencias del proyecto
   * `composer install`
3. Configurar tu propio archivo .env(.dev.local preferiblemente) con la URL de la base de datos
4. Inicializar base de datos
     * `php bin/console doctrine:database:create`
     * `php bin/console doctrine:migrations:migrate`
     * `php bin/console doctrine:schema:update --force`
     * `php bin/console cache:clear`
     * `php bin/console doctrine:cache:clear-metadata`
5. Inicializar el servidor
     * `symfony serve` ó `symfony serve -d` ó `symfony server:start`
6. Probar en el navegador
     * `localhost:8000`

Para más detalles: pincha [aquí](https://docs.google.com/document/d/18THmMerEXcmNi7mlvxPgBiQK4GZOW10Pl3b33ddkt5g/edit?usp=sharing)
