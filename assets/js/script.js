"use strict";

let nav = document.querySelector("nav"),
    link = document.getElementById("search_link"),
    search = document.getElementById("nav_search"),
    input = document.getElementById("search_input");

link.addEventListener("click", function(){
    search.style.display = "block";
}, true);

search.addEventListener("click", function(){
    this.style.display = "block";
}, true);

nav.addEventListener("click", function(){
    search.style.display = "none";
}, true);