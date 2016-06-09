console.info("location\tsize\tfollowers\tname\tlogo\tlink");
    //you have to create the search first and then take the url from console and put it here
    //theurl = 'https://www.linkedin.com/vsearch/p?locationType=Y&openFacets=N%2CG%2CCC%2CED&f_ED=10582&page_num='+i;
    //theurl = 'https://www.linkedin.com/vsearch/cj?type=companies&orig=FCTD&rsid=55565661420433031038&pageKey=biz-overview-internal&search=Search&f_CCR=au%3A0&openFacets=N,CCR,JO,I&f_I=47&page_num='+i;
for (i = 1; i < 250; i++) {
	theurl = 'https://www.linkedin.com/vsearch/c?keywords=a&page_num='+i;
	
	$.ajax({
		url: theurl,
        async: false
    }).done(function(data){
    	$.each(data.content.page.voltron_unified_search_json.search.results ,
      	function(i,e) {
        	console.info( 
          	e.company.fmt_location + "\t" +
          	e.company.fmt_size + "\t" +
          	e.company.followersCount + "\t" +
          	e.company.fmt_canonicalName + "\t" +
          	e.company.logo_result_base.media_picture_link + "\t" +'http://www.linkedin.com'+e.company.link_biz_overview_6          
                   		);
        	return true;
      	});
  });
  //break;
}

https://www.linkedin.com/profile/view?id=ADEAAAAp7ykBJ9M_aQ1TXBGGpFRN-WUWDu777bw&authType=OUT_OF_NETWORK&authToken=eMTX&locale=en_US&srchid=804044101465339738389&srchindex=14&srchtotal=59185&trk=vsrp_people_res_name&trkInfo=VSRPsearchId%3A804044101465339738389%2CVSRPtargetId%3A2748201%2CVSRPcmpt%3Aprimary%2CVSRPnm%3Afalse%2CauthType%3AOUT_OF_NETWORK

	theurl = 'https://www.linkedin.com/vsearch/c?keywords=a&page_num='+i;

for (i = 1; i < 3; i++) {
	theurl = 'https://www.linkedin.com/vsearch/p?locationType=Y&openFacets=N%2CG%2CCC%2CED&f_ED=10582&page_num='+i;
	$.ajax({
		url: theurl,
        async: false
    }).done(function(data){
    	$.each(data.content.page.voltron_unified_search_json.results, function(k,e){
    		console.info(
    			k
    			);
    		return true;
    	});
  	});
}




<span class="number highlight" data-from="0" data-to="100" data-refresh-interval="10">100</span>












