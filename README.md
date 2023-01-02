SIMPLE API WITH PHP OOP

IMPORTANT INFOS:

1 - THE PROJECT ITSELF IS IN THE APP FOLDER

2 - PROJECT INCLUDES DOCKER CONTAINERS WITH ALL ENVIRONMENT
CONFIGURED, 

3 - SERVICES NECESSARY FOR PROJECT OPERATION :
    - NGINX - LATEST
    - PHP 8.1
    - MYSQL 8.1

4 - DATABASE SETTINGS CAN BE MODIFIED IN APP/MODEL/CONNECTION.PHP FILE

5 - IN THE ENDPOINTS FOLDER THERE IS A FILE TO IMPORT THE ROUTES IN POSTMAN

6 -OBVIOUSLY BECAUSE IT IS A TRIAL APPLICATION SOME PRECAUTIONS WITH SECURITY HAVE NOT BEEN TAKEN NOR ALL PREVENTIONS AGAINST SQL INJECTION

CONFIGURATION

- INSIDE THE PROJECT FOLDER RUN IN THE TERMINAL DOCKER-COMPOSE UP -D --BUILD TO UP THE SERVICES
- IN THE DATABASE MANAGER IF IT DOES NOT EXIST, CREATE THE php_db BASE AND IMPORT THE SCRIPT FROM THE .DATABASE FOLDER TO CREATE THE TABLES
- IMPORTANT: CHOOSING NOT TO USE DOCKER YOU WILL NEED TO CONFIGURE NGINX/APACHE FOR DUE REDIRECTING, EXAMPLE BELOW :

    location / {
        try_files $uri $uri/ /index.php?$query_string;
  }
- OPTIONALLY CONFIGURE VHOST WITH ADDRESS http://php-oop.crud