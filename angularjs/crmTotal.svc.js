app.factory("crmTotal", function(){
    
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
			TVA : totalTVA,
			TTC : totalTTC
		}
	};
    
	return service;
    
    
	function subtotalHT(array, index){
		var total = 0;
		for(var i = index - 1; i >= 0; i--){
			if(array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment"){
				total += parseFloat(array[i].total_ht);
			}
			else if(array[i].type === "subTotal"){
				i = -1;
			}
		}
		return total;
	}

	function subtotalTTC(array, index){
		var total = 0;
		for(var i = index - 1; i >= 0; i--){
			if(array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment"){
				total += parseFloat(array[i].total_ttc);
			}
			else if(array[i].type === "subTotal"){
				i = -1;
			}
		}
		return total;
	}

	function totalPreDiscountHT(array){
		var total = 0;
		for(var i = 0; i < array.length; i++){
			if(array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment"){
				total += parseFloat(array[i].total_ht);
			}
		}
		return total;
	}

	function totalPreDiscountTTC(array){
		var total = 0;
		for(var i = 0; i < array.length; i++){
			if(array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment"){
				total += parseFloat(array[i].total_ttc);
			}
		}
		return total;
	}

	function totalDiscount(array, global_discount){
		var discount = 0;
		var total = 0;

		for (var i = 0; i < array.length; i++) {
			if (array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment") {
				discount = parseFloat(array[i].price_unit) * ( 1 -  ( 1 - parseFloat(array[i].discount) / 100) * ( 1 - parseFloat(global_discount) / 100) );
				total += discount;
			}
		}

		return total;
	}

	function totalHT(array){
		var total = 0;

		for(var i = 0; i < array.length; i++){
			if(array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment"){
				total += parseFloat(array[i].total_ht);
			}
		}

		return total;
	}

	function totalTVA(array){
		var total = 0;

		for(var i = 0; i < array.length; i++){
			if(array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment"){
				total += parseFloat(array[i].total_ht) * (parseFloat(array[i].taxe) / 100);
			}
		}

		return total;
	}

	function totalTTC(array){
		var total = 0;

		for(var i = 0; i < array.length; i++){
			if(array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment"){
				total += parseFloat(array[i].total_ttc);
			}
		}

		return total;
	}
});
