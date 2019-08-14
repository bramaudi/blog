let getPagination = (p) => {
	
	let start, end, html = ''
	if (p.get > 1) {
		html += '<a onclick="getData(1)">First</a>'
	}

	if (p.get > 2) {
		start = p.get - 2
	} else {
		start = 1
	}
	if (p.get == p.page) {
		end = p.page
	} else if (p.get < p.page) {
		end = +p.get + 1
	} else {
		end = +p.get + 2
	}
	
	for (let i = start; i <= end; i++) {
		if (i != p.get) {
			html += ' <a onclick="getData('+i+')">'+i+'</a> '
		} else {
			html += '<span>'+i+'</span>'
		}
	}

	if (p.get > 0 && p.get < p.page) {
		html += '<a onclick="getData('+p.page+')">Last ('+p.page+')</a>'
	}

	m.render(pagination, m('div.pagination', m.trust(html)))
}