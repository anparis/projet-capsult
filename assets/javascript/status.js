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

    $.ajax({
      type: 'GET',
      url: url,
      dataType: 'json',
      success: (data) => {
        const sealed = this.querySelector('svg.sealed');
        const open = this.querySelector('svg.open');
        const span = this.querySelector('span.status');

        span.innerHTML = data.status_fr;
        sealed.classList.toggle('hidden');
        open.classList.toggle('hidden');
      },
      error: () => {
        swal(
          'Oops...',
          errMsg.responseJSON.body,
          'error'
        )
      }
    });
  }
}