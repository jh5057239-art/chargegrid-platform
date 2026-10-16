# Despliegue de ChargeGrid en InfinityFree

Esta rama agrega los archivos de reescritura y una plantilla de entorno para probar ChargeGrid en alojamiento compartido InfinityFree. No cambia la rama `main` ni la configuración Docker de Render.

## Limitaciones importantes

- InfinityFree no ofrece terminal SSH ni permite ejecutar Composer o `php artisan` en el servidor. Las dependencias de Laravel deben prepararse antes de subir el proyecto.
- El repositorio no incluye `vendor/` ni `composer.lock`; subir únicamente el contenido de GitHub no basta para arrancar Laravel.
- Debes usar MySQL/MariaDB creado desde el panel de InfinityFree; SQLite de Render no es apropiado para este despliegue.
- Las migraciones y los datos de demostración deben prepararse fuera del servidor e importarse con phpMyAdmin. No hay que exponer una ruta web pública que ejecute migraciones.
- El alojamiento gratuito tiene límites de archivos y recursos. Este despliegue es solo para pruebas, no para gestionar dinero real ni datos financieros sensibles.

## Archivos añadidos

- `.htaccess` en la raíz: envía las solicitudes a `public/` y evita exponer directamente archivos internos.
- `public/.htaccess`: reglas de rutas de Laravel.
- `.env.infinityfree.example`: plantilla de variables para MySQL. Copia su contenido a un archivo llamado `.env` y completa los valores en tu entorno de hosting. Nunca subas el `.env` real a GitHub.

## Preparación requerida antes de subir

En un entorno con PHP compatible y Composer, dentro de la carpeta del proyecto:

1. Instala dependencias de producción: `composer install --no-dev --prefer-dist --no-interaction`.
2. Genera una clave segura para la aplicación con `php artisan key:generate` en un entorno local configurado; no reutilices claves publicadas.
3. Prepara las migraciones y un volcado SQL para la base MySQL. Revisa los datos de demostración antes de exportarlos.
4. No incluyas `node_modules/`, archivos `.env`, claves, logs ni datos reales.
5. Sube el proyecto completo preparado, incluida la carpeta `vendor/`, al directorio `htdocs` mediante el administrador de archivos o FTP.

## Configuración en InfinityFree

1. Crea una cuenta de hosting y un subdominio gratuito en [InfinityFree](https://www.infinityfree.com/).
2. En el panel, crea una base de datos MySQL. Copia el host, nombre de base, usuario y contraseña exactos que muestra el panel; el host normalmente no es `localhost`.
3. Copia `.env.infinityfree.example` a `.env` y configura `APP_KEY`, `APP_URL` y las variables `DB_*`. Usa `APP_DEBUG=false` en el sitio público.
4. Importa el SQL preparado en phpMyAdmin.
5. Sube los archivos preparados a `htdocs`. Mantén la estructura del proyecto: `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `vendor/` y `artisan`.
6. Comprueba primero la página principal. Si aparece HTTP 500, revisa la configuración PHP y los registros disponibles; no dejes `APP_DEBUG=true` en el sitio público.

## Almacenamiento público

InfinityFree no admite enlaces simbólicos de Laravel. La regla de `.htaccess` raíz intenta servir archivos de `storage/app/public/` bajo la ruta `/storage/`. No guardes documentos privados ni pruebas de identidad en esa ubicación.

## Estado

Esta rama prepara la configuración de rutas y variables, pero no puede generar automáticamente `vendor/`, crear tu base de datos remota ni publicar el sitio en tu cuenta InfinityFree. Eso requiere preparar el paquete y configurar el panel de hosting.
