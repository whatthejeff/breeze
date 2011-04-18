INSTALLATION
============

The easiest way to install the Breeze Framework is with PEAR.

    pear channel-discover pear.breezephp.com
    pear install breeze/Breeze-beta

If you get a permissions error, you will need to use `su`, `sudo`, or contact your server administrator for help.

### Manual installation

To install the Breeze Framework without PEAR, simply add the `lib` directory to your PHP `include_path`.  You can find information about the PHP `include_path` configuration directive here:

<http://www.php.net/manual/en/ini.core.php#ini.include-path>

Instructions on how to change PHP configuration directives can be found here:

<http://www.php.net/manual/en/configuration.changes.php>

BINARIES
--------

The Breeze Framework comes with a binary called `breeze` which can be used to generate a working project skeleton.  If you used the manual installation option, you will need to add the `bin` directory to your `PATH` environmental variable and ensure that you have executable privileges.  Afterwards, you can do the following:

    $ breeze myapp
    Your project was successfully created!
    $ find myapp
    myapp
    myapp/controller.php
    myapp/public
    myapp/public/index.php
    myapp/views
    myapp/views/index.php
    myapp/views/layout.php

You can also use the `breeze` binary to generate a more complex project structure.

    $ breeze --type complex myapp
    Your project was successfully created!
    $ find myapp
    myapp
    myapp/bootstrap.php
    myapp/controllers
    myapp/controllers/Index.php
    myapp/helpers
    myapp/models
    myapp/public
    myapp/public/index.php
    myapp/views
    myapp/views/index.php
    myapp/views/layout.php

These project structures are only suggestions.  You should feel free to structure and configure your projects as you deem fit.

SERVER CONFIGURATIONS
---------------------

Unless you intend to run your entire application from a single file (which, by the way, breeze is great at), you'll likely need some special server configurations to get your Breeze application working as expected.

**NOTE**: Your exact configurations will depend on your specific application.

### Apache

    RewriteEngine on
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule .* /index.php

### nginx

    try_files $uri $uri/ /index.php;

    location ~* \.php$ {
      fastcgi_pass   127.0.0.1:8888;

      fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
      fastcgi_param  QUERY_STRING     $query_string;
      fastcgi_param  REQUEST_METHOD   $request_method;
      fastcgi_param  CONTENT_TYPE     $content_type;
      fastcgi_param  CONTENT_LENGTH   $content_length;
      fastcgi_param  SERVER_SOFTWARE  nginx;
      fastcgi_param  SCRIPT_NAME      $fastcgi_script_name;
      fastcgi_param  REQUEST_URI      $request_uri;
      fastcgi_param  DOCUMENT_URI     $document_uri;
      fastcgi_param  DOCUMENT_ROOT    $document_root;
      fastcgi_param  SERVER_PROTOCOL  $server_protocol;
      fastcgi_param  REMOTE_ADDR      $remote_addr;
      fastcgi_param  REMOTE_PORT      $remote_port;
      fastcgi_param  SERVER_ADDR      $server_addr;
      fastcgi_param  SERVER_PORT      $server_port;
      fastcgi_param  SERVER_NAME      $server_name;
    }

### lighttpd

    # Note that you will need to parse the query string manually
    # if you use this method.
    #
    # See: http://redmine.lighttpd.net/wiki/lighttpd/FrequentlyAskedQuestions#Whatkindofenvironmentdoesserver.error-handler-404setup
    server.error-handler-404 = "/index.php

### IIS

    [ISAPI_Rewrite]

    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]

SYSTEM REQUIREMENTS
-------------------

The Breeze Framework requires PHP 5.3 or later.

GETTING STARTED
---------------

See `README.md`