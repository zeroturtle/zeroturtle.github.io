
// çàãðóæàåì ïðîòîêîë
function loadEvent(event) {
	event.preventDefault()
	fetch(`${baseURL}${Rank}/proto.html`)
	.then(response => response.text())
	.then((html) => {
		let doc = new DOMParser().parseFromString(html, "text/html")
		//document.head.insertAdjacentHTML('beforeend',`<link type="text/css" rel="stylesheet" href="proto.css">`)
		let resultTable = doc.querySelector('table')
		resultTable.setAttribute('id','resultTable')
		// äëÿ êàæäîé ññûëêå íå "0" äîáàâëÿåì âûçîâ detail
		for (lnk of [].filter.call(resultTable.getElementsByTagName('a'), item =>(item.pathname.split('/').slice(-1))[0] !=0)) { 
			let f = lnk.href.toLowerCase()
			if (f.endsWith('_team')) {
				lnk.addEventListener("click", displayTeamDetails)
			}
			else if (f.endsWith('mp4')) {
				lnk.addEventListener("click", displayRoundVideo)
			}
			else {  // this is a score details
				if (Number.isFinite(lnk))
					lnk.addEventListener("click", displayRoundDetails)
			}
		}

	// show draw
		const rowDraw = 1
		for( let c of resultTable.rows[rowDraw].cells ) {
			let span = document.createElement('span')
			span.style.display = 'none'
			span.classList.add('draw-img')

			c.innerHTML.split(/-/).forEach((v) => {
				let img = document.createElement('img')
				img.src = Rank+'/divepool/'+'/'+ v +'.png'
				span.append(img)
			});
			c.onmouseover = function(event) {
				if (event.target.querySelector('span')) {
					const span = event.target.querySelector('span')
					span.style.display = 'block'
					const rect = span.getBoundingClientRect();
					let newX = (rect.right < window.innerWidth) ? event.clientX : (( rect.width > window.innerWidth) ? 0 : window.innerWidth - rect.width)
					span.style.left = newX + 'px';
				}
			};
			c.onmouseout = function(event) {
				if (event.target.querySelector('span'))
					event.target.querySelector('span').style.display = 'none'
			};
			c.append(span);
		};
	// draw
		// convert country name to flag SVG-image
		for (const cell of resultTable.querySelectorAll('td')) {
			if (cell.cellIndex == 1 && code3.indexOf(cell.innerText) > 0) {
				let img = doc.createElement("img");
				img.setAttribute("src", `../../flags/${(code2[code3.indexOf(cell.innerText)]).toLowerCase()}.svg`)
				img.setAttribute("alt", `${cell.innerText}`)
				img.style.width = "36px";
				cell.innerHTML = '';
				cell.append(img);
			}
		}

		scoreSummary.append(resultTable)
	})
}

// video of perfomance
function displayRoundVideo() {
	// открыть видео в отдельном окне
	event.preventDefault()
}

//Team
function displayTeamDetails() {
	event.preventDefault()
	let doc = document.implementation.createHTMLDocument('Team Photo')
	// найти картинку по записи в 
	fetch( new URL(`${baseURL}${Rank}/team/`+String(this).split('/').pop()+'.html') )
		.then(response => response.text())
		.then((html) => {
			// Convert the HTML string into a document object
			let doc = new DOMParser().parseFromString(html, "text/html")
			doc.head.insertAdjacentHTML('beforeend', `<link type="text/css" rel="stylesheet" href="${baseURL}team.css">`)
			frame.srcdoc = doc.documentElement.outerHTML
			modal.showModal()
		})
		.catch(error => console.error('Error fetching file:', error))
}

// round detail
function displayRoundDetails() {
	event.preventDefault()
	// çàãðóæàåì ñóäåéñêóþ çàïèñêó	
	fetch( new URL(`${baseURL}${Rank}/detail/`+String(this).split('/').pop()+'.html') )
		.then(response => response.text())
		.then((html) => {
			// Convert the HTML string into a document object
			let doc = new DOMParser().parseFromString(html, "text/html")
			doc.head.insertAdjacentHTML('beforeend',`<link type="text/css" rel="stylesheet" href="${baseURL}detail.css">`)
			doc.head.insertAdjacentHTML('beforeend', `<script src="${baseURL}detail.js"></script>`)
			//<a target="parent"> will open links in a new tab/window ... <a target="_parent"> will open links in the parent/current window.
			for(a of doc.querySelectorAll('a')) a.setAttribute('target','parent')
			frame.srcdoc = doc.documentElement.outerHTML
			modal.showModal()
		})
		.catch(error => console.error('Error fetching file:', error))
}

