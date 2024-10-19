// выделяется крайняя строка таблицы, у которой больше двух колонок
window.addEventListener("load", (event) => {
	const rows = document.querySelectorAll("tr")
	Array.from(rows).filter(item => item.cells.length > 2).pop().classList.add('totalrow')

	rows.forEach((row) => {
		row.cells.length < 2 ? row.classList.add('noborder') : row.classList.add('bordered')
	});

})