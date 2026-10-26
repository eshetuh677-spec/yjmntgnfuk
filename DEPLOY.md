# EFPT_AMS Deployment Guide (Ubuntu 22.04+)

This `DEPLOY.md` contains exact commands and configuration placeholders for deploying EFPT_AMS to a fresh Ubuntu server.
Replace placeholders like `<YOUR_DOMAIN>`, `<DB_USER>`, `<DB_PASS>`, `<GIT_REPO_URL>` before running commands.

---

## 1) System user & updates

Create an admin user and update system:

```bash
# create user
sudo adduser deployer
sudo usermod -aG sudo deployer
# login as that user and update
ssh deployer@<SERVER_IP>
sudo apt update && sudo apt upgrade -y
```

## 2) Install LAMP stack & utilities

```bash
sudo apt install -y apache2 mariadb-server php php-mysql php-xml php-mbstring php-zip libapache2-mod-php git unzip certbot python3-certbot-apache ufw fail2ban
```

If you prefer PHP-FPM:

```bash
sudo apt install -y php-fpm libapache2-mod-fcgid
sudo a2enmod proxy_fcgi setenvif
sudo a2enconf php*-fpm
```

## 3) Database setup

Log into MariaDB and create DB & user (replace placeholders):

```bash
sudo mysql -u root -p
# In MySQL shell:
CREATE DATABASE efpt_ams CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '<DB_USER>'@'localhost' IDENTIFIED BY '<DB_PASS>';
GRANT ALL PRIVILEGES ON efpt_ams.* TO '<DB_USER>'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Import schema (from repo):

```bash
# on server, assuming repo will be at /var/www/efpt_ams
    mysql -u <DB_USER> -p efpt_ams < /var/www/efpt_ams/database/sqlSchemaAll.sql
```

## 4) Deploy application files

Clone your repo and set permissions (edit repo URL):

```bash
sudo mkdir -p /var/www
cd /var/www
sudo git clone <GIT_REPO_URL> efpt_ams
sudo chown -R www-data:www-data /var/www/efpt_ams
sudo find /var/www/efpt_ams -type d -exec chmod 750 {} \;
sudo find /var/www/efpt_ams -type f -exec chmod 640 {} \;
sudo chmod -R 770 /var/www/efpt_ams/uploads
```

## 5) Secure `config.php` (recommended)

Move `config.php` outside webroot and symlink it:

```bash
sudo mkdir -p /var/www/config
sudo mv /var/www/efpt_ams/includes/config.php /var/www/config/config.php
sudo ln -s /var/www/config/config.php /var/www/efpt_ams/includes/config.php
sudo chown root:www-data /var/www/config/config.php
sudo chmod 640 /var/www/config/config.php
```

Edit `/var/www/config/config.php` and set DB credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', '<DB_USER>');
define('DB_PASS', '<DB_PASS>');
define('DB_NAME', 'efpt_ams');
```

## 6) Apache virtual host

Create `/etc/apache2/sites-available/efpt_ams.conf` with this content (replace `<YOUR_DOMAIN>`):

```apache
<VirtualHost *:80>
    ServerName <YOUR_DOMAIN>
    ServerAlias www.<YOUR_DOMAIN>

    DocumentRoot /var/www/efpt_ams
    <Directory /var/www/efpt_ams>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/efpt_ams_error.log
    CustomLog ${APACHE_LOG_DIR}/efpt_ams_access.log combined
</VirtualHost>
```

Enable site and modules:

```bash
sudo a2enmod rewrite headers
sudo a2ensite efpt_ams.conf
sudo systemctl reload apache2
```

## 7) Obtain TLS certificate (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d <YOUR_DOMAIN> -d www.<YOUR_DOMAIN>
# follow prompts to install certificate
```

Test auto-renewal:

```bash
sudo certbot renew --dry-run
```

## 8) PHP hardening

Edit `/etc/php/<version>/apache2/php.ini` (replace `<version>`, e.g. `8.1`):

```ini
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
memory_limit = 256M
upload_max_filesize = 16M
post_max_size = 18M
date.timezone = "UTC"
```

Restart Apache:

```bash
sudo systemctl restart apache2
```

## 9) Block execution in uploads directory

Create `.htaccess` in `/var/www/efpt_ams/uploads`:

```
Options -Indexes
<FilesMatch "\\.php$">
    Require all denied
</FilesMatch>
```

(If using nginx, configure similar rules in your site config.)

## 10) Firewall & Fail2Ban

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Apache Full'
sudo ufw enable
sudo apt install -y fail2ban
# optionally tune /etc/fail2ban/jail.local
```

## 11) Create initial admin user (on server)

Option A — use the included CLI script:

```bash
cd /var/www/efpt_ams
sudo -u www-data php scripts/create_admin.php --username=admin --password='ChangeMe123!' --full='System Administrator'
```

Option B — SQL insert (if you generated a password hash locally):

```sql
INSERT INTO users (username, password, role, full_name, status) VALUES ('admin','<HASH>','admin','System Administrator','active');
```

## 12) Backups (example cron)

Use a `.my.cnf` for safe DB auth in backup scripts. Example cron entry to run daily at 02:00:

Create `/root/.my.cnf` with secure perms (or store in non-root):

```ini
[client]
user=<DB_USER>
password="<DB_PASS>"
```

Cron job (`/etc/cron.d/efpt_backup`):

```
0 2 * * * root mysqldump efpt_ams > /var/backups/efpt_ams_$(date +\%F).sql
0 3 * * * root tar -czf /var/backups/efpt_ams_uploads_$(date +\%F).tar.gz /var/www/efpt_ams/uploads
```

## 13) Log rotation

Ensure `/etc/logrotate.d/apache2` handles Apache logs; create a small rotate for PHP errors if you enabled `/var/log/php_errors.log`.

## 14) Post-deploy checks

- Visit: https://<YOUR_DOMAIN>/ and confirm site loads
- Login with the admin user
- Upload a document and a signature to confirm `uploads` permissions
- Generate Reports → Export CSV
- Run `sudo certbot renew --dry-run` and check renewal

---

## Notes & placeholders
- `<YOUR_DOMAIN>` — your DNS must point to the server IP
- `<DB_USER>` / `<DB_PASS>` — database credentials
- `<GIT_REPO_URL>` — the git clone URL of your project

If you want, I can also:
- create the `/etc/apache2/sites-available/efpt_ams.conf` file for you in the repo,
- add the `.htaccess` into `uploads/`, and
- add a `docker-compose.yml` for a Docker-based deployment.
