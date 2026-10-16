# ChargeGrid — PHP + MySQL (demostración)

Interfaz en español y MXN, adaptada a móviles y escritorio, para hosting compartido con PHP 8.1+ y MySQL/MariaDB (por ejemplo, Hostinger o InfinityFree, sujeto a las funciones habilitadas por el plan).

## Funciones incluidas
- Registro e inicio de sesión con contraseñas cifradas.
- Catálogo ilustrativo de estaciones de carga.
- Panel de usuario, saldo de demostración y código de invitación validado.
- Solicitudes internas de depósito/retiro de prueba.
- Panel administrativo para aprobar o rechazar solicitudes de prueba.
- Protección CSRF, consultas preparadas, salida HTML escapada y endurecimiento básico de sesión.

## Instalación
1. Crea una base MySQL y un usuario en el panel del hosting.
2. Importa schema.sql desde phpMyAdmin.
3. Copia config.example.php a config.php y rellena las credenciales reales del hosting. No subas ni publiques config.php en GitHub.
4. Sube los archivos a la raíz pública del dominio (public_html o la carpeta indicada por tu hosting).
5. Activa HTTPS en el hosting y abre el dominio.
6. Registra tu cuenta usando una contraseña de al menos 12 caracteres.
7. En phpMyAdmin, convierte tu cuenta en administradora ejecutando la consulta siguiente y reemplazando el correo por el tuyo:

   UPDATE users SET is_admin = 1 WHERE email = 'TU_CORREO_DE_REGISTRO';

8. Elimina config.example.php del servidor después de configurar el sistema. Conserva una copia de seguridad privada de la base de datos.

## Límites importantes
Este repositorio es una demostración funcional, NO una plataforma financiera lista para operar con dinero real. Los precios son ilustrativos; los depósitos/retiros son solicitudes internas y no mueven fondos. No hay pasarela de pago, transferencias, libro contable, verificación de identidad, controles antifraude, monitoreo ni auditoría independiente. No anuncies rendimientos ni recibas fondos reales con este código.

Antes de cualquier uso público: auditoría de seguridad independiente, pruebas de integración en el hosting elegido, límites de intentos de acceso, recuperación de cuenta segura, registro de auditoría, copias de seguridad probadas, monitoreo y revisión legal/regulatoria aplicable.

## Seguridad
- Usa HTTPS y credenciales únicas.
- No compartas contraseñas ni datos bancarios.
- Configura la base de datos con privilegios mínimos.
- Los errores técnicos se registran en el log del servidor; nunca publiques credenciales.
