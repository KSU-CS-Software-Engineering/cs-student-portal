# Development Environment

Follow these instructions to configure your development environment.

Latest release of Ubuntu LTS is a recommended development system. It is easiest to just set up a virtual machine with Ubuntu installed so you have a total control of your environment. If you want to use a different system, you may have to adjust these instructions to match your environment.

A familiarity with Ubuntu and Linux in general is assumed (things like running `sudo apt update` before installing packages).


## Before Cloning Repository


### Packages Required

Install the following packages and all dependencies:

1. `git`
2. Web server: `apache2` or `nginx`
3. Database: `mysql-server`
4. `php libapache2-mod-php ` for Apache, `php-fpm` for Nginx
5. PHP extensions required by Laravel: `php-mysql php-mbstring php-gd php-xml`
6. PHP extensions required by the application: `php-curl php-zip`
7. Composer:
    1. Follow the instructions at [getcomposer.org](https://getcomposer.org/download/) to install the composer
    2. Then, to install the composer globally (recommended), run `sudo mv composer.phar /usr/local/bin/composer`
8. `nodejs`
    1. Make sure you get the latest version, follow [this guide](https://github.com/nodesource/distributions/blob/master/README.md#debinstall)
9. `yarn`
    1. Follow the instructions here: https://yarnpkg.com/en/docs/install#debian-stable
10. [Adminer](https://www.adminer.org/en/ "Adminer.org") - Database management tool written in PHP
     - `sudo wget -O /var/www/html/adminer.php https://www.adminer.org/latest-mysql-en.php` will install the Adminer
     - To open it, just open your browser and into address bar enter `localhost/adminer.php`


### Configure git

1. Generate an SSH key pair using `ssh-keygen`
2. Upload the public key to your GitHub account
3. Configure git
    1. `git config --global user.name "John Doe"`
    2. `git config --global user.email johndoe@example.com`


## Clone Repository

We assume you have `www` directory inside your home directory

1. From the `www` directory, run `git clone git@github.com:KSU-CS-Software-Engineering/cs-student-portal.git`


## After Cloning Repository

1. Go into the `~/www/cs-student-portal` directory and run the following commands:
    1. `composer install`
    3. `yarn install`
    4. `yarn dev`
        - This will recompile all CSS, JS and asset files into the public folder


### App & Database Setup

This section will walk you through setting up the required database for the CS Student Portal system

1. From terminal run `mysql_secure_installation`
2. Then run

       sudo mysql << --end
       CREATE DATABASE cssp DEFAULT CHARACTER SET = utf8mb4 DEFAULT COLLATE = utf8mb4_unicode_520_ci;
       CREATE USER cssp@localhost IDENTIFIED BY 'cssp';
       GRANT ALL PRIVILEGES ON cssp.* TO cssp@localhost;
       FLUSH PRIVILEGES;
       --end

    to create user and database for the CS Student Portal
3. Create a copy of file `.env.example` named `.env`
4. Modify the settings in `.env` file as needed
    1. Database settings
        
           DB_DATABASE=cssp
           DB_USERNAME=cssp
           DB_PASSWORD=cssp

4. Run `php artisan key:generate` to generate a new random APP_KEY
5. Run `php artisan migrate --seed` to migrate and seed the database tables
6. Run `php artisan deploy:post` to fill other data to the tables


### Web server configuration

1. Run `sudo ln -s "$HOME/www/cs-student-portal" /var/www` to link the application to web server root directory

Then, follow the instructions below for your web server.


#### Apache

1. Copy `cs-student-portal.apache.conf` to `/etc/apache2/sites-available/cs-student-portal.conf`
2. Enable the site by doing `sudo a2ensite cs-student-portal`
3. Add `127.0.0.1  cs-student-portal.example` to `/etc/hosts`
4. Enable the apache `rewrite` module: `sudo a2enmod rewrite`
5. Restart the web server `sudo systemctl restart apache2`


#### Nginx

1. Copy `cs-student-portal.nginx.conf` to `/etc/nginx/sites-available/cs-student-portal.conf`
2. Enable the site by running `sudo ln -s ../sites-available/cs-student-portal.conf /etc/nginx/sites-enabled/cs-student-portal.conf`
3. Add `127.0.0.1  cs-student-portal.example` to `/etc/hosts` file
4. Restart the web server `sudo systemctl restart nginx`


## IDE

The recommended IDE is PHP Storm, but also code editors like VS Code or Atom can be used.

For better PHP Storm auto-completion run the following three commands in the project root

    php artisan ide-helper:generate
    php artisan ide-helper:model
    php artisan ide-helper:meta


## Unit Testing

Some very minor unit tests are available. To run the tests, execute `./vendor/bin/phpunit` in the main directory.


## Deployment

See [DEPLOYMENT.md](./DEPLOYMENT.md) for instructions on configuring the deployment


## References

**General Development Environment**
- https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-ubuntu-18-04
- https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-ubuntu-18-04
- https://help.ubuntu.com/community/SSH/OpenSSH/Keys
- https://git-scm.com/book/en/v2/Getting-Started-First-Time-Git-Setup

**Laravel documentation**
- https://laravel.com/docs

**Laravel 5.4 video tutorials**
- https://laracasts.com/series/laravel-from-scratch-2017

**Laravel 5.7 video tutorials**
- https://laracasts.com/series/laravel-from-scratch-2018

**Vue.js video tutorials**
- https://laracasts.com/series/learn-vue-2-step-by-step



## mozjpeg error command to fix
- wget -q -O /tmp/libpng12.deb http://mirrors.kernel.org/ubuntu/pool/main/libp/libpng/libpng12-0_1.2.54-1ubuntu1_amd64.deb \
  && sudo dpkg -i /tmp/libpng12.deb \
  && rm /tmp/libpng12.deb
