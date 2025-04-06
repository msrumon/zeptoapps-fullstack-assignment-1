import { App } from './app.class.js';
import { Theme } from './theme.class.js';

document.addEventListener('DOMContentLoaded', async () => {
  new Theme(document.getElementById('theme'));

  const elUploader = document.getElementById('uploader');
  const elFile = document.getElementById('file');
  const elFonts = document.getElementById('fonts');
  const elGroups = document.getElementById('groups');
  const elCreation = document.getElementById('creation');

  new MutationObserver((mutations) => {
    let elSelect = elCreation.querySelector('select');

    for (const mutation of mutations) {
      if (!elSelect) {
        const option = document.createElement('option');
        option.value = '';
        option.selected = true;
        option.innerHTML = '&dash;&dash; Select A Font &dash;&dash;';

        elSelect = document.createElement('select');
        elSelect.className = 'form-select';
        elSelect.name = 'fonts[]';
        elSelect.required = true;
        elSelect.append(option);

        const div1 = document.createElement('div');
        div1.className = 'col p-0';
        div1.append(elSelect);

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn-close';
        button.title = 'Close';
        button.disabled = true;

        const div2 = document.createElement('div');
        div2.className = 'col-auto p-0';
        div2.append(button);

        const div0 = document.createElement('div');
        div0.className = 'row m-0 p-2 gap-2 align-items-center border rounded';
        div0.append(div1, div2);

        elCreation.querySelector('article').append(div0);
      }

      mutation.addedNodes.forEach((node) => {
        if (!(node instanceof Element)) return;
        const option = document.createElement('option');
        option.value = node.dataset.id;
        option.textContent = node.dataset.name;
        elSelect.append(option);
      });

      mutation.removedNodes.forEach((node) => {
        if (!(node instanceof Element)) return;
        elSelect.querySelector(`option[value="${node.dataset.id}"]`).remove();
      });

      if (mutation.target.children.length > 0) {
        elCreation.classList.remove('d-none');
      } else {
        elCreation.classList.add('d-none');
      }
    }
  }).observe(elFonts.querySelector('tbody'), { childList: true });

  new MutationObserver((mutations) => {
    for (const mutation of mutations) {
      const fields = mutation.target.children;
      for (const el of fields) {
        el.querySelector('button.btn-close').disabled = !(fields.length > 1);
      }
    }
  }).observe(elCreation.querySelector('article'), { childList: true });

  const app = new App(elFile, elFonts, elCreation, elGroups);
  await app.initFonts();
  await app.initGroups();

  const font = app.getFont();
  const group = app.getGroup();

  elUploader.addEventListener('click', () => {
    elFile.querySelector('input').click();
  });

  elUploader.addEventListener('dragover', (event) => {
    event.preventDefault();
    elFile.querySelector('p').textContent = '';
    if (event.currentTarget.contains(event.relatedTarget)) return;
    elUploader.classList.add('border-primary');
  });

  elUploader.addEventListener('dragleave', (event) => {
    elFile.querySelector('p').textContent = '';
    if (event.currentTarget.contains(event.relatedTarget)) return;
    elUploader.classList.remove('border-primary');
  });

  elUploader.addEventListener('drop', async (event) => {
    event.preventDefault();
    await font.process(event);
  });

  new MutationObserver((mutations) => {
    const form = elCreation.querySelector('form');

    for (const mutation of mutations) {
      if (mutation.type === 'childList') {
        mutation.addedNodes.forEach((node) => {
          if (!(node instanceof Element)) return;
          group.validate(form);
        });

        mutation.removedNodes.forEach((node) => {
          if (!(node instanceof Element)) return;
          group.validate(form);
        });
      }
    }
  }).observe(elCreation.querySelector('form'), {
    childList: true,
    subtree: true,
  });
});
