// ESC в поле поиска — очистить
document.querySelectorAll('.category-chip').forEach((chip) => {
  chip.addEventListener('click', () => {
    document.querySelectorAll('.category-chip').forEach((c) => {
      c.classList.remove('is-active');
      c.setAttribute('aria-selected', 'false');
    });
    chip.classList.add('is-active');
    chip.setAttribute('aria-selected', 'true');
  });
});

document.querySelectorAll('.header-search__input, .search-page-form__input').forEach((input) => {
  input.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      input.value = '';
    }
  });
});