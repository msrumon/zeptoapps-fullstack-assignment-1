import { Font } from './font.class.js';
import { Group } from './group.class.js';

export class App {
  #font;
  #group;

  constructor(elFile, elFonts, elCreation, elGroups) {
    this.#font = new Font(elFile, elFonts);
    this.#group = new Group(elCreation, elGroups);
  }

  getFont() {
    return this.#font;
  }

  getGroup() {
    return this.#group;
  }

  async initFonts() {
    const httpResponse = await fetch('/font');
    const jsonResponse = await httpResponse.json();

    this.#font.init(jsonResponse.data || []);
  }

  async initGroups() {
    const httpResponse = await fetch('/group');
    const jsonResponse = await httpResponse.json();

    this.#group.init(jsonResponse.data || []);
  }
}
