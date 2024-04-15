import './bootstrap.js';
import './styles/app.scss'

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉')

var coll = document.querySelector('.collapsible');
var performance = document.querySelector('.column .performance');

coll.addEventListener('click', function() {
    setTimeout(function() {
        performance.classList.toggle('visible');
    }, 50);
});