import { Tooltip } from 'bootstrap';

export class Group {
  #elInput;
  #elOutput;

  constructor(elCreation, elGroups) {
    this.#elInput = elCreation;
    this.#elOutput = elGroups;
  }

  getInput() {
    return this.#elInput;
  }

  getOutput() {
    return this.#elOutput;
  }

  async init(groups) {
    for (const group of groups) {
      await this.#render(group);
    }

    this.#elInput.addEventListener('input', async (event) => {
      const target = event
        .composedPath()
        .find((el) => el instanceof HTMLFormElement);
      if (!target) return;

      this.validate(target);
    });

    this.#elInput.addEventListener('click', async (event) => {
      const target = event
        .composedPath()
        .find((el) => el.className === 'btn-close');
      if (!target) return;

      target.closest('.row').remove();
    });

    this.#elInput
      .querySelector('aside button[type="button"]')
      .addEventListener('click', async () => {
        const option = document.createElement('option');
        option.value = '';
        option.selected = true;
        option.innerHTML = '&dash;&dash; Select A Font &dash;&dash;';

        const select = document.createElement('select');
        select.className = 'form-select';
        select.name = 'fonts[]';
        select.required = true;
        select.append(option);

        const httpResponse = await fetch('/font');
        const jsonResponse = await httpResponse.json();

        for (const font of jsonResponse.data) {
          const option = document.createElement('option');
          option.value = font.id;
          option.innerHTML = font.name;
          select.append(option);
        }

        const div1 = document.createElement('div');
        div1.className = 'col p-0';
        div1.append(select);

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

        this.#elInput.querySelector('article').append(div0);
      });

    this.#elInput
      .querySelector('form')
      .addEventListener('submit', async (event) => {
        event.preventDefault();

        const elFormError = event.target.querySelector('aside p');
        elFormError.classList.add('d-none');

        try {
          await this.#render(await this.#upload(event.target));
        } catch (error) {
          elFormError.textContent = error.message;
          elFormError.classList.remove('d-none');
        } finally {
          const article = event.target.querySelector('article');
          while (article.children.length > 1) {
            article.lastElementChild.remove();
          }
          event.target.reset();
        }
      });

    this.#elOutput.addEventListener('click', async (event) => {
      const target = event.composedPath().find((el) => el.title === 'Edit');
      if (!target) return;

      alert('Not Implemented');
    });

    this.#elOutput.addEventListener('click', async (event) => {
      const target = event.composedPath().find((el) => el.title === 'Delete');
      if (!target) return;

      if (!confirm(`Delete?`)) return;

      await this.#destroy(target.value);
    });
  }

  validate(form) {
    const input = form.querySelector('input[required]');
    const selects = form.querySelectorAll('select[required]');
    const button = form.querySelector('button[type="submit"]');

    const arrSelects = Array.from(selects);
    const select2values = arrSelects.map((select) => select.value);

    button.disabled = !(
      input.checkValidity() &&
      selects.length >= 2 &&
      arrSelects.every((select) => select.checkValidity()) &&
      new Set(select2values).size === select2values.length
    );
  }

  async #upload(form) {
    const data = new FormData(form);

    const httpResponse = await fetch('/group/create', {
      method: 'POST',
      body: data,
    });
    const jsonResponse = await httpResponse.json();

    if (jsonResponse.error) throw new Error(jsonResponse.error.message);

    return jsonResponse.data;
  }

  async #render(group) {
    const th = document.createElement('th');
    th.scope = 'row';
    th.textContent = group.name;

    const td1 = document.createElement('td');
    td1.textContent = group.fonts.map((font) => font.name).join(', ');

    const td2 = document.createElement('td');
    td2.textContent = group.fonts.length;

    const button1 = document.createElement('button');
    button1.value = group.id;
    button1.className = 'btn';
    button1.title = 'Edit';
    button1.innerHTML = '<i class="bi bi-pen text-info"></i>';
    new Tooltip(button1);

    const button2 = document.createElement('button');
    button2.value = group.id;
    button2.className = 'btn';
    button2.title = 'Delete';
    button2.innerHTML = '<i class="bi bi-trash text-danger"></i>';
    new Tooltip(button2);

    const td3 = document.createElement('td');
    td3.append(button1, button2);

    const tr = document.createElement('tr');
    tr.className = 'align-middle';
    tr.role = 'button';
    tr.dataset.id = group.id;
    tr.append(th, td1, td2, td3);

    this.#elOutput.querySelector('tbody').append(tr);
    this.#elOutput.querySelector('table').classList.remove('d-none');
    this.#elOutput.querySelector('span').classList.add('d-none');
  }

  async #destroy(id) {
    const formData = new FormData();
    formData.append('id', id);

    await fetch('/group/delete', { method: 'POST', body: formData });
    this.#elOutput.querySelector(`tbody tr[data-id="${id}"]`).remove();

    if (this.#elOutput.querySelector('tbody').children.length === 0) {
      this.#elOutput.querySelector('table').classList.add('d-none');
      this.#elOutput.querySelector('span').classList.remove('d-none');
    }
  }
}
