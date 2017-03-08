clea
====

A Symfony project created on November 14, 2016, 10:30 am.

=== INSTALL ===

--- Config DB ---
change name,user,password in config.yml

This project use:
 
--- Assetic ---

assets are in Ressources/Public

-> php bin/console assets:install

assets are imported

--- CID config ---
add cid array(courses_cid) in config.yml

courses_cid: //array
	- f3b69a4c-5e7d-47bb-9e1c-6abc704f629d //add cid to array
	- f3b69a4c-5e7d-47bb-9e1c-6abc705f629d
	- f3b69a4c-5e7d-47bb-9e1c-6abc706f629d

use to filter api request on cid choosen

--- FOS:JS-ROUTING

ajax is in Ressources/Public/js

comment new FOS\JsRoutingBundle\FOSJsRoutingBundle() in AppKernel,
comment fos_js_routing in config.yml,

Lunch
-> composer.phar update

uncomment new FOS\JsRoutingBundle\FOSJsRoutingBundle(),

-> php bin/console fos:js-routing:dump

debug: php bin/console fos:js-routing:debug

see all route managed

FOS:JS-ROUTING is installed


