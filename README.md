# ChargeGrid — PHP + MySQL

Plataforma demostrativa en español, MXN, con interfaz adaptable a móvil y escritorio. Preparada para hosting compartido que soporte PHP y MySQL, incluyendo Hostinger e InfinityFree.

## Incluye
- Registro e inicio de sesión con contraseñas cifradas.
- Catálogo ilustrativo de estaciones de carga.
- Panel de usuario, saldo de demostración y código de invitación.
- Solicitudes de depósito/retiro de prueba con estados.
- Panel administrativo para aprobar o rechazar solicitudes.
- Protección CSRF, consultas preparadas y salida HTML escapada.

## Instalación
1. En el hosting crea una base de datos MySQL y un usuario con permisos.
2. Importa `schema.sql` desde phpMyAdmin.
3. Copia `config.example.php` como `config.php` y completa host, base, usuario y contraseña.
4. Sube los archivos a la carpeta pública del sitio (`public_html` en Hostinger o `htdocs` en InfinityFree).
5. Abre el dominio y registra una cuenta.
6. En phpMyAdmin ejecuta: `UPDATE users SET is_admin = 1 WHERE email = 'TU_CORREO_DE_REGISTRO';` reemplazando el correo por el tuyo.

## Seguridad
- No compartas `config.php`, credenciales, contraseñas ni datos bancarios.
- Elimina `config.example.php` del servidor después de configurar el proyecto.
- Usa HTTPS.
- Los depósitos y retiros son solamente solicitudes internas de demostración: no se realizan pagos ni transferencias.
- El catálogo y los precios son ilustrativos. No hay inversiones ni rendimientos garantizados.
