app.factory("crmTotal", function(){

	var doc = {};
	var lines = [];
    
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

	function init(d, l){
        doc = d;
		lines = l;

		process();
	}

	function process(){
        makeTVAarray();
		calcTotals();
	}

	function makeTVAarray(){
		var tmp = {};
		angular.forEach(lines, function(line){
			if(line !== undefined && line.type !== "subTotal" && line.type !== "comment") {
				console.log(line.id_taxe);
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
			if(lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment"){
				t += round2(parseFloat(lines[i].price_unit));
			}
		}
		service.get.totals.total_prediscount_ht = t;
	}

	function calcTotalPreDiscountTTC(){
		var t = 0;
		for(var i = 0; i < lines.length; i++){
			if(lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment"){
				t += round2(parseFloat(lines[i].price_unit) * ( 1 + (parseFloat(lines[i].value_taxe) / 100)));
			}
		}
        service.get.totals.total_prediscount_ttc = t;
	}

	function calcTotalDiscount(){
		var discount = 0;
		var t = 0;
		for (var i = 0; i < lines.length; i++) {
			if (lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment") {
				discount = round2(parseFloat(lines[i].price_unit) * ( 1 -  ( 1 - parseFloat(lines[i].discount) / 100) * ( 1 - parseFloat(doc.global_discount) / 100) ));
				t += discount;
			}
		}
        service.get.totals.total_discount = t;
	}

	function calcTotalHT(){
		var t = 0;
		for(var i = 0; i < lines.length; i++){
			if(lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment"){
				t += round2(parseFloat(lines[i].total_ht));
			}
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
        line.total_ht = round2(parseFloat(line.price_unit) * parseFloat(line.qty) * ( 1 - (parseFloat(line.discount) / 100) ) * ( 1 - (parseFloat(doc.global_discount) / 100) ));
        line.total_ttc = round2(line.total_ht * ( 1 + (parseFloat(line.value_taxe) / 100) ));
    }

    function round2(num) {
        return +(Math.round(num + "e+2")  + "e-2");
    }
});
