export class Theme {
  constructor(element) {
    const currentTheme =
      localStorage.getItem('theme') || document.body.dataset.bsTheme;
    switch (currentTheme) {
      case 'dark':
        document.body.dataset.bsTheme = 'dark';
        element.firstElementChild.classList.remove('bi-moon-fill');
        element.firstElementChild.classList.add('bi-sun-fill');
        break;

      default:
        document.body.dataset.bsTheme = 'light';
        element.firstElementChild.classList.remove('bi-sun-fill');
        element.firstElementChild.classList.add('bi-moon-fill');
    }

    element.addEventListener('click', (event) => {
      const target = event
        .composedPath()
        .find((el) => el instanceof HTMLButtonElement);
      if (!target) return;

      switch (document.body.dataset.bsTheme) {
        case 'dark':
          document.body.dataset.bsTheme = 'light';
          target.firstElementChild.classList.remove('bi-sun-fill');
          target.firstElementChild.classList.add('bi-moon-fill');
          break;

        default:
          document.body.dataset.bsTheme = 'dark';
          target.firstElementChild.classList.remove('bi-moon-fill');
          target.firstElementChild.classList.add('bi-sun-fill');
      }

      localStorage.setItem('theme', document.body.dataset.bsTheme);
    });
  }
}
