// Cookie-функции
function setCookie(name, value, days = 365) {
  const expires = new Date(Date.now() + days*24*60*60*1000).toUTCString();
  document.cookie = `${name}=${value}; expires=${expires}; path=/`;
}

function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
  return match ? match[2] : null;
}

// Функция загрузки контента через fetch
async function loadContent(lang) {
  const contentDiv = document.getElementById('content');
  try {
    const response = await fetch(`inc/${lang}.html`);
    if (!response.ok) throw new Error('Failed to load content');
    const html = await response.text();
    contentDiv.innerHTML = html;
    document.getElementById('language-selector').value = lang;
  } catch (err) {
    contentDiv.innerHTML = `<p>Ошибка загрузки контента.</p>`;
    console.error(err);
  }
}

// Инициализация языка
let lang = getCookie('siteLang') || 'en';
loadContent(lang);

// Обработчик смены языка
document.getElementById('language-selector').addEventListener('change', e => {
  const selectedLang = e.target.value;
  setCookie('siteLang', selectedLang, 365);
  loadContent(selectedLang);
});
