# ChargeGrid en InfinityFree (prueba gratuita)

Esta rama prepara una versión de ChargeGrid para alojamiento compartido InfinityFree. La rama `main` y su despliegue Docker de Render no se modifican.

## Qué prepara el proyecto

- Reglas `.htaccess` para servir Laravel desde la carpeta `public/` aunque InfinityFree mantenga `htdocs` como raíz web.
- Dependencias PHP de producción mediante GitHub Actions.
- Una clave de aplicación generada durante la compilación y una plantilla `.env.infinityfree` con los campos de base de datos pendientes.
- Un volcado `database-infinityfree.sql` generado al ejecutar las migraciones y el seeder de estaciones de demostración.
- Un ZIP `chargegrid-infinityfree.zip` listo para descargar como artefacto de la acción.

## Paso 1: descargar el paquete desde Android

1. Abre [la acción de compilación](https://github.com/jh5057239-art/chargegrid-platform/actions/workflows/build-infinityfree.yml).
2. Abre la ejecución más reciente de la rama `infinityfree-deploy`.
3. Confirma que terminó en verde. Si falla, abre el paso que falló y consulta el mensaje de error.
4. En **Artifacts**, descarga `chargegrid-infinityfree`. Extrae el ZIP en tu teléfono con un gestor de archivos compatible.

La compilación se inicia automáticamente al actualizar esta rama. También puedes abrir la acción y pulsar **Run workflow**.

## Paso 2: crear el hosting y MySQL

1. Regístrate en [InfinityFree](https://www.infinityfree.com/) y crea un sitio con su subdominio gratuito.
2. Abre el panel de hosting y crea una base de datos MySQL.
3. Copia el host MySQL, el nombre de base de datos, el usuario y la contraseña exactos que muestra InfinityFree. No supongas que el host es `localhost`.

## Paso 3: configurar el archivo .env

En el paquete viene un archivo llamado `.env.infinityfree`. Haz una copia llamada `.env` y edítala antes de subirla:

- `APP_URL`: coloca la URL HTTPS exacta de tu subdominio.
- `DB_HOST`: host MySQL que indica el panel.
- `DB_DATABASE`: nombre completo de la base creada.
- `DB_USERNAME`: usuario MySQL.
- `DB_PASSWORD`: contraseña MySQL.

No cambies `APP_KEY` ni compartas el archivo `.env`. Mantén `APP_DEBUG=false`.

## Paso 4: importar las tablas

1. Abre phpMyAdmin desde el panel de InfinityFree.
2. Selecciona la base de datos que creaste.
3. Abre **Importar** y elige `database-infinityfree.sql` del ZIP.
4. Ejecuta la importación y comprueba que se crearon las tablas.

El volcado se genera desde las migraciones actuales y contiene estaciones de carga de demostración; no incluye usuarios reales ni saldos reales.

### Habilitar tu propia cuenta administradora (opcional)

1. Primero registra tu cuenta desde la página web.
2. En phpMyAdmin, abre la base de datos y la pestaña **SQL**.
3. Ejecuta la siguiente consulta sustituyendo el correo por el correo exacto que registraste:

   ```sql
   UPDATE users SET is_admin = 1 WHERE email = 'TU_CORREO_DE_REGISTRO';
   ```

4. Cierra sesión y vuelve a iniciar sesión. No uses una contraseña predeterminada ni compartas tus credenciales.

## Paso 5: subir los archivos

1. En el administrador de archivos o por FTP, abre `htdocs`.
2. Sube el contenido del ZIP extraído, incluida la carpeta `vendor/`. No subas el ZIP como un único archivo esperando que el servidor lo ejecute.
3. Sube el archivo `.env` que configuraste. Comprueba que el gestor de archivos muestra archivos ocultos.
4. Conserva la estructura completa de Laravel: `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `vendor/` y `artisan`.
5. Abre la URL HTTPS de tu subdominio y prueba la página principal, registro e inicio de sesión.

## Limitaciones y seguridad

- El despliegue no se publica automáticamente en tu cuenta InfinityFree: la cuenta, el subdominio y la base de datos debes crearlos tú.
- El paquete todavía requiere comprobarse en el hosting real; no declares el sitio operativo hasta probarlo.
- InfinityFree no permite terminal SSH ni ejecutar `php artisan` en el servidor. Por eso se preparan las dependencias y el SQL antes de subir.
- El alojamiento gratuito tiene límites de recursos y archivos. Esta configuración es para una demo visual, no para operar pagos, guardar dinero, procesar inversiones reales ni almacenar datos financieros sensibles.
- No habilites `APP_DEBUG=true` en el sitio público y no publiques el archivo `.env`.
- Si la compilación falla, el error puede indicar incompatibilidad de una migración o de una dependencia; corrige eso antes de importar.

## Archivos clave

- [Rama InfinityFree](https://github.com/jh5057239-art/chargegrid-platform/tree/infinityfree-deploy)
- [Acción para generar el ZIP](https://github.com/jh5057239-art/chargegrid-platform/actions/workflows/build-infinityfree.yml)
- [Archivo raíz .htaccess](https://github.com/jh5057239-art/chargegrid-platform/blob/infinityfree-deploy/.htaccess)
