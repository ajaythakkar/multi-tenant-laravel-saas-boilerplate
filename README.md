## Multi-tenant Laravel SaaS boilerplate

This repository puts together the most common building blocks necessary for developing a multi tenant SAAS application using Laravel, thus enabling users to jumpstart their SAAS application development with separate tenant databases.

It provides

- Register Page for new project with New Domain.
- Multi tenancy with separate databases

## Getting Started
This starter boilerplate is built on Laravel 6.2. Hence, working knowledge of Laravel is required.

### Installing
- Download the repository by cloning it or in zip format & extract to your project folder
- Run 'composer install' to install the packages used in this project.

```
composer install     
```
- Copy .env.example to .env & update the necessary parameters.
- Change your domain in .env `CENTRAL_DOMAIN = example.com` and add domain in `config/tenancy.php` in array of `exempt_domains` which is your main(central) domain.

## Package
- "stancl/tenancy": "^2.3"
<h3>Tenancy for Laravel</h3> 
<a href="https://tenancyforlaravel.com/docs/v2/getting-started/">Documentation</a>

## Server Setup
Setup domain with your project with creating virtual host.

<br><a href="https://www.digitalocean.com/community/tutorials/how-to-set-up-apache-virtual-hosts-on-ubuntu-18-04">Create Virtual Host</a> step by step

<br><br><b>Note : </b> cPanel it will not work as it will create dynamic database.

You have to add `*.` in `ServerAlias` to support dynamic subdomains.

```
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    ServerName example.com
    ServerAlias *.example.com
    VirtualDocumentRoot /var/www/example.com/public
    DocumentRoot /var/www/example.com/public
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

## License

This project is free for anyone who wishes to copy and replicate / customize for their needs as long as they adhere to the license terms & conditions of the packages included.
