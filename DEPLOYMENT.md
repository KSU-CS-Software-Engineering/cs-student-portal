# Deployment Configuration

1. Install Envoy globally using `composer global require "laravel/envoy=~1.0"`
2. Add `export PATH=$PATH:~/.composer/vendor/bin` to `~/.bashrc` so that envoy can be found by the system
3. Configure SSH keys on any systems that will be used for deployment. Generally it just needs the development system's public key added as an authorized key.
4. See `Envoy.blade.php` in the root directory of the web application for available tasks


## Setting Up Deployment Server

1. Install packages listed above
2. Configure git as above
3. Create a database user and database
4. Create directory structure
    1. /var/www/flowchart_releases
    2. /var/www/flowchart_data
        1. /var/www/flowchart_data/.env <-- Copy from repo sample or existing file
        2. /var/www/flowchart_data/app <-- Set owner to www-data:www-data
        3. /var/www/flowchart_data/logs <-- Set owner to www-data:www-data
5. Put server SSH key on github
6. Pull from git at least once to accept the key
7. Check settings in Envoy.blade.php
8. Deploy and test


## References

**Deploying with Envoy**
- https://serversforhackers.com/video/deploying-with-envoy-cast
- https://serversforhackers.com/video/enhancing-envoy-deployment
