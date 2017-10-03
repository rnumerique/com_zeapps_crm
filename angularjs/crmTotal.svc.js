app.factory("crmTotal", [function(){

	var doc = {};
	var lines = [];
	var line_details = [];

	var service = {
		init: init,
        sub : {
            HT : subtotalHT,
            TTC : subtotalTTC
        },
		line: {
			update: updateSums
		},
		get: {
			totals: {}
		}
	};
    
	return service;

	function init(d, l, l_d){
        doc = d;
		lines = l;
        line_details = l_d;

		process();
	}

	function process(){
        makeTVAarray();
		calcTotals();
	}

	function makeTVAarray(){
		var tmp = {};
		angular.forEach(lines, function(line){
			if(line !== undefined && line.type !== "subTotal" && line.type !== "comment" && line.has_detail !== "1") {
                if (tmp[line.id_taxe] === undefined) {
                    tmp[line.id_taxe] = {
                        ht: 0,
                        value_taxe: round2(parseFloat(line.value_taxe))
                    };
                }

                tmp[line.id_taxe].ht += round2(parseFloat(line.total_ht));
                tmp[line.id_taxe].value = round2(parseFloat(tmp[line.id_taxe].ht) * (parseFloat(tmp[line.id_taxe].value_taxe) / 100));
            }
		});
		angular.forEach(line_details, function(line){
			if (tmp[line.id_taxe] === undefined) {
				tmp[line.id_taxe] = {
					ht: 0,
					value_taxe: round2(parseFloat(line.value_taxe))
				};
			}

			tmp[line.id_taxe].ht += round2(parseFloat(line.total_ht));
			tmp[line.id_taxe].value = round2(parseFloat(tmp[line.id_taxe].ht) * (parseFloat(tmp[line.id_taxe].value_taxe) / 100));
		});

		service.get.tvas = tmp;
	}

	function calcTotals(){
		calcTotalPreDiscountHT();
		calcTotalPreDiscountTTC();
		calcTotalDiscount();
		calcTotalHT();
		calcTotalTVA();
		calcTotalTTC();
	}

	function calcTotalPreDiscountHT(){
		var t = 0;
		for(var i = 0; i < lines.length; i++){
			if(lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment" && lines[i].has_detail !== "1"){
				t += round2(parseFloat(lines[i].price_unit) * parseFloat(lines[i].qty));
			}
		}
		for(var i = 0; i < line_details.length; i++){
			t += round2(parseFloat(line_details[i].price_unit) * parseFloat(line_details[i].qty));
		}
		service.get.totals.total_prediscount_ht = t;
	}

	function calcTotalPreDiscountTTC(){
		var t = 0;
		for(var i = 0; i < lines.length; i++){
			if(lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment" && lines[i].has_detail !== "1"){
				t += round2(parseFloat(lines[i].price_unit) * parseFloat(lines[i].qty) * ( 1 + (parseFloat(lines[i].value_taxe) / 100)));
			}
		}
		for(var i = 0; i < line_details.length; i++){
			t += round2(parseFloat(line_details[i].price_unit) * parseFloat(line_details[i].qty) * ( 1 + (parseFloat(line_details[i].value_taxe) / 100)));
		}
        service.get.totals.total_prediscount_ttc = t;
	}

	function calcTotalDiscount(){
		var discount = 0;
		var t = 0;
		for (var i = 0; i < lines.length; i++) {
			if (lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment" && lines[i].has_detail !== "1") {
				discount = round2(parseFloat(lines[i].price_unit) * parseFloat(lines[i].qty) * ( 1 -  ( 1 - parseFloat(lines[i].discount) / 100) * ( 1 - parseFloat(doc.global_discount) / 100) ));
				t += discount;
			}
		}
		for (var i = 0; i < line_details.length; i++) {
			discount = round2(parseFloat(line_details[i].price_unit) * parseFloat(line_details[i].qty) * ( 1 -  ( 1 - parseFloat(line_details[i].discount) / 100) * ( 1 - parseFloat(doc.global_discount) / 100) ));
			t += discount;
		}
        service.get.totals.total_discount = t;
	}

	function calcTotalHT(){
		var t = 0;
		for(var i = 0; i < lines.length; i++){
			if(lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment" && lines[i].has_detail !== "1"){
				t += round2(parseFloat(lines[i].total_ht));
			}
		}
		for(var i = 0; i < line_details.length; i++){
			t += round2(parseFloat(line_details[i].total_ht));
		}
        service.get.totals.total_ht = t;
	}

	function calcTotalTVA(){
		var t = 0;
		angular.forEach(service.get.tvas, function(tva){
			t += round2(parseFloat(tva.value));
		});
        service.get.totals.total_tva = t;
	}

	function calcTotalTTC(){
        service.get.totals.total_ttc = parseFloat(service.get.totals.total_ht) + parseFloat(service.get.totals.total_tva);
	}

    function subtotalHT(array, index){
        var t = 0;
        for(var i = index - 1; i >= 0; i--){
            if(array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment"){
                t += round2(parseFloat(array[i].total_ht));
            }
            else if(array[i].type === "subTotal"){
                i = -1;
            }
        }
        return t;
    }

    function subtotalTTC(array, index){
        var t = 0;
        for(var i = index - 1; i >= 0; i--){
            if(array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment"){
                t += round2(parseFloat(array[i].total_ttc));
            }
            else if(array[i].type === "subTotal"){
                i = -1;
            }
        }
        return t;
    }

    function updateSums(line){
    	line.qty = line.qty || 0;
    	line.price_unit = line.price_unit || 0;
    	line.discount = line.discount || 0;

        line.total_ht = round2(round2(parseFloat(line.price_unit)) * parseFloat(line.qty) * ( 1 - (parseFloat(line.discount) / 100) ) * ( 1 - (parseFloat(doc.global_discount) / 100) ));
        line.total_ttc = round2(line.total_ht * ( 1 + (parseFloat(line.value_taxe) / 100) ));
    }

    function round2(num) {
        return +(Math.round(num + "e+2")  + "e-2");
    }
}]);
