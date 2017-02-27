app.config(['$provide',
    function ($provide) {
        $provide.decorator('zeHttp', function($delegate){
            var zeHttp = $delegate;

            zeHttp.crm = {
                modality : {
                    get : get_modality,
                    get_all : getAll_modality,
                    save : post_modality,
                    del : del_modality
                },
                taxe : {
                    get : get_taxe,
                    get_all : getAll_taxe,
                    save : post_taxe,
                    del : del_taxe
                },
                invoice : {
                    get : get_invoice,
                    get_all : getAll_invoice,
                    save : post_invoice,
                    del : del_invoice,
                    finalize : finalize_invoice,
                    test : test_invoice,
                    line : {
                        save : save_line_invoice,
                        position : update_linepos_invoice,
                        del : del_line_invoice
                    },
                    activity : {
                        save : save_activity_invoice,
                        del : del_activity_invoice
                    },
                    document : {
                        upload : url_document_invoice,
                        del : del_document_invoice
                    },
                    pdf : {
                        get : get_pdf_invoice,
                        make : make_pdf_invoice
                    }
                },
                order : {
                    get : get_order,
                    get_all : getAll_order,
                    save : post_order,
                    del : del_order,
                    test : test_order,
                    line : {
                        save : save_line_order,
                        position : update_linepos_order,
                        del : del_line_order
                    },
                    activity : {
                        save : save_activity_order,
                        del : del_activity_order
                    },
                    document : {
                        upload : url_document_order,
                        del : del_document_order
                    },
                    pdf : {
                        get : get_pdf_order,
                        make : make_pdf_order
                    }
                },
                quote : {
                    get : get_quote,
                    get_all : getAll_quote,
                    save : post_quote,
                    del : del_quote,
                    test : test_quote,
                    line : {
                        save : save_line_quote,
                        position : update_linepos_quote,
                        del : del_line_quote
                    },
                    activity : {
                        save : save_activity_quote,
                        del : del_activity_quote
                    },
                    document : {
                        upload : url_document_quote,
                        del : del_document_quote
                    },
                    pdf : {
                        get : get_pdf_quote,
                        make : make_pdf_quote
                    }
                },
                product : {
                    get : get_product,
                    get_all : getAll_product,
                    getOf : get_products_of,
                    save : save_product,
                    del : delete_product
                },
                category : {
                    tree : get_categories_tree,
                    get : get_category,
                    save : save_category,
                    update_order : update_category_order,
                    del : delete_category,
                    openTree : recursiveOpening
                },
                product_stock : {
                    get : get_product_stock,
                    get_all : getAll_product_stock,
                    save : save_product_stock,
                    del : delete_product_stock
                }
            };

            zeHttp.config = angular.extend(zeHttp.config || {}, {
                product : {
                    get : {
                        attr : get_product_attr
                    }
                },
                invoice : {
                    get : {
                        frequency: get_invoice_freq,
                        format: get_invoice_format
                    }
                },
                quote : {
                    get : {
                        frequency: get_quote_freq,
                        format: get_quote_format
                    }
                },
                order : {
                    get : {
                        frequency: get_order_freq,
                        format: get_order_format
                    }
                }
            });

            return zeHttp;

            // MODALITY
            function get_modality(id){
                return zeHttp.get('/com_zeapps_crm/modalities/get/' + id);
            }
            function getAll_modality(){
                return zeHttp.get('/com_zeapps_crm/modalities/getAll/');
            }
            function post_modality(data){
                return zeHttp.post('/com_zeapps_crm/modalities/save', data);
            }
            function del_modality(id){
                return zeHttp.delete('/com_zeapps_crm/modalities/delete/' + id);
            }

            // TAXE
            function get_taxe(id){
                return zeHttp.get('/com_zeapps_crm/taxes/get/' + id);
            }
            function getAll_taxe(){
                return zeHttp.get('/com_zeapps_crm/taxes/getAll/');
            }
            function post_taxe(data){
                return zeHttp.post('/com_zeapps_crm/taxes/save', data);
            }
            function del_taxe(id){
                return zeHttp.delete('/com_zeapps_crm/taxes/delete/' + id);
            }


            // INVOICE
            function test_invoice(data){
                return zeHttp.post('/com_zeapps_crm/invoices/testFormat', data);
            }
            function get_invoice(id){
                return zeHttp.get('/com_zeapps_crm/invoices/get/' + id);
            }
            function getAll_invoice(id_project, type){
                id_project = id_project || 0;
                type = type || '';
                return zeHttp.get('/com_zeapps_crm/invoices/getAll/' + id_project + '/' + type);
            }
            function post_invoice(data){
                return zeHttp.post('/com_zeapps_crm/invoices/save', data);
            }
            function del_invoice(id){
                return zeHttp.delete('/com_zeapps_crm/invoices/delete/' + id);
            }
            function finalize_invoice(id){
                return zeHttp.get('/com_zeapps_crm/invoices/finalizeInvoice/' + id);
            }
            function save_line_invoice(data){
                return zeHttp.post('/com_zeapps_crm/invoices/saveLine', data);
            }
            function update_linepos_invoice(data){
                return zeHttp.post('/com_zeapps_crm/invoices/updateLinePosition/', data);
            }
            function del_line_invoice(id){
                return zeHttp.delete('/com_zeapps_crm/invoices/deleteLine/' + id);
            }
            function save_activity_invoice(data){
                return zeHttp.post('com_zeapps_crm/invoices/saveActivity', data);
            }
            function del_activity_invoice(id){
                return zeHttp.post('com_zeapps_crm/invoices/deleteActivity/' + id);
            }
            function url_document_invoice(){
                return '/com_zeapps_crm/invoices/uploadDocuments/';
            }
            function del_document_invoice(id){
                return zeHttp.post('/com_zeapps_crm/invoices/deleteDocument/' + id);
            }
            function get_pdf_invoice(){
                return '/com_zeapps_crm/invoices/getPDF/';
            }
            function make_pdf_invoice(id){
                return zeHttp.post('/com_zeapps_crm/invoices/makePDF/' + id);
            }


            // ORDER
            function test_order(data){
                return zeHttp.post('/com_zeapps_crm/orders/testFormat', data);
            }
            function get_order(id){
                return zeHttp.get('/com_zeapps_crm/orders/get/' + id);
            }
            function getAll_order(id_project, type){
                id_project = id_project || 0;
                type = type || '';
                return zeHttp.get('/com_zeapps_crm/orders/getAll/' + id_project + '/' + type);
            }
            function post_order(data){
                return zeHttp.post('/com_zeapps_crm/orders/save', data);
            }
            function del_order(id){
                return zeHttp.delete('/com_zeapps_crm/orders/delete/' + id);
            }
            function save_line_order(data){
                return zeHttp.post('/com_zeapps_crm/orders/saveLine', data);
            }
            function update_linepos_order(data){
                return zeHttp.post('/com_zeapps_crm/orders/updateLinePosition/', data);
            }
            function del_line_order(id){
                return zeHttp.delete('/com_zeapps_crm/orders/deleteLine/' + id);
            }
            function save_activity_order(data){
                return zeHttp.post('com_zeapps_crm/orders/saveActivity', data);
            }
            function del_activity_order(id){
                return zeHttp.post('com_zeapps_crm/orders/deleteActivity/' + id);
            }
            function url_document_order(){
                return '/com_zeapps_crm/orders/uploadDocuments/';
            }
            function del_document_order(id){
                return zeHttp.post('/com_zeapps_crm/orders/deleteDocument/' + id);
            }
            function get_pdf_order(){
                return '/com_zeapps_crm/orders/getPDF/';
            }
            function make_pdf_order(id){
                return zeHttp.post('/com_zeapps_crm/orders/makePDF/' + id);
            }


            // QUOTE
            function test_quote(data){
                return zeHttp.post('/com_zeapps_crm/quotes/testFormat', data);
            }
            function get_quote(id){
                return zeHttp.get('/com_zeapps_crm/quotes/get/' + id);
            }
            function getAll_quote(id_project, type){
                id_project = id_project || 0;
                type = type || '';
                return zeHttp.get('/com_zeapps_crm/quotes/getAll/' + id_project + '/' + type);
            }
            function post_quote(data){
                return zeHttp.post('/com_zeapps_crm/quotes/save', data);
            }
            function del_quote(id){
                return zeHttp.delete('/com_zeapps_crm/quotes/delete/' + id);
            }
            function save_line_quote(data){
                return zeHttp.post('/com_zeapps_crm/quotes/saveLine', data);
            }
            function update_linepos_quote(data){
                return zeHttp.post('/com_zeapps_crm/quotes/updateLinePosition/', data);
            }
            function del_line_quote(id){
                return zeHttp.delete('/com_zeapps_crm/quotes/deleteLine/' + id);
            }
            function save_activity_quote(data){
                return zeHttp.post('com_zeapps_crm/quotes/saveActivity', data);
            }
            function del_activity_quote(id){
                return zeHttp.post('com_zeapps_crm/quotes/deleteActivity/' + id);
            }
            function url_document_quote(){
                return '/com_zeapps_crm/quotes/uploadDocuments/';
            }
            function del_document_quote(id){
                return zeHttp.post('/com_zeapps_crm/quotes/deleteDocument/' + id);
            }
            function get_pdf_quote(){
                return '/com_zeapps_crm/quotes/getPDF/';
            }
            function make_pdf_quote(id){
                return zeHttp.post('/com_zeapps_crm/quotes/makePDF/' + id);
            }


            // PRODUCT
            function get_product(id){
                return zeHttp.get('/com_zeapps_crm/product/get/'+id);
            }
            function getAll_product(){
                return zeHttp.get('/com_zeapps_crm/product/getAll');
            }
            function get_products_of(id){
                return zeHttp.get('/com_zeapps_crm/product/getProductsOf/'+id);
            }
            function save_product(data){
                return zeHttp.post('/com_zeapps_crm/product/save', data);
            }
            function delete_product(id){
                return zeHttp.post('/com_zeapps_crm/product/delete/'+id);
            }


            // CATEGORIES
            function get_categories_tree(){
                return zeHttp.get('/com_zeapps_crm/categories/get_tree');
            }
            function get_category(id){
                return zeHttp.get('/com_zeapps_crm/categories/get/'+id);
            }
            function save_category(data){
                return zeHttp.post('/com_zeapps_crm/categories/save', data);
            }
            function update_category_order(data){
                return zeHttp.post('/com_zeapps_crm/categories/update_order', data);
            }
            function delete_category(id, force){
                if(force === undefined)
                    return zeHttp.post('/com_zeapps_crm/categories/delete/'+id);
                else if(force)
                    return zeHttp.post('/com_zeapps_crm/categories/delete/'+id+'/true');
                else
                    return zeHttp.post('/com_zeapps_crm/categories/delete/'+id+'/false');

            }
            function recursiveOpening(branch, id){
                if(angular.isArray(branch.branches)){
                    for(var i = 0; i < branch.branches.length; i++){
                        if(recursiveOpening(branch.branches[i], id)){
                            branch.open = true;
                            return true;
                        }
                    }
                }
                return branch.id == id;
            }


            // PRODUCT STOCKS
            function get_product_stock(id){
                return zeHttp.get('/com_zeapps_crm/stock/get/'+id);
            }
            function getAll_product_stock(id){
                id = id || '';
                return zeHttp.get('/com_zeapps_crm/stock/getAll/' + id);
            }
            function save_product_stock(data){
                return zeHttp.post('/com_zeapps_crm/stock/save', data);
            }
            function delete_product_stock(id){
                return zeHttp.post('/com_zeapps_crm/stock/delete/'+id);
            }


            // CONFIG
            function get_invoice_freq(){
                return zeHttp.get('/zeapps/config/get/crm_invoice_frequency');
            }
            function get_invoice_format(){
                return zeHttp.get('/zeapps/config/get/crm_invoice_format');
            }
            function get_quote_freq(){
                return zeHttp.get('/zeapps/config/get/crm_quote_frequency');
            }
            function get_quote_format(){
                return zeHttp.get('/zeapps/config/get/crm_quote_format');
            }
            function get_order_freq(){
                return zeHttp.get('/zeapps/config/get/crm_order_frequency');
            }
            function get_order_format(){
                return zeHttp.get('/zeapps/config/get/crm_order_format');
            }
            function get_product_attr(){
                return zeHttp.get('/zeapps/config/get/crm_product_attributes');
            }
        });
    }]);