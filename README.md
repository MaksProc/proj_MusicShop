This is a homework project. I made it in Symfony framework which I wasn't familiar with, as an exercise.

<h2>Installation</h2>

Project includes Docker settings and a database backup. Assuming Docker is installed and `localhost:8000` port is not occupied, open cmd in project folder and run:

```
docker-compose up --build
```
Project server should then be available at `localhost:8000` soon after container is created. However, its database will contain no example users or products. You may load the backup example data included in the repository using this command:

```
docker exec -i symfony_db psql -U symfony -d app_db < backup.sql
```

The included admin account's credentials are: `admin@oms.com 123`.

<h3>⚠ Security notice</h3>

This project includes an insecure `APP_SECRET` defined in `.env.dev` for demonstration purposes. 
This allows the project and database to work out of the box, however it is a security risk. **To secure it for public access, you must generate a secure app secret**.
<p>To generate a new secret:</p>

```
docker exec -i symfony_app php bin/console secrets:generate-keys
docker exec -i symfony_app php bin/console secrets:set APP_SECRET
```

This may, however, lock out pre-existing users.

<h2>Functionality</h2>

Project is a shop of musical instruments and accessories intended to show basic login and database connection functionality. 
A user may log in as either admin or client, with different functional interfaces available to both.

<h4>Shop index page</h4>

<p align="center">
  <img src="https://github.com/user-attachments/assets/df34a973-1ef5-4b3f-b095-f3ea8efdf010" width="720" height="450">
</p>
Features a simple name searchbar and a form for setting maximum and minimum desired price.

<h4>Example of admin dashboard interface</h4>

<p align="center">
  <img src="https://github.com/user-attachments/assets/9e6a149d-65b7-4497-bd8a-c3b088d0ba91" height="300">
</p>
Features multiple tabs and responsive UI. Clicking at any table row will load a form pre-filled with corresponding product's data; form submission will update and save the changes in the database.

<h4>Example of client basket interface</h4>

<p align="center">
  <img src="https://github.com/user-attachments/assets/6d699aaf-213d-4f00-b5d7-b51eb42712c4" height="270">
</p>
Table rows use Javascript's Fetch API to load and save data without page reload.

UI powered by Bootstrap.
Current product images are taken from Unsplash.
