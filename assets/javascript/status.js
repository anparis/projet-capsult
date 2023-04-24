import axios from "axios";

export default class Status {
  constructor(statusElement) {
    this.statusElement = statusElement;

    if (this.statusElement) {
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

    const xhr = new XMLHttpRequest();

    xhr.open('POST',url);

    xhr.responseType = 'json';

    xhr.onload = () => {
      if(xhr.status === 200){

        const sealed = this.querySelector('svg.sealed');
        const open = this.querySelector('svg.open');
        const span = this.querySelector('span.status');
        
        span.innerHTML = xhr.response.status_fr;
        sealed.classList.toggle('hidden');
        open.classList.toggle('hidden');
      }
      else{
        alert('Request failed.  Returned status of ' + xhr.status);
      }
    };
    xhr.send();
  }
}