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

    // Create a new XMLHttpRequest object
    const xhr = new XMLHttpRequest();
    // using open method to specify the HTTP method and URL
    xhr.open('POST', url);
    // type of response is JSON
    xhr.responseType = 'json';
    xhr.onload = function () {
      // check if success
      if (xhr.status === 200) {
        const spanExplore = document.querySelector('span.explore');
        const spanWithdraw = document.querySelector('span.withdraw-explore');

        spanExplore.classList.toggle('hidden');
        spanWithdraw.classList.toggle('hidden');

        /* 
          Creation of a new XMLHTTPRequest because I want to change dymamically 
          a part of my current page according to previous post request
        */
        const xhr2 = new XMLHttpRequest();
        // get my current page
        xhr2.open("GET", window.location.href);
        xhr2.onload = () => {
          if (xhr2.status === 200) {
            const parser = new DOMParser();
            // response in text is converted in DOM object thanks to the parseFromString method
            const htmlDoc = parser.parseFromString(xhr2.responseText, 'text/html');
            // I use querySelector on my new Dom Objet to get my Explorable capsules
            const listExplore = htmlDoc.querySelector('ul.listExplore').innerHTML;

            // Content of listExplore is inserted into HTML element ul.listExplore 
            document.querySelector('ul.listExplore').innerHTML = listExplore;
          }
        };
        xhr2.send();
      } else {
        alert('Request failed.  Returned status of ' + xhr.status);
      }
    };
    xhr.onerror = function () {
      console.log('error');
    };
    xhr.send();
  }
}