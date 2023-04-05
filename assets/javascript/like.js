import axios from "axios";

export default class Like{
  constructor(likeElement, listCapsules){
    this.likeElement = likeElement;
    this.listCapsules = listCapsules;

    if(this.likeElement && this.listCapsules){
      this.init();
    }
  }

  init() {
    this.likeElement.addEventListener('click', this.onClick);
  }

  onClick(event) {
    // prevent an entire page load
    event.preventDefault();
    const url = this.href;

    axios.get(url).then(res => {
      console.log(res.data);
      const heartFilled = this.querySelector('svg.filled');
      const heartUnFilled = this.querySelector('svg.unfilled');

      const list = document.querySelector('ul.list');
      
      $("ul.list").load(window.location.href + " ul.list > *");
      
      heartFilled.classList.toggle('hidden');
      heartUnFilled.classList.toggle('hidden');
    })
  }
}