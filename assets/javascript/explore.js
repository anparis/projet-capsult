import axios from "axios";

export default class Explore {
  constructor(exploreElement) {
    this.exploreElement = exploreElement;
    if (this.exploreElement) {
      this.init();
    }
  }

  init() {
    this.exploreElement.addEventListener('click', this.onClick);
  }

  onClick(event) {
    // prevent an entire page load
    event.preventDefault();

    const url = this.href;
    console.log(url);

    $.ajax({
      type: 'POST',
      url: url,
      dataType: 'json',
      success: () => {
        const spanExplore = this.querySelector('span.explore');
        const spanWithdraw = this.querySelector('span.withdraw-explore');

        $("ul.listExplore").load(window.location.href + " ul.listExplore > *");
        spanExplore.classList.toggle('hidden');
        spanWithdraw.classList.toggle('hidden');
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