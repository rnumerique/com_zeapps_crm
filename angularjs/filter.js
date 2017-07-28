app.filter("com_zeapps_crmFilter", function($filter){
	return function(list, filters){
		if(filters){
			return $filter("filter")(list, function(listItem){
				if(filters.finalized){
					if(listItem.finalized == "1")
						return false;
				}

				if(filters.numerotation != undefined && filters.numerotation != "") {
					var regex = new RegExp(filters.numerotation, "i");
					if(listItem.numerotation.search(regex) == -1)
						return false;
				}
				if(filters.libelle != undefined && filters.libelle != "") {
					var regex = new RegExp(filters.libelle, "i");
					if(listItem.libelle.search(regex) == -1)
						return false;
				}
				if(filters.client != undefined && filters.client != "") {
					var regex = new RegExp(filters.client, "i");
					if(listItem.company.company_name.search(regex) == -1
																				&& listItem.contact.first_name.search(regex) == -1
																				&& listItem.contact.last_name.search(regex) == -1)
						return false;
				}

				if(filters.date_creation_start != undefined && filters.date_creation_start != "") {
					if(listItem.date_creation <= filters.date_creation_start)
						return false;
				}
				if(filters.date_creation_end != undefined && filters.date_creation_end != "") {
					if(listItem.date_creation >= filters.date_creation_end)
						return false;
				}
				if(filters.date_limite_start != undefined && filters.date_limite_start != "") {
					if(listItem.date_limite <= filters.date_limite_start)
						return false;
				}
				if(filters.date_limite_end != undefined && filters.date_limite_end != "") {
					if(listItem.date_limite >= filters.date_limite_end)
						return false;
				}

				if(filters.total_ht_floor != undefined && filters.total_ht_floor != "") {
					if(parseFloat(listItem.total_ht) <= parseFloat(filters.total_ht_floor))
						return false;
				}
				if(filters.total_ht_ceiling != undefined && filters.total_ht_ceiling != "") {
					if(parseFloat(listItem.total_ht) >= parseFloat(filters.total_ht_ceiling))
						return false;
				}
				if(filters.total_ttc_floor != undefined && filters.total_ttc_floor != "") {
					if(parseFloat(listItem.total_ttc) <= parseFloat(filters.total_ttc_floor))
						return false;
				}
				if(filters.total_ttc_ceiling != undefined && filters.total_ttc_ceiling != "") {
					if(parseFloat(listItem.total_ttc) >= parseFloat(filters.total_ttc_ceiling))
						return false;
				}

				return true;
			});
		}
		return list;
	};
});