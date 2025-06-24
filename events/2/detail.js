// выделяется крайняя строка таблицы, у которой больше двух колонок
window.addEventListener("load", (event) => {
	Array.from(document.getElementsByTagName('tr')).filter(item => item.cells.length > 2).pop().classList.add('totalrow')
})