# WebApp

This is the main web application where the domain points and people access. It communicates with the Email and Doc Service and is written in PHP on the CodeIgniter Framework.

The frontend is a custom Bootstrap css framework. All of the JavaScript is injected into a page on the fly via PHP helpers. Pdf files are generated by an internal library.

This application is a MVC (Model, View, Controller) application. This means that the within the Application directory, the Model files are libraries for Database Access, the View files are the frontend, and the Controller files glue the two together.

If running on Apache, the following .httaccess file will need to be included:
RewriteEngine On
RewriteCond $1 !^(index\.php|img|css|js|robots\.txt)
RewriteRule ^(.*)$ /index.php/$1 [L]

<h2>AWS Setup Notes</h2>
To have a working Database connection between a Beanstalk and EC2 you will need to do the following:

Change the DB config settings to:
$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = $_SERVER['RDS_HOSTNAME'];
$db['default']['username'] = $_SERVER['RDS_USERNAME'];
$db['default']['password'] = $_SERVER['RDS_PASSWORD'];
$db['default']['database'] = $_SERVER['RDS_DB_NAME'];
$db['default']['port'] = $_SERVER['RDS_PORT'];
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

Then set the environmental variables for your Beanstalk instance to the ones listed above.

In addition, it is important to have both the EC2 instances and RDS in the Same Security Group. For more information see:
https://docs.aws.amazon.com/elasticbeanstalk/latest/dg/AWSHowTo.RDS.html?icmpid=docs_elasticbeanstalk_console
