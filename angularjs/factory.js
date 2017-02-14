app.config(['$provide',
    function ($provide) {
        $provide.decorator('zeHttp', function($delegate){
            var zeHttp = $delegate;

            // MODALITY
            var get_modality = function(id){
                return zeHttp.get('/com_zeapps_crm/modalities/get/' + id)
            };
            var getAll_modality = function(){
                return zeHttp.get('/com_zeapps_crm/modalities/getAll/');
            };
            var post_modality = function(data){
                return zeHttp.post('/com_zeapps_crm/modalities/save', data);
            };
            var del_modality = function(id){
                return zeHttp.delete('/com_zeapps_crm/modalities/delete/' + id);
            };

            // TAXE
            var get_taxe = function(id){
                return zeHttp.get('/com_zeapps_crm/taxes/get/' + id)
            };
            var getAll_taxe = function(){
                return zeHttp.get('/com_zeapps_crm/taxes/getAll/');
            };
            var post_taxe = function(data){
                return zeHttp.post('/com_zeapps_crm/taxes/save', data);
            };
            var del_taxe = function(id){
                return zeHttp.delete('/com_zeapps_crm/taxes/delete/' + id);
            };


            // INVOICE
            var test_invoice = function(data){
                return zeHttp.post('/com_zeapps_crm/invoices/testFormat', data);
            };
            var get_invoice = function(id){
                return zeHttp.get('/com_zeapps_crm/invoices/get/' + id)
            };
            var getAll_invoice = function(id_project){
                id_project = id_project || 0;
                return zeHttp.get('/com_zeapps_crm/invoices/getAll/' + id_project);
            };
            var post_invoice = function(data){
                return zeHttp.post('/com_zeapps_crm/invoices/save', data);
            };
            var del_invoice = function(id){
                return zeHttp.delete('/com_zeapps_crm/invoices/delete/' + id);
            };
            var finalize_invoice = function(id){
                return zeHttp.get('/com_zeapps_crm/invoices/finalizeInvoice/' + id)
            };
            var save_line_invoice = function(data){
                return zeHttp.post('/com_zeapps_crm/invoices/saveLine', data);
            };
            var update_linepos_invoice = function(data){
                return zeHttp.post('/com_zeapps_crm/invoices/updateLinePosition/', data);
            };
            var del_line_invoice = function(id){
                return zeHttp.delete('/com_zeapps_crm/invoices/deleteLine/' + id);
            };
            var save_activity_invoice = function(data){
                return zeHttp.post('com_zeapps_crm/invoices/saveActivity', data);
            };
            var del_activity_invoice = function(id){
                return zeHttp.post('com_zeapps_crm/invoices/deleteActivity/' + id);
            };
            var url_document_invoice = function(){
                return '/com_zeapps_crm/invoices/uploadDocuments/';
            };
            var del_document_invoice = function(id){
                return zeHttp.post('/com_zeapps_crm/invoices/deleteDocument/' + id);
            };
            var get_pdf_invoice = function(){
                return '/com_zeapps_crm/invoices/getPDF/';
            };
            var make_pdf_invoice = function(id){
                return zeHttp.post('/com_zeapps_crm/invoices/makePDF/' + id);
            };


            // ORDER
            var test_order = function(data){
                return zeHttp.post('/com_zeapps_crm/orders/testFormat', data);
            };
            var get_order = function(id){
                return zeHttp.get('/com_zeapps_crm/orders/get/' + id)
            };
            var getAll_order = function(id_project){
                id_project = id_project || 0;
                return zeHttp.get('/com_zeapps_crm/orders/getAll/' + id_project);
            };
            var post_order = function(data){
                return zeHttp.post('/com_zeapps_crm/orders/save', data);
            };
            var del_order = function(id){
                return zeHttp.delete('/com_zeapps_crm/orders/delete/' + id);
            };
            var save_line_order = function(data){
                return zeHttp.post('/com_zeapps_crm/orders/saveLine', data);
            };
            var update_linepos_order = function(data){
                return zeHttp.post('/com_zeapps_crm/orders/updateLinePosition/', data);
            };
            var del_line_order = function(id){
                return zeHttp.delete('/com_zeapps_crm/orders/deleteLine/' + id);
            };
            var save_activity_order = function(data){
                return zeHttp.post('com_zeapps_crm/orders/saveActivity', data);
            };
            var del_activity_order = function(id){
                return zeHttp.post('com_zeapps_crm/orders/deleteActivity/' + id);
            };
            var url_document_order = function(){
                return '/com_zeapps_crm/orders/uploadDocuments/';
            };
            var del_document_order = function(id){
                return zeHttp.post('/com_zeapps_crm/orders/deleteDocument/' + id);
            };
            var get_pdf_order = function(){
                return '/com_zeapps_crm/orders/getPDF/';
            };
            var make_pdf_order = function(id){
                return zeHttp.post('/com_zeapps_crm/orders/makePDF/' + id);
            };


            // QUOTE
            var test_quote = function(data){
                return zeHttp.post('/com_zeapps_crm/quotes/testFormat', data);
            };
            var get_quote = function(id){
                return zeHttp.get('/com_zeapps_crm/quotes/get/' + id)
            };
            var getAll_quote = function(id_project){
                id_project = id_project || 0;
                return zeHttp.get('/com_zeapps_crm/quotes/getAll/' + id_project);
            };
            var post_quote = function(data){
                return zeHttp.post('/com_zeapps_crm/quotes/save', data);
            };
            var del_quote = function(id){
                return zeHttp.delete('/com_zeapps_crm/quotes/delete/' + id);
            };
            var save_line_quote = function(data){
                return zeHttp.post('/com_zeapps_crm/quotes/saveLine', data);
            };
            var update_linepos_quote = function(data){
                return zeHttp.post('/com_zeapps_crm/quotes/updateLinePosition/', data);
            };
            var del_line_quote = function(id){
                return zeHttp.delete('/com_zeapps_crm/quotes/deleteLine/' + id);
            };
            var save_activity_quote = function(data){
                return zeHttp.post('com_zeapps_crm/quotes/saveActivity', data);
            };
            var del_activity_quote = function(id){
                return zeHttp.post('com_zeapps_crm/quotes/deleteActivity/' + id);
            };
            var url_document_quote = function(){
                return '/com_zeapps_crm/quotes/uploadDocuments/';
            };
            var del_document_quote = function(id){
                return zeHttp.post('/com_zeapps_crm/quotes/deleteDocument/' + id);
            };
            var get_pdf_quote = function(){
                return '/com_zeapps_crm/quotes/getPDF/';
            };
            var make_pdf_quote = function(id){
                return zeHttp.post('/com_zeapps_crm/quotes/makePDF/' + id);
            };


            // PRODUCT
            var get_product = function(id){
                return zeHttp.get('/com_zeapps_crm/product/get/'+id);
            };
            var getAll_product = function(){
                return zeHttp.get('/com_zeapps_crm/product/getAll');
            };
            var get_products_of = function(id){
                return zeHttp.get('/com_zeapps_crm/product/getProductsOf/'+id);
            };
            var save_product = function(data){
                return zeHttp.post('/com_zeapps_crm/product/save', data);
            };
            var delete_product = function(id){
                return zeHttp.post('/com_zeapps_crm/product/delete/'+id);
            };


            // CATEGORIES
            var get_categories_tree = function(){
                return zeHttp.get('/com_zeapps_crm/categories/get_tree');
            };
            var get_category = function(id){
                return zeHttp.get('/com_zeapps_crm/categories/get/'+id);
            };
            var save_category = function(data){
                return zeHttp.post('/com_zeapps_crm/categories/save', data);
            };
            var update_category_order = function(data){
                return zeHttp.post('/com_zeapps_crm/categories/update_order', data);
            };
            var delete_category = function(id, force){
                if(force === undefined)
                    return zeHttp.post('/com_zeapps_crm/categories/delete/'+id);
                else if(force)
                    return zeHttp.post('/com_zeapps_crm/categories/delete/'+id+'/true');
                else
                    return zeHttp.post('/com_zeapps_crm/categories/delete/'+id+'/false');

            };
            var recursiveOpening = function(branch, id){
                if(angular.isArray(branch.branches)){
                    for(var i = 0; i < branch.branches.length; i++){
                        if(recursiveOpening(branch.branches[i], id)){
                            branch.open = true;
                            return true;
                        }
                    }
                }
                return branch.id == id;
            };


            // CONFIG
            var get_invoice_freq = function(){
                return zeHttp.get('/zeapps/config/get/crm_invoice_frequency');
            };
            var get_invoice_format = function(){
                return zeHttp.get('/zeapps/config/get/crm_invoice_format');
            };
            var get_quote_freq = function(){
                return zeHttp.get('/zeapps/config/get/crm_quote_frequency');
            };
            var get_quote_format = function(){
                return zeHttp.get('/zeapps/config/get/crm_quote_format');
            };
            var get_order_freq = function(){
                return zeHttp.get('/zeapps/config/get/crm_order_frequency');
            };
            var get_order_format = function(){
                return zeHttp.get('/zeapps/config/get/crm_order_format');
            };
            var get_product_attr = function(){
                return zeHttp.get('/zeapps/config/get/crm_product_attributes');
            };

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
        });
    }]);