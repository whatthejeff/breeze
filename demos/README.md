Demos
=====

Some demos have been included to help you get started with the Breeze Framwork.  For help installing and configuring these demos, see the `INSTALL.md` file.  For more information regarding how these demos work, see the `README.md` file in the root of the Breeze project.

blog
----

The blog demo is a basic blogging application that utilizes [Doctrine](http://www.doctrine-project.org/) for database access.  Run the following commands in the databases directory to generate the blog databases:

    touch blog.db
    php generate.php

You will also need to make sure that your web server user has write access to your database file and directory.  For a development environment,
the following commands should be sufficient.

    chmod a+w . blog.db

For more information on generating databases from schema files, read the following guide: http://www.doctrine-project.org/documentation/manual/1_2/en/introduction-to-models:generating-models:schema-files

hello_world
-----------

The hello world demo is one of the most basic examples of a Breeze application.  You'll find examples of routing with basic strings, routing with regular expressions, and using templates.

namespaces
----------

The namespaces directory contains versions of the two previous demos that have been altered to use techniques for protecting the global scope.  You can find more information about these techniques in the `README.md` file located in the root of the Breeze project.