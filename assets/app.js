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
// start the Stimulus application
import './bootstrap';

// Tout ce qui se trouve dans cet EventListener va être effectué quand le DOM aura fini de charger
document.addEventListener('DOMContentLoaded', () => {
  const likeElement = document.querySelector('a[data-action="like"]');
  const listCapsules = document.querySelector('ul[class="list"]');

  if(likeElement && listCapsules){
    new Like(likeElement, listCapsules);
  }
})