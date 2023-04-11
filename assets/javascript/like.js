import axios from "axios";

export default class Like{
  constructor(likeElement){
    this.likeElement = likeElement;

    if(this.likeElement){
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
      const heartFilled = this.querySelector('svg.filled');
      const heartUnFilled = this.querySelector('svg.unfilled');

      $("ul.listOfLikes").load(window.location.href + " ul.listOfLikes > *");
      
      heartFilled.classList.toggle('hidden');
      heartUnFilled.classList.toggle('hidden');
    })
  }
}