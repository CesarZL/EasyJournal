# Proyecto de Estadías: Formateador de Documentos LaTeX

Este proyecto es una aplicación web desarrollada con Laravel y PHP, que utiliza Google Gemini para formatear documentos LaTeX.

## Descripción

La aplicación permite a los usuarios subir sus documentos LaTeX y los formatea automáticamente utilizando Google Gemini. Esto facilita la creación de documentos LaTeX de alta calidad, ahorrando tiempo y esfuerzo.

## Tecnologías Utilizadas

- Laravel: Un marco de trabajo de PHP para el desarrollo web.
- PHP: Un lenguaje de programación de uso general que es especialmente adecuado para el desarrollo web.
- Google Gemini: Una herramienta de Google que ayuda a formatear el documento.


## Tareas a realizar:

1. Realizar una revisión de las necesidades del usuario y establecer los requisitos del sistema
2. Investigar y estudiar las tecnologías disponibles para el desarrollo web y el procesamiento de documentos tex
3. Analizar las herramientas y frameworks más adecuados para implementar la aplicación web, tomando en cuenta la escalabilidad y la eficiencia
4. Crear esquemas de base de datos para almacenar las plantillas, la información de los coautores y los documentos generados por el sistema
5. Diseñar la arquitectura de la aplicación, definiendo las relaciones entre los distintos componentes
6. Utilizar herramientas para diseñar la interfaz de usuario, teniendo en cuenta la experiencia del usuario y la facilidad de uso
7. Configurar el entorno de desarrollo y establecer un flujo de trabajo eficiente para el desarrollo del proyecto
8. Implementar las funcionalidades para que los usuarios puedan cargar sus plantillas y gestionar a sus coautores
9. Desarrollar un sistema de procesamiento de documentos tex, integrando herramientas y bibliotecas adecuadas para esta tarea
10. Realizar pruebas unitarias y de integración para garantizar el funcionamiento correcto de las funcionalidades implementadas
11. Identificar y corregir errores y fallos en el código, asegurando la estabilidad y seguridad del sistema
12. Realizar pruebas de rendimiento para optimizar el tiempo de respuesta de la aplicación y su capacidad de manejar grandes volúmenes de datos
13. Configurar un servidor en Digital Ocean para alojar la aplicación web
14. Implementar medidas de seguridad, como certificados SSL y firewalls, para proteger la plataforma de posibles amenazas
15. Realizar pruebas finales en el entorno de producción para asegurar que la aplicación funciona correctamente antes de publicarla
16. Preparar documentación detallada sobre el funcionamiento de la aplicación, incluyendo guías de usuario y manuales técnicos
17. Entregar el proyecto finalizado, asegurándose de que cumple con todos los requisitos y expectativas establecidas inicialmente

## Propósito del Proyecto:

Desarrollar una aplicación web que permita el procesamiento de documentos tex para agilizar el proceso de escritura académica y profesional. La aplicación estará diseñada para que los usuarios puedan cargar sus propias plantillas, gestionar a sus coautores y generar documentos a partir de un archivo base, todo ello integrado en un entorno web con el fin de automatizar la creación de artículos y documentos académicos

## Descripción del Proyecto:

Tiene como objetivo principal simplificar y optimizar el proceso de redacción de documentos tex, especialmente en el ámbito académico y profesional. Los usuarios podrán registrarse en la plataforma y cargar sus propias plantillas de documentos tex, que pueden incluir formatos estándar para artículos científicos, informes técnicos, tesis, entre otros.
La aplicación proporcionará herramientas para gestionar las contribuciones de cada coautor, facilitando así el trabajo en equipo y la coordinación de esfuerzos. Contará con un sistema de procesamiento de documentos tex, que permitirá a los usuarios generar automáticamente versiones completas de sus documentos a partir de un archivo base y los datos proporcionados en la plantilla. Esto incluirá la inserción dinámica de texto, gráficos y tablas según lo requiera el usuario.
La aplicación estará desarrollada utilizando el framework de PHP Laravel para el backend y tecnologías web modernas para el frontend, como lo es Tailwind CSS, Editor.js, etc. Asegurando un rendimiento óptimo y una experiencia de usuario fluida. Se desplegará en un servidor de Digital Ocean para garantizar la accesibilidad y la escalabilidad de la plataforma.


## Despliegue de Proyecto en Digital Ocean con phpMyAdmin

### Clonar Repositorio y Configurar Apache

1. En el directorio `/var/www`, clonar el repositorio y realizar ajustes:
    ```bash
    git clone URL_REPO
    cd /var/www
    sudo rm -rf html  # Borrar la carpeta html si existe
    mv nombre_repo html  # Cambiar el nombre de la carpeta del repositorio clonado a html
    ```

2. Cambiar a la rama main:
    ```bash
    git checkout main
    ```

3. Habilitar el módulo rewrite de Apache:
    ```bash
    sudo a2enmod rewrite
    ```

