window.addEventListener("load", (event) => {
	const rows = document.querySelectorAll("tr")
	// выделяется крайняя строка таблицы, у которой больше двух колонок
	Array.from(rows).filter(item => item.cells.length > 2).pop().classList.add('totalrow')

	// set tanle zebra-color
	rows.forEach((row, index) => {
		if ((row.cells.length > 2) && (index % 2 === 0)) {
			row.classList.add('even-class');
		}
	});

})