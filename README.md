# ChargeGrid

Plataforma Laravel en español para catálogo y administración de estaciones de carga de vehículos eléctricos. La interfaz usa MXN para mostrar precios de catálogo.

## Requisitos

- PHP 8.2 o superior
- Composer
- SQLite para desarrollo; MySQL/MariaDB se puede configurar en `.env`

## Instalación local

```bash
git clone https://github.com/jh5057239-art/chargegrid-platform.git
cd chargegrid-platform
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Abre `http://127.0.0.1:8000`. Para ejecutar Laravel completo necesitas PHP y Composer en tu entorno o un servidor compatible.

## Cuenta administradora

1. Registra una cuenta desde `/register`.
2. En el servidor, ejecuta `php artisan chargegrid:make-admin TU_TELEFONO` y confirma la operación.
3. Cierra sesión y vuelve a entrar. El enlace de administración aparecerá en la barra inferior.

No se incluye contraseña predeterminada de administrador.

## Funciones iniciales

- Registro e inicio de sesión con teléfono o correo, contraseña y protección CSRF.
- Códigos de invitación generados al crear cuentas.
- Catálogo de estaciones de demostración cargado por el seeder.
- Solicitudes de depósito y retiro con revisión de estado.
- Gestión básica del catálogo y listado de usuarios.

## Límites importantes

Esta versión no procesa pagos, no transfiere dinero, no ofrece rendimientos de inversión ni actualiza el saldo al aprobar una solicitud. Los precios del catálogo son datos de muestra. Antes de producción se requiere implementar pruebas, auditoría, controles antifraude, libro contable transaccional, recuperación de contraseña, protección de cargas de archivos y revisión legal/compliance.

## Seguridad

No subas `.env`, contraseñas, tokens, claves privadas ni datos personales al repositorio. En producción establece `APP_DEBUG=false`, configura HTTPS y permisos correctos para `storage/` y `bootstrap/cache/`.
