import axios from "axios";

export default class Like{
  constructor(likeElement){
    this.likeElement = likeElement;

    if(this.likeElement){
      this.init();
    }
  }

  init() {
    this.likeElement.addEventListener('click', this.onClick)
  }

  onClick(event) {
    event.preventDefault();
    const url = this.href;

    axios.get(url).then(res => {

      const heartFilled = this.querySelector('svg.filled');
      const heartUnFilled = this.querySelector('svg.unfilled');

      heartFilled.classList.toggle('hidden');
      heartUnFilled.classList.toggle('hidden');
    })
  }
}