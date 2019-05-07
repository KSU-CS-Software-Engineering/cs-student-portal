# Development environment setup

Follow these instructions to configure your development environment.

Latest release of Ubuntu LTS (currently 18.04) is a recommended development system.
It is easiest to just set up a virtual machine with Ubuntu installed so you have a total control of your environment.

If you want to use a different system, you may have to adjust these instructions
to match your environment. Also make sure that your system meets the requirements
specified in [README][readme].

A familiarity with elementary Linux and Ubuntu commands is assumed (e.g. `cd`, `cp`, `mv`, `sudo`, `apt`).


## Before cloning the repository


### Install required packages

Install the following packages and all their dependencies:

1. `git`
2. Web server: `apache2` or `nginx`
3. Database server: `mysql-server`
4. PHP
    - For Apache install `php libapache2-mod-php`
    - For Nginx server install `php-fpm`
5. PHP extensions required by Laravel: `php-mysql php-mbstring php-xml`
6. PHP extensions required by the application: `php-curl`
7. Composer:
    1. Follow the instructions at [Composer's website][composer] to install the Composer
    2. Then, to install the Composer globally (recommended), run
        ```sh
        sudo mv composer.phar /usr/local/bin/composer
        ```
8. `curl` – used for installing `nodejs` and `yarn`
9. `nodejs`
    - To make sure you get the latest version, follow
      [this guide][nodesourse]
10. `yarn` – Follow the instructions at [Yarn's website][yarn]
11. [Adminer][adminer] – Database management tool written in PHP
    - To install the latest version, run:
        ```sh
        sudo wget -O '/var/www/html/adminer.php' 'https://www.adminer.org/latest-mysql-en.php'
        ``` 
    - To run the Adminer, just open your browser and into address bar enter `localhost/adminer.php`


### Configure Git

1. Generate an SSH key pair by running
    ```sh
    ssh-keygen
    ```
2. Upload the public key to your [GitHub account][github-add-ssh]
3. Configure Git
    1. name:
        ```sh
        git config --global user.name "John Doe"
        ```
    2.  and e-mail:
        ```sh
        git config --global user.email johndoe@example.com
        ```


## Clone the repository

1. In your home directory, crate a new directory called `www`
    ```sh
    mkdir -p "$HOME/www" && cd "$HOME/www"
    ```
2. From the `www` directory, run 
    ```sh
    git clone git@github.com:KSU-CS-Software-Engineering/cs-student-portal.git
    ```


## After cloning the repository

Inside the `~/www/cs-student-portal` directory run the following commands:
```sh
composer install
yarn install
yarn dev # This will compile all CSS, JS and asset files into the public folder
```


### App & Database setup

This section will walk you through setting up the required database for the CS Student Portal system

1. From terminal run 
    ```sh
    sudo mysql_secure_installation
    ```
2. Then run
    ```
    sudo mysql << --end
    CREATE DATABASE cssp DEFAULT CHARACTER SET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_520_ci;
    CREATE USER cssp@localhost IDENTIFIED BY 'cssp';
    GRANT ALL PRIVILEGES ON cssp.* TO cssp@localhost;
    FLUSH PRIVILEGES;
    --end
    ```
    to create user and database for the CS Student Portal
3. Create a copy of file `.env.example` named `.env`
4. Modify the settings in the `.env` file as needed
    - Database settings:
        ```
        DB_DATABASE=cssp
        DB_USERNAME=cssp
        DB_PASSWORD=cssp
        ```

5. Run
    ```sh
    php artisan key:generate
    ```
    to generate a new random APP_KEY
6. Run
    ```sh
    php artisan migrate --seed
    ```
    to migrate and seed the database tables
7. Run
    ```sh
    php artisan deploy:post
    ```
    to fill other data to the tables


### Web server configuration

1. Run
    ```sh
    sudo ln -s "$HOME/www/cs-student-portal" /var/www
    ```
    to link the application to web server root directory
2. To allow the web server a write access to the storage directory, run the following three commands:
    ```sh
    find storage -type d -exec setfacl -m d:u:www-data:rwX {} \;
    find storage -type d -exec setfacl -m u:www-data:rwx {} \;
    find storage -type f -exec setfacl -m u:www-data:rw {} \;
    ```

Then, follow the instructions below for your web server.


#### Apache

1. Copy `cs-student-portal.apache.conf` file form the root of the project to Apache configuration directory `/etc/apache2/sites-available`
    ```sh
    sudo cp "$HOME/www/cs-student-portal/cs-student-portal.apache.conf" /etc/apache2/sites-available/cs-student-portal.conf
    ```
2. Enable the site by running
    ```sh
    sudo a2ensite cs-student-portal
    ```
3. Add `127.0.0.1  cs-student-portal.example` as a new line to `/etc/hosts` file
    ```sh
    echo '127.0.0.1  cs-student-portal.example' | sudo tee -a /etc/hosts
    ```
4. Enable the apache `rewrite` module:
    ```sh
    sudo a2enmod rewrite
    ```
5. Restart the web server
    ```sh
    sudo systemctl restart apache2
    ```


#### Nginx

1. Copy `cs-student-portal.nginx.conf` file form the root of the project to Nginx configuration directory `/etc/nginx/sites-available`
    ```sh
    sudo cp "$HOME/www/cs-student-portal/cs-student-portal.nginx.conf" /etc/nginx/sites-available/cs-student-portal.conf
    ```
2. Enable the site by running
    ```sh
    sudo ln -s ../sites-available/cs-student-portal.conf /etc/nginx/sites-enabled/cs-student-portal.conf
    ```
3. Add `127.0.0.1  cs-student-portal.example` as a new line to `/etc/hosts` file
    ```sh
    echo '127.0.0.1  cs-student-portal.example' | sudo tee -a /etc/hosts
    ```
4. Restart the web server
    ```sh
    sudo systemctl restart nginx
    ```


## IDE

The recommended IDE is [PhpStorm][phpstorm],
but code editors like [VS Code][vscode]
or [Atom][atom] can also be used.

For better PhpStorm auto-completion run the following three commands in the project root:
```sh
php artisan ide-helper:generate
php artisan ide-helper:model
php artisan ide-helper:meta
```

## Unit Testing

Some very minor unit tests are available. To run the tests, execute
```sh
./vendor/bin/phpunit
```
from the project root directory.


## Deployment

See [DEPLOYMENT.md][deployment-guide] for instructions on configuring the deployment


## References

**General Development Environment**
- https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-ubuntu-18-04
- https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-ubuntu-18-04
- https://help.ubuntu.com/community/SSH/OpenSSH/Keys
- https://git-scm.com/book/en/v2/Getting-Started-First-Time-Git-Setup

**Laravel documentation**
- https://laravel.com/docs/5.4
- https://laravel.com/api/5.4/

**Laravel 5.4 video tutorials**
- https://laracasts.com/series/laravel-from-scratch-2017

**Laravel 5.7 video tutorials**
- https://laracasts.com/series/laravel-from-scratch-2018

**Vue.js video tutorials**
- https://laracasts.com/series/learn-vue-2-step-by-step

[composer]: https://getcomposer.org/download/ 'Composer'
[nodesourse]: https://github.com/nodesource/distributions/blob/master/README.md#debinstall 'NodeSource – GitHub'
[yarn]: https://yarnpkg.com/en/docs/install#debian-stable 'Yarn installation guide'
[adminer]: https://www.adminer.org/en/ 'Adminer.org'
[github-add-ssh]: https://github.com/settings/ssh/new 'Add SSH keys – GitHub'
[phpstorm]: https://www.jetbrains.com/phpstorm/ 'Jetbrains – PhpStorm'
[vscode]: https://code.visualstudio.com 'Visual Studio Code'
[atom]: https://atom.io 'Atom'

[deployment-guide]: ./DEPLOYMENT.md 'Deployment guide'
[readme]: ./README.md#system-requirements 'Readme'
