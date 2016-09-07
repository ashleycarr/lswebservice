#/bin/bash
echo
echo ------------------------------------
echo Testing API Request Handler
echo ------------------------------------
phpunit request.php
echo
echo ------------------------------------
echo Testing Professional DB lookup
echo ------------------------------------
phpunit lookup.php
echo
echo ------------------------------------
echo Testing Google Geocode module
echo ------------------------------------
phpunit geocode.php
