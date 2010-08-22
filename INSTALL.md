INSTALLATION
============

The easiest way to install the Breeze Framework is with PEAR.

    pear channel-discover pear.breezephp.com
    pear install breeze/Breeze-beta

If you get a permissions error, you will need to use `su`, `sudo`, or contact your server administrator for help.

### Manual installation

To install the Breeze Framework without PEAR, simply add the Breeze directory to your PHP `include_path`.  You can find information about the PHP `include_path` configuration directive here:

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

### IIS

    [ISAPI_Rewrite]

    RepeatLimit 20
    RewriteRule (?!\.(js|ico|gif|jpg|png|css|swf))$ index.php

### lighttpd

    url.rewrite-once = (
        ".*\.(js|ico|gif|jpg|png|css|)$" => "$0",
        "^/.*(\?.*)" => "/index.php$1",
        "" => "/index.php"
    )

SYSTEM REQUIREMENTS
-------------------

The Breeze Framework requires PHP 5.3 or later.

GETTING STARTED
---------------

See `README.mkd`