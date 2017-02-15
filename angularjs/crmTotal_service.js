app.factory('crmTotal', function(){
    
    var service = {
        sub : {
            HT : subtotalHT,
            TTC : subtotalTTC
        },
        preDiscount : {
            HT : totalPreDiscountHT,
            TTC : totalPreDiscountTTC
        },
        discount : totalDiscount,
        total : {
            HT : totalHT,
            TTC : totalTTC
        }
    };
    
    return service;
    
    
    function subtotalHT(array, index){
        var total = 0;
        for(var i = index - 1; i >= 0; i--){
            if(array[i] != undefined && array[i].type != 'subTotal' && array[i].type != 'comment'){
                total += parseFloat(array[i].total_ht);
            }
            else if(array[i].type == 'subTotal'){
                i = -1;
            }
        }
        return total;
    }

    function subtotalTTC(array, index){
        var total = 0;
        for(var i = index - 1; i >= 0; i--){
            if(array[i] != undefined && array[i].type != 'subTotal' && array[i].type != 'comment'){
                total += parseFloat(array[i].total_ttc);
            }
            else if(array[i].type == 'subTotal'){
                i = -1;
            }
        }
        return total;
    }

    function totalPreDiscountHT(array){
        var total = 0;
        for(var i = 0; i < array.length; i++){
            if(array[i] != undefined && array[i].type != 'subTotal' && array[i].type != 'comment'){
                total += parseFloat(array[i].total_ht);
            }
        }
        return total;
    }

    function totalPreDiscountTTC(array){
        var total = 0;
        for(var i = 0; i < array.length; i++){
            if(array[i] != undefined && array[i].type != 'subTotal' && array[i].type != 'comment'){
                total += parseFloat(array[i].total_ttc);
            }
        }
        return total;
    }

    function totalDiscount(array, global_discount){
        var total = 0;

        for (var i = 0; i < array.length; i++) {
            if (array[i] != undefined && array[i].type != 'subTotal' && array[i].type != 'comment') {
                total += parseFloat(array[i].total_ht) * (array[i].discount / 100);
            }
        }
        total = total + totalPreDiscountHT(array) * (global_discount / 100);

        return total;
    }

    function totalHT(array, global_discount){
        var total = 0;

        for(var i = 0; i < array.length; i++){
            if(array[i] != undefined && array[i].type != 'subTotal' && array[i].type != 'comment'){
                total += parseFloat(array[i].total_ht);
            }
        }
        total = total * (1- (global_discount / 100) );

        return total;
    }

    function totalTTC(array, global_discount){
        var total = 0;

        for(var i = 0; i < array.length; i++){
            if(array[i] != undefined && array[i].type != 'subTotal' && array[i].type != 'comment'){
                total += parseFloat(array[i].total_ttc);
            }
        }
        total = total * (1- (global_discount / 100) );

        return total;
    }
});
