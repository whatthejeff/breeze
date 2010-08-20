<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Hello World Demo</title>
  </head>
  <body>
    <h1>Hello <?php echo trim(ucwords(str_replace('/', ' ', htmlentities($name, ENT_QUOTES, 'UTF-8')))); ?>!</h1>
  </body>
</html>