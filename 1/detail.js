// выделяется крайняя строка таблицы, у которой больше двух колонок
window.addEventListener("load", (event) => {
	const rows = document.querySelectorAll("tr")
	Array.from(rows).filter(item => item.cells.length > 2).pop().classList.add('totalrow')

	var odd = false;
	rows.forEach((row) => {
		row.cells.length < 2 ? row.classList.add('noborder') : row.classList.add('bordered')
/*
		if (row.cells.length < 2) {
		  row.classList.add('noborder') 
		}
		else {
		  row.classList.add('bordered');
		  odd = !odd;
		  row.style.backgroundColor = odd ? #f2f2f2 : #ffffff;
		}
*/
	});

})