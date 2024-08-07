﻿	const code3=['AUS','AUT','AZE','ALB','DZA','AIA','AGO','AND','ATA','ATG','ANT','ARG','ARM','ABW','AFG','BHS','BGD','BRB','BHR','BLR','BLZ','BEL','BEN','BMU','BVT','BGR','BOL','BIH','BWA','BRA','BRN','BFA','BDI','BTN','VUT','VAT','GBR','HUN','VEN','VGB','VIR','ASM','TMP','VNM','GAB','HTI','GUY','GMB','GHA','GLP','GTM','GIN','GNB','DEU','GIB','HND','HKG','GRD','GRL','GRC','GEO','GUM','DNK','COD','DJI','DMA','DOM','EGY','ZMB','ESH','ZWE','ISR','IND','IDN','JOR','IRQ','IRN','IRL','ISL','ESP','ITA','YEM','CPV','KAZ','CYM','KHM','CMR','CAN','QAT','KEN','CYP','KGZ','KIR','CHN','CCK','COL','COM','COG','CRI','CIV','CUB','KWT','COK','LAO','LVA','LSO','LBR','LBN','LBY','LTU','LIE','LUX','MUS','MRT','MDG','MYT','MAC','MKD','MWI','MYS','MLI','MDV','MLT','MAR','MTQ','MHL','MEX','FSM','MOZ','MDA','MCO','MNG','MSR','MMR','NAM','NRU','NPL','NER','NGA','NLD','NIC','NIU','NZL','NCL','NOR','NFK','ARE','OMN','PAK','PLW','?','PAN','PNG','PRY','PER','PCN','POL','PRT','PRI','REU','CXR','RUS','RWA','ROM','SLV','WSM','SMR','STP','SAU','SWZ','SJM','SHN','PRK','MNP','SYC','VCT','SPM','SEN','KNA','LCA','SGP','SYR','SVK','SVN','USA','SLB','SOM','SDN','SUR','SLE','TJK','THA','TWN','TZA','TCA','TGO','TKL','TON','TTO','TUV','TUN','TKM','TUR','UGA','UZB','UKR','WLF','URY','FRO','FJI','PHL','FIN','FLK','FRA','GUF','PYF','HMD','HRV','CAF','TCD','CZE','CHL','CHE','SWE','LKA','ECU','GNQ','ERI','EST','ETH','YUG','ZAF','SGS','KOR','JAM','JPN','ATF','IOT','UMI']
	const code2=['AU','AT','AZ','AL','DZ','AI','AO','AD','AQ','AG','AN','AR','AM','AW','AF','BS','BD','BB','BH','BY','BZ','BE','BJ','BM','BV','BG','BO','BA','BW','BR','BN','BF','BI','BT','VU','VA','GB','HU','VE','VG','VI','AS','TP','VN','GA','HT','GY','GM','GH','GP','GT','GN','GW','DE','GI','HN','HK','GD','GL','GR','GE','GU','DK','CD','DJ','DM','DO','EG','ZM','EH','ZW','IL','IN','ID','JO','IQ','IR','IE','IS','ES','IT','YE','CV','KZ','KY','KH','CM','CA','QA','KE','CY','KG','KI','CN','CC','CO','KM','CG','CR','CI','CU','KW','CK','LA','LV','LS','LR','LB','LY','LT','LI','LU','MU','MR','MG','YT','MO','MK','MW','MY','ML','MV','MT','MA','MQ','MH','MX','FM','MZ','MD','MC','MN','MS','MM','NA','NR','NP','NE','NG','NL','NI','NU','NZ','NC','NO','NF','AE','OM','PK','PW','PS','PA','PG','PY','PE','PN','PL','PT','PR','RE','CX','RU','RW','RO','SV','WS','SM','ST','SA','SZ','SJ','SH','KP','MP','SC','VC','PM','SN','KN','LC','SG','SY','SK','SI','US','SB','SO','SD','SR','SL','TJ','TH','TW','TZ','TC','TG','TK','TO','TT','TV','TN','TM','TR','UG','UZ','UA','WF','UY','FO','FJ','PH','FI','FK','FR','GF','PF','HM','HR','CF','TD','CZ','CL','CH','SE','LK','EC','GQ','ER','EE','ET','YU','ZA','GS','KR','JM','JP','TF','IO','UM']
	// ÷òîá çàêðûòü dialog
	const modal = document.querySelector('dialog')
	const frame = modal.querySelector('iframe')
	function closedialog() {
//		frame.src="about:blank"
		frame.srcdoc = ""
		modal.close()
	}
	function windowOnClick(event) {
		if (event.target === modal) {
			closedialog()
		}
	}
	window.addEventListener("click", windowOnClick); 

	// get last-modified of proto.html
/*	fetch("proto.html", {method: "HEAD"})
		.then (r => document.getElementById('container').after('Last-Modified: '+ r.headers.get('Last-Modified')))
		.catch(e => console.error(e))
*/

	// çàãðóæàåì ïðîòîêîë
	window.addEventListener("load", (event) => {
		fetch('proto.html')
		.then(response => response.text())
		.then((data) => {
			container.innerHTML= data   //âûâîäèì ïðîòîêîë
			// äëÿ êàæäîé ññûëêå íå "0" äîáàâëÿåì âûçîâ detail
			for (lnk of [].filter.call(document.getElementsByTagName('a'), item =>(item.pathname.split('/').slice(-1))[0] !=0))  
				if (lnk.href.endsWith('jpg')) {
					lnk.addEventListener("click", displayTeamDetails)
				}
				else { 
					lnk.addEventListener("click", displayRoundDetails)
				}
			// convert country name to flag SVG-image
			for (const cell of document.querySelectorAll('td')) {
				if (cell.cellIndex == 1 && code3.indexOf(cell.innerText) > 0) {
					let img = document.createElement("img");
					img.setAttribute("src", `./flags/${(code2[code3.indexOf(cell.innerText)]).toLowerCase()}.svg`)
					img.setAttribute("alt", `${cell.innerText}`)
					img.style.width = "40px";
					cell.innerHTML = '';
					cell.append(img);
				}
			}
		})
	})

//Team Photo
function displayTeamDetails() {
	event.preventDefault()
	var doc = document.implementation.createHTMLDocument('Team Photo')
	let img = document.createElement("img");
	img.setAttribute("src", (this.pathname.split('/').slice(-1))[0])
	img.setAttribute("alt", (this.pathname.split('/').slice(-1))[0])
//	img.style.height = '100vh'
	doc.body.append(img)
	doc.body.insertAdjacentHTML('afterbegin',`<h2>${this.innerHTML}<h2>`) //team name
	doc.body.insertAdjacentHTML('beforeend','Team Members:')

	frame.srcdoc = doc.documentElement.outerHTML
	modal.showModal()
}

// round detail
function displayRoundDetails() {
	event.preventDefault()
	// çàãðóæàåì ñóäåéñêóþ çàïèñêó	
	fetch( new URL(this+'.html') )
		.then(response => response.text())
		.then((html) => {
			// Convert the HTML string into a document object
			var doc = new DOMParser().parseFromString(html, "text/html")
			doc.head.insertAdjacentHTML('beforeend', '<link type="text/css" rel="stylesheet" href="detail.css">');
			//<a target="parent"> will open links in a new tab/window ... <a target="_parent"> will open links in the parent/current window.
			for(a of doc.querySelectorAll('a')) a.setAttribute('target','parent')

			frame.srcdoc = doc.documentElement.outerHTML
			modal.showModal()
		})
		.catch(error => console.error('Error fetching file:', error))
}
	document.querySelector('body').insertAdjacentText('beforeend', 'Powered by OPTIMUS Prometheus')
