
.htaccess
solve no green padlock problem:
RewriteCond %{HTTP_HOST} bitscharity\.com [NC]
RewriteCond %{SERVER_PORT} 80
RewriteRule ^(.*)$ https://bitscharity.com/$1 [R,L]

hide file extentions (this works on subdirectories):
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([^\.]+)$ $1.php [NC,L]

hide file extentions (this does not work on subdirectories):
RewriteEngine On
RewriteRule   ^([A-Za-z0-9-]+)/?$  $1.php

~~~~~~~~~
Do you want know what's the orange color from Bitcoin logo? Here's the colours:

Hexadecimal:
#FF9900

RGB
(255,153,0)
~~~~~~~~~~~~

qG5WDj*pyct8#FcHmXk^$WBZSNsVp-pG+8jHZ*m+r@Abb!35_f

qG5WDj*pyct8#FcH

BITS bits
Playfair+Display:700
Nunito+Sans:700
Mukta+Malar:700



