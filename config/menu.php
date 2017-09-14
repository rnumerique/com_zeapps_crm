<?php
defined('BASEPATH') OR exit('No direct script access allowed');




/********** CONFIG MENU ************/
$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_modalities" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = "Modalités" ;
$tabMenu["fa-icon"] = "credit-card-alt" ;
$tabMenu["url"] = "/ng/com_zeapps/modalities" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 40 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_taxes" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = "Taxes" ;
$tabMenu["fa-icon"] = "money" ;
$tabMenu["url"] = "/ng/com_zeapps/taxes" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 41 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_quotes" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = "Devis" ;
$tabMenu["fa-icon"] = "file-text" ;
$tabMenu["url"] = "/ng/com_zeapps/quote" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 45 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_orders" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = "Commandes" ;
$tabMenu["fa-icon"] = "file-text" ;
$tabMenu["url"] = "/ng/com_zeapps/order" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 46 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_deliveries" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = "Livraisons" ;
$tabMenu["fa-icon"] = "truck" ;
$tabMenu["url"] = "/ng/com_zeapps/delivery" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 47 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_invoices" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = "Factures" ;
$tabMenu["fa-icon"] = "credit-card" ;
$tabMenu["url"] = "/ng/com_zeapps/invoice" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 48 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_products" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = "Produits" ;
$tabMenu["fa-icon"] = "tags" ;
$tabMenu["url"] = "/ng/com_zeapps/produits" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 49 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_ze_apps_warehouses" ;
$tabMenu["space"] = "com_ze_apps_config" ;
$tabMenu["label"] = "Entrepôts" ;
$tabMenu["fa-icon"] = "building" ;
$tabMenu["url"] = "/ng/com_zeapps/warehouses" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 50 ;
$menuLeft[] = $tabMenu ;



/********** insert in left menu ************/
$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_quote" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Devis" ;
$tabMenu["fa-icon"] = "file-text" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/quote" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 3 ;
$menuLeft[] = $tabMenu ;


$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_order" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Commandes" ;
$tabMenu["fa-icon"] = "file-text" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/order" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 4 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_delivery" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Livraisons" ;
$tabMenu["fa-icon"] = "truck" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/delivery" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 5 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_invoice" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Factures" ;
$tabMenu["fa-icon"] = "credit-card" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/invoice" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 6 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_contract" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Contrats" ;
$tabMenu["fa-icon"] = "handshake-o" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/contract" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 7 ;
//$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_opportunity" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Opportunités" ;
$tabMenu["fa-icon"] = "search" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/opportunity" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 8 ;
//$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_product" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Produits" ;
$tabMenu["fa-icon"] = "tags" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/product" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 9 ;
$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_product_price" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Grille tarifaire" ;
$tabMenu["fa-icon"] = "tags" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/product_price" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 10 ;
//$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_stock" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Stocks" ;
$tabMenu["fa-icon"] = "cubes" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/stock" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 11 ;
$menuLeft[] = $tabMenu ;




$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_purchase" ;
$tabMenu["space"] = "com_ze_apps_purchase" ;
$tabMenu["label"] = "Achats" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/purchase" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 1 ;
//$menuLeft[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_delivery_receipt" ;
$tabMenu["space"] = "com_ze_apps_purchase" ;
$tabMenu["label"] = "Bon de réception" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/delivery_receipt" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 2 ;
//$menuLeft[] = $tabMenu ;


$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_purchase" ;
$tabMenu["space"] = "com_ze_apps_accounting" ;
$tabMenu["label"] = "Achats" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/purchase" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 1 ;
//$menuLeft[] = $tabMenu ;






/********** insert in top menu ************/
$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_quote" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Devis" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/quote" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 3 ;
$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_order" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Commandes" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/order" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 4 ;
$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_invoice" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Factures" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/invoice" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 5 ;
$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_contract" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Contrats" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/contract" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 6 ;
//$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_opportunity" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Opportunités" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/opportunity" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 7 ;
//$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_product" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Produits" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/product" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 8 ;
$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_product_price" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Grille tarifaire" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/product_price" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 9 ;
//$menuHeader[] = $tabMenu ;


$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_stock" ;
$tabMenu["space"] = "com_ze_apps_sales" ;
$tabMenu["label"] = "Stocks" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/stock" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 10 ;
$menuHeader[] = $tabMenu ;



$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_invoice" ;
$tabMenu["space"] = "com_ze_apps_purchase" ;
$tabMenu["label"] = "Achats" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/purchase" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 1 ;
//$menuHeader[] = $tabMenu ;

$tabMenu = array () ;
$tabMenu["id"] = "com_zeapps_crm_delivery_receipt" ;
$tabMenu["space"] = "com_ze_apps_purchase" ;
$tabMenu["label"] = "Bon de réception" ;
$tabMenu["url"] = "/ng/com_zeapps_crm/delivery_receipt" ;
$tabMenu["access"] = "com_zeapps_crm_read" ;
$tabMenu["order"] = 2 ;
//$menuHeader[] = $tabMenu ;
