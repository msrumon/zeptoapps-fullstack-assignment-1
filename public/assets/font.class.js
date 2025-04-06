import { Tooltip } from 'bootstrap';

export class Font {
  #elInput;
  #elOutput;

  constructor(elFile, elFonts) {
    this.#elInput = elFile;
    this.#elOutput = elFonts;
  }

  getInput() {
    return this.#elInput;
  }

  getOutput() {
    return this.#elOutput;
  }

  async init(fonts) {
    for (const font of fonts) {
      await this.#render(font);
    }

    this.#elInput
      .querySelector('input')
      .addEventListener('change', async (event) => {
        await this.process(event);
      });

    this.#elOutput.addEventListener('click', async (event) => {
      const target = event
        .composedPath()
        .find((el) => el instanceof HTMLButtonElement);
      if (!target) return;

      if (!confirm(`Delete?`)) return;

      await this.#destroy(target.value);
    });
  }

  async process(event) {
    const elFileError = this.#elInput.querySelector('p');
    elFileError.classList.add('d-none');

    const [file] =
      event instanceof DragEvent
        ? event.dataTransfer.files
        : event.target.files;

    if (file) {
      try {
        await this.#render(await this.#upload(file));
      } catch (error) {
        elFileError.textContent = error.message;
        elFileError.classList.remove('d-none');
      } finally {
        event.target.value = '';
      }
    }
  }

  async #upload(file) {
    if (!file.name.endsWith('.ttf')) throw new TypeError('Not A Font!');

    const formData = new FormData();
    formData.append('font', file);

    const httpResponse = await fetch('/font/create', {
      method: 'POST',
      body: formData,
    });
    const jsonResponse = await httpResponse.json();

    if (jsonResponse.error) throw new Error(jsonResponse.error.message);

    return jsonResponse.data;
  }

  async #render(font) {
    const fontFace = new FontFace(font.name, `url('/fonts/${font.path}')`);
    await fontFace.load();
    document.fonts.add(fontFace);

    const th = document.createElement('th');
    th.scope = 'row';
    th.textContent = font.name;

    const td1 = document.createElement('td');
    td1.style.fontFamily = font.name;
    td1.innerHTML = [
      'A QUICK BROWN FOX JUMPS OVER THE LAZY DOG',
      'a quick brown fox jumps over the lazy dog',
    ].join('<br>');

    const button = document.createElement('button');
    button.value = font.id;
    button.className = 'btn';
    button.title = 'Delete';
    button.innerHTML = '<i class="bi bi-trash text-danger"></i>';
    new Tooltip(button);

    const td2 = document.createElement('td');
    td2.append(button);

    const tr = document.createElement('tr');
    tr.className = 'align-middle';
    tr.role = 'button';
    tr.dataset.id = font.id;
    tr.dataset.name = font.name;
    tr.append(th, td1, td2);

    this.#elOutput.querySelector('tbody').append(tr);
    this.#elOutput.querySelector('table').classList.remove('d-none');
    this.#elOutput.querySelector('span').classList.add('d-none');
  }

  async #destroy(id) {
    const formData = new FormData();
    formData.append('id', id);

    await fetch('/font/delete', { method: 'POST', body: formData });
    this.#elOutput.querySelector(`tbody tr[data-id="${id}"]`).remove();

    if (this.#elOutput.querySelector('tbody').children.length === 0) {
      this.#elOutput.querySelector('table').classList.add('d-none');
      this.#elOutput.querySelector('span').classList.remove('d-none');
    }
  }
}
