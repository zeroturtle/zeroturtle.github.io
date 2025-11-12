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
    attachHistoryListener();
  } catch (err) {
    contentDiv.innerHTML = `<p>Ошибка загрузки контента.</p>`;
    console.error(err);
  }
}

function attachHistoryListener() {
  const link = document.getElementById("historyLink");

  link.addEventListener("click", async (event) => {
    const link = event.target.closest("#historyLink");
    if (!link) return; // клик не по нужной ссылке
    event.preventDefault();

    const content = document.getElementById("historyContent");
    const arrow = document.getElementById("historyArrow");

    // Подгружаем текст только один раз
    if (!content.dataset.loaded) {
      try {
        const text = await loadHistory(link.href);
        content.innerHTML = text;
        content.dataset.loaded = "true";
      } catch (err) {
      content.textContent = "Error loading history.";
      console.error(err);
      }
    }

    // Переключаем видимость и класс стрелки
    const isVisible = content.style.display !== "none";
    content.style.display = isVisible ? "none" : "block";
    arrow.classList.toggle("open", !isVisible);
  });
}

function loadHistory(url) {
  return fetch(url).then(res => res.ok ? res.text() : Promise.reject("Failed"));
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


const backToTop = document.querySelector('.back-to-top');

// Плавный скролл при клике
backToTop.addEventListener('click', (e) => {
  e.preventDefault();
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Появление кнопки при прокрутке
window.addEventListener('scroll', () => {
  backToTop.style.display = window.scrollY > 200 ? 'flex' : 'none';
});