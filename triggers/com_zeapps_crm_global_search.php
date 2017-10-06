<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class com_zeapps_crm_global_search extends ZeCtrl
{
    public function execute($data = array()){
        $this->load->model('Zeapps_deliveries', 'deliveries');
        $this->load->model('Zeapps_invoices', 'invoices');
        $this->load->model('Zeapps_quotes', 'quotes');
        $this->load->model('Zeapps_orders', 'orders');
        $this->load->model('Zeapps_product_categories', 'categories');
        $this->load->model('Zeapps_product_products', 'products');
        $this->load->model('Zeapps_product_stocks', 'stocks');

        $return = array(
            "CRM" => []
        );

        if($quotes = $this->quotes->searchFor($data)){
            $return['CRM']["Devis"] = [];
            foreach($quotes as $quote){
                $return['CRM']["Devis"][] = array(
                    'label' => $quote->numerotation . " - " . $quote->libelle,
                    'url' => "/ng/com_zeapps_crm/quote/".$quote->id
                );
            }
        }

        if($orders = $this->orders->searchFor($data)){
            $return['CRM']["Commandes"] = [];
            foreach($orders as $order){
                $return['CRM']["Commandes"][] = array(
                    'label' => $order->numerotation . " - " . $order->libelle,
                    'url' => "/ng/com_zeapps_crm/order/".$order->id
                );
            }
        }

        if($deliveries = $this->deliveries->searchFor($data)){
            $return['CRM']["Livraisons"] = [];
            foreach($deliveries as $delivery){
                $return['CRM']["Livraisons"][] = array(
                    'label' => $delivery->numerotation . " - " . $delivery->libelle,
                    'url' => "/ng/com_zeapps_crm/delivery/".$delivery->id
                );
            }
        }

        if($invoices = $this->invoices->searchFor($data)){
            $return['CRM']["Factures"] = [];
            foreach($invoices as $invoice){
                $return['CRM']["Factures"][] = array(
                    'label' => $invoice->numerotation . " - " . $invoice->libelle,
                    'url' => "/ng/com_zeapps_crm/invoice/".$invoice->id
                );
            }
        }

        if($categories = $this->categories->searchFor($data)){
            $return['CRM']["Catégories de produits"] = [];
            foreach($categories as $category){
                $return['CRM']["Catégories de produits"][] = array(
                    'label' => $category->name,
                    'url' => "/ng/com_zeapps_crm/product/category/".$category->id
                );
            }
        }

        if($products = $this->products->searchFor($data)){
            $return['CRM']["Produits"] = [];
            foreach($products as $product){
                $return['CRM']["Produits"][] = array(
                    'label' => $product->ref . " - " . $product->name,
                    'url' => "/ng/com_zeapps_crm/product/".$product->id
                );
            }
        }

        if($stocks = $this->stocks->searchFor($data)){
            $return['CRM']["Stocks"] = [];
            foreach($stocks as $stock){
                $return['CRM']["Stocks"][] = array(
                    'label' => $stock->ref . " - " . $stock->label,
                    'url' => "/ng/com_zeapps_crm/stock/".$stock->id
                );
            }
        }

        return $return;
    }
}
