export default class Like {
  constructor(likeElement) {
    this.likeElement = likeElement;

    if (this.likeElement) {
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

    $.ajax({
      type: 'POST',
      url: url,
      dataType: 'json',
      success: () => {
        const heartFilled = this.querySelector('svg.filled');
        const heartUnFilled = this.querySelector('svg.unfilled');
        
        // triggers the load of the list of likes
        $("ul.listOfLikes").load(window.location.href + " ul.listOfLikes > *");

        heartFilled.classList.toggle('hidden');
        heartUnFilled.classList.toggle('hidden');
      },
      error: function (errMsg) {
        swal(
          'Oops...',
          errMsg.responseJSON.body,
          'error'
        )
      }
    });
  }
}