4. Modificar las reglas de rutas de Apache en `/etc/apache2/apache2.conf`:
    ```
    Buscar "AllowOverride" y cambiar de "None" a "All" dentro de <Directory /var/www/>
    ```

5. Configurar el Virtual Host en `/etc/apache2/sites-enabled/000-default.conf`:
    ```
    DocumentRoot /var/www/html/public
    ```

6. Reiniciar Apache:
    ```bash
    sudo service apache2 restart
    ```

### Permisos y Actualizaciones del Sistema

1. Configurar permisos en la carpeta del proyecto:
    ```bash
    sudo chmod -R 775 /var/www/html/storage
    sudo chmod -R 775 /var/www/html/bootstrap/cache
    sudo chown -R $USER:www-data /var/www/html/storage
    sudo chown -R $USER:www-data /var/www/html/bootstrap/cache
    ```

2. Actualizar el sistema:
    ```bash
    sudo apt update && sudo apt upgrade -y
    ```

### Instalación y Configuración de PHP 8.3

1. Agregar el repositorio PPA de Ondrey Sury:
    ```bash
    sudo add-apt-repository ppa:ondrej/php
    ```

2. Actualizar los repositorios:
    ```bash
    sudo apt update
    ```

3. Instalar PHP 8.3 y sus módulos necesarios:
    ```bash
    sudo apt install php8.3 php8.3-mysql php8.3-intl php8.3-curl php8.3-mbstring php8.3-xml php8.3-zip php8.3-ldap php8.3-gd php8.3-bz2 php8.3-sqlite3 php8.3-redis
    ```

4. Deshabilitar PHP 8.1 y Habilitar PHP 8.3:
    ```bash
    sudo a2dismod php8.1
    sudo a2enmod php8.3
    ```

5. Reiniciar Apache:
    ```bash
    sudo systemctl restart apache2
    ```

### Instalar composer y Dependencias de Laravel

Para instalar Composer y las dependencias de Laravel, sigue estos pasos:

1. Actualiza los paquetes del sistema operativo ejecutando el siguiente comando en la terminal:
    ```
    sudo apt-get update
    ```

2. Descarga el instalador de Composer ejecutando el siguiente comando en la terminal:
    ```
    curl -sS https://getcomposer.org/installer -o composer-setup.php
    ```

3. Instala Composer en el directorio `/usr/local/bin` y configura el nombre del archivo como `composer` ejecutando el siguiente comando en la terminal:
    ```
    sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    ```

4. Actualiza Composer a la última versión ejecutando el siguiente comando en la terminal:
    ```
    sudo composer self-update
    ```

### Instalar mysql-server

Para instalar mysql-server, sigue los siguientes pasos:

1. Actualiza los paquetes del sistema operativo ejecutando el siguiente comando en la terminal:
    ```
    sudo apt update
    ```

2. Instala mysql-server ejecutando el siguiente comando en la terminal:
    ```
    sudo apt-get install mysql-server
    ```

Para solucionar el error "Access denied for user 'root'@'localhost'":

1. Accede a MySQL como usuario root ejecutando el siguiente comando en la terminal:
    ```
    sudo mysql -u root -p
    ```

2. Muestra las bases de datos ejecutando el siguiente comando en la terminal:
    ```
    show databases;
    ```

3. Selecciona la base de datos `mysql` ejecutando el siguiente comando en la terminal:
    ```
    use mysql;
    ```

4. Actualiza el plugin del usuario root ejecutando el siguiente comando en la terminal:
    ```
    update user set plugin='mysql_native_password' where user='root';
    ```

5. Actualiza los privilegios ejecutando el siguiente comando en la terminal:
    ```
    flush privileges;
    ```

Finalmente, para acceder a MySQL como usuario root, ejecuta el siguiente comando en la terminal:
```
mysql -u root -p
```


### Configuración Adicional y Preparación del Entorno

1. Limpiar la caché de Laravel:
    ```bash
    
    php artisan cache:clear;php artisan config:clear;php artisan route:clear;php artisan view:clear;
    
    ```

2. Configurar permisos para la carga de imágenes con Intervention:
    ```bash
    sudo chmod -R 775 /var/www/html/public/uploads
    sudo chown -R $USER:www-data /var/www/html/public/uploads
    ```

3. Instalar npm y Node.js:
    ```bash
    sudo apt-get update
    sudo apt-get install npm
    ```

4. Actualizar Node.js a la última versión:
    ```bash
    npm install n --global
    n latest
    ```

5. Instalar dependencias del proyecto y compilar:
    ```bash
    cd /var/www/html
    npm install
    npm run build
    ```

6. Configurar el archivo `.env` con la información de la base de datos.

7. Configurar la base de datos en phpMyAdmin (http://ip-sitio/phpmyadmin):
    - Crear base de datos: `nombre_de_env`
    - Importar backup si es necesario

8. Verificar las rutas:
    ```bash
    php artisan route:list
    ```

### Puesta en Producción

1. Cambiar `APP_DEBUG` a false en el archivo `.env`.

2. Compilar los assets si no se ha hecho previamente:
    ```bash
    npm run build
    ```