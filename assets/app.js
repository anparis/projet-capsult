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
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.querySelector(".nav-menu");
  const navLink = document.querySelectorAll(".nav-link");
  const navDropdowns = document.querySelectorAll('.nav-dropdown');
  console.log(navDropdowns);

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

  if(navDropdowns){
    // If a link has a dropdown, add sub menu toggle.
    document.querySelectorAll('nav ul li a:not(:only-child)').forEach(function(link) {
      link.addEventListener('click', function(e) {
        // Toggle the visibility of the sibling nav-dropdown
        const dropdown = this.nextElementSibling;
        dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';

        // Close other dropdowns
        navDropdowns.forEach(function(navDropdown) {
          if (navDropdown !== dropdown) {
            navDropdown.style.display = 'none';
          }
        });

        e.stopPropagation();
      });
    });

    // Clicking away from dropdown will remove the dropdown class
    document.addEventListener('click', function() {
      navDropdowns.forEach(function(navDropdown) {
        navDropdown.style.display = 'none';
      });
    });

    // Toggle open and close nav styles on click
    const navToggle = document.getElementById('nav-toggle');
    const navList = document.querySelector('nav ul');

    navToggle.addEventListener('click', function() {
      if (navList.style.display === 'block') {
        navList.style.display = 'none';
      } else {
        navList.style.display = 'flex';
      }
    });
  }

})