// приведение в соответствие сокращения стран из 3 букв к названиям флагов из 2 букв.
const code3=['AUS','AUT','AZE','ALB','DZA','AIA','AGO','AND','ATA','ATG','ANT','ARG','ARM','ABW','AFG','BHS','BGD','BRB','BHR','BLR','BLZ','BEL','BEN','BMU','BVT','BGR','BOL','BIH','BWA','BRA','BRN','BFA','BDI','BTN','VUT','VAT','GBR','HUN','VEN','VGB','VIR','ASM','TMP','VNM','GAB','HTI','GUY','GMB','GHA','GLP','GTM','GIN','GNB','DEU','GIB','HND','HKG','GRD','GRL','GRC','GEO','GUM','DNK','COD','DJI','DMA','DOM','EGY','ZMB','ESH','ZWE','ISR','IND','IDN','JOR','IRQ','IRN','IRL','ISL','ESP','ITA','YEM','CPV','KAZ','CYM','KHM','CMR','CAN','QAT','KEN','CYP','KGZ','KIR','CHN','CCK','COL','COM','COG','CRI','CIV','CUB','KWT','COK','LAO','LVA','LSO','LBR','LBN','LBY','LTU','LIE','LUX','MUS','MRT','MDG','MYT','MAC','MKD','MWI','MYS','MLI','MDV','MLT','MAR','MTQ','MHL','MEX','FSM','MOZ','MDA','MCO','MNG','MSR','MMR','NAM','NRU','NPL','NER','NGA','NLD','NIC','NIU','NZL','NCL','NOR','NFK','ARE','OMN','PAK','PLW','?','PAN','PNG','PRY','PER','PCN','POL','PRT','PRI','REU','CXR','RUS','RWA','ROM','SLV','WSM','SMR','STP','SAU','SWZ','SJM','SHN','PRK','MNP','SYC','VCT','SPM','SEN','KNA','LCA','SGP','SYR','SVK','SVN','USA','SLB','SOM','SDN','SUR','SLE','TJK','THA','TWN','TZA','TCA','TGO','TKL','TON','TTO','TUV','TUN','TKM','TUR','UGA','UZB','UKR','WLF','URY','FRO','FJI','PHL','FIN','FLK','FRA','GUF','PYF','HMD','HRV','CAF','TCD','CZE','CHL','CHE','SWE','LKA','ECU','GNQ','ERI','EST','ETH','YUG','ZAF','SGS','KOR','JAM','JPN','ATF','IOT','UMI']
const code2=['AU','AT','AZ','AL','DZ','AI','AO','AD','AQ','AG','AN','AR','AM','AW','AF','BS','BD','BB','BH','BY','BZ','BE','BJ','BM','BV','BG','BO','BA','BW','BR','BN','BF','BI','BT','VU','VA','GB','HU','VE','VG','VI','AS','TP','VN','GA','HT','GY','GM','GH','GP','GT','GN','GW','DE','GI','HN','HK','GD','GL','GR','GE','GU','DK','CD','DJ','DM','DO','EG','ZM','EH','ZW','IL','IN','ID','JO','IQ','IR','IE','IS','ES','IT','YE','CV','KZ','KY','KH','CM','CA','QA','KE','CY','KG','KI','CN','CC','CO','KM','CG','CR','CI','CU','KW','CK','LA','LV','LS','LR','LB','LY','LT','LI','LU','MU','MR','MG','YT','MO','MK','MW','MY','ML','MV','MT','MA','MQ','MH','MX','FM','MZ','MD','MC','MN','MS','MM','NA','NR','NP','NE','NG','NL','NI','NU','NZ','NC','NO','NF','AE','OM','PK','PW','PS','PA','PG','PY','PE','PN','PL','PT','PR','RE','CX','RU','RW','RO','SV','WS','SM','ST','SA','SZ','SJ','SH','KP','MP','SC','VC','PM','SN','KN','LC','SG','SY','SK','SI','US','SB','SO','SD','SR','SL','TJ','TH','TW','TZ','TC','TG','TK','TO','TT','TV','TN','TM','TR','UG','UZ','UA','WF','UY','FO','FJ','PH','FI','FK','FR','GF','PF','HM','HR','CF','TD','CZ','CL','CH','SE','LK','EC','GQ','ER','EE','ET','YU','ZA','GS','KR','JM','JP','TF','IO','UM']
const modal = document.querySelector('dialog')
const frame = modal.querySelector('iframe')

const urlParams = new URLSearchParams(window.location.search)
Rank = (urlParams.get("r")==null) ? document.getElementsByClassName("nav-link")[0].getAttribute('href').slice(3) : urlParams.get("r") // default параметр r


function windowOnClick(event) {
	if (event.target === modal) {
		frame.srcdoc = ""
		modal.close('escape')
	}
}

window.addEventListener("load", loadEvent)
window.addEventListener("click", windowOnClick)
document.getElementById('scoreSummary').insertAdjacentHTML('afterend', '<small>Powered by OPTIMUS Artemis</small>')

