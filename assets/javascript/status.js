import axios from "axios";

export default class Status{
  constructor(statusElement){
    this.statusElement = statusElement;

    if(this.statusElement){
      this.init();
    }
  }

  init() {
    this.statusElement.addEventListener('click', this.onClick);
  }

  onClick(event) {
    // prevent an entire page load
    event.preventDefault();
    const url = this.href;
    console.log(url);

    axios.get(url).then(res => {
      const sealed = this.querySelector('svg.sealed');
      const open = this.querySelector('svg.open');
      const span = this.querySelector('span.status');
      
      span.innerHTML = res.data.status_fr;
      sealed.classList.toggle('hidden');
      open.classList.toggle('hidden');
    })
  }
}