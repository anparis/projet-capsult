/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/normalize.css'
import './styles/app.css';
import './styles/sakura.css'

//Js
import Like from './javascript/like.js';
import Status from './javascript/status.js';
import Explore from './javascript/explore.js';

// Tout ce qui se trouve dans cet EventListener va être effectué quand le DOM aura fini de charger
document.addEventListener('DOMContentLoaded', () => {
  const likeElement = document.querySelector('a[data-action="like"]');
  const statusElement = document.querySelector('a[data-action="status"]');
  const exploreElement = document.querySelector('a[data-action="explore"]');
  const likeHidden = document.querySelector('button.favorite');
  const exploreHidden = document.querySelector('button.explore-btn');
  const detailsElement = document.querySelector('button.btn-details');
  // const hamburger = document.querySelector(".hamburger");
  // const navMenu = document.querySelector(".nav-menu");
  // const navLink = document.querySelectorAll(".nav-link");

  if (likeElement) {
    new Like(likeElement);
  }

  if (statusElement) {
    new Status(statusElement);
  }

  if (exploreElement) {
    new Explore(exploreElement)
  }

  if (likeHidden) {
    const listOfLikes = document.querySelector(".listOfLikes");

    document.addEventListener('click', (event) => {
      // check if the click target is inside the hidden element
      if (!likeHidden.contains(event.target)) {
        // if not, hide the element
        listOfLikes.style.display = 'none';
      }
    });

    likeHidden.addEventListener('click', function () {
      listOfLikes.style.display = 'block';
    });
  }

  if (exploreHidden) {
    const exploreWrapper = document.querySelector(".explore-wrapper");

    document.addEventListener('click', (event) => {
      // check if the click target is inside the hidden element
      if (!exploreHidden.contains(event.target)) {
        // if not, hide the element
        exploreWrapper.style.display = 'none';
      }
    });

    exploreHidden.addEventListener('click', function () {
      exploreWrapper.style.display = 'block';
    });
  }

  if (detailsElement) {
    const middleInfo = document.querySelector(".middle-info");
    const svg = document.querySelector("svg.rotate");
    let isRotated = true;

    detailsElement.addEventListener('click', (event) => {
      if (isRotated) {
        middleInfo.style.display = 'flex';
        svg.style.transform = "rotate(0deg)";
        isRotated = false;
      } else {
        middleInfo.style.display = 'none';
        svg.style.transform = "rotate(180deg)";

        isRotated = true;
      }
    });
  }

  if(hamburger){
    const mobileMenu = () => {
      hamburger.classList.toggle("active")
      navMenu.classList.toggle("active")
    }
  
    const closeMenu = () => {
      hamburger.classList.remove("active")
      navMenu.classList.remove("active")
    }

    hamburger.addEventListener("click", mobileMenu);
    navLink.forEach((l) => l.addEventListener("click", closeMenu));
  }
})