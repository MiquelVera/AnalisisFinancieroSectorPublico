
/* Cargamos en un array todos los nombres que usaremos en el autocompletar */
/* Lo hacemos antes de todo, justamente cuando se carga el documento porque así quita retrasos a la hora de escribir */
var nombres;
function cargarNombres(){
    fetch("datos.json").then(response => response.json()).then(result => nombres = result);
    console.log(nombres);
}

function autocomplete(inp) {
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;

    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        
        //El buscador empieza a dar autocompletados a partir de 3 caracteres, para que sea más rápido
        if(val.length > 2){ 
            var related_names=[];
            nombres.map(function(objeto){
                if ((objeto.nombre.toUpperCase()).includes(val.toUpperCase())) { // ***cambiamos arr[i] por nombres[i].nombre ***
                    related_names.push(objeto);
                }
            });
            if(related_names.length){
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "-autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                /*append the DIV element as a child of the autocomplete container:*/
                this.parentNode.appendChild(a);
                related_names.map(function(objeto){
                    /*create a DIV element for each matching element:*/
                    b = document.createElement("div");
                    b.setAttribute("class", "elementos");
                    b.innerHTML = "<a class='lista-autocompletar'>" + objeto.nombre + "</a>";
                    /*insert a input field that will hold the current array item's value:*/
                    b.innerHTML += '<input type="hidden" value="' + objeto.nombre + '">'; // *** cambiamos arr[i] por nombres[i].nombre ***
                    /*execute a function when someone clicks on the item value (DIV element):*/
                    b.addEventListener("click", function(e) {
                        /*insert the value for the autocomplete text field:*/
                        inp.value = this.getElementsByTagName("input")[0].value;
                        /*close the list of autocompleted values,
                        (or any other open lists of autocompleted values:*/
                        closeAllLists();
                    });
                    a.appendChild(b);
                });
            }
        }
    });
    
    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "-autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
            var scelement = document.getElementById(this.id + "-autocomplete-list");
            var limitTop = Math.round(scelement.scrollTop/x[0].offsetHeight);
            var limitBottom = Math.round(scelement.scrollTop/x[0].offsetHeight)+5;
            if (e.key == 'ArrowDown') {
                /*If the arrow DOWN key is pressed,
                increase the currentFocus variable:*/
                currentFocus++;
                /*and and make the current item more visible:*/
                addActive(x);
                if(currentFocus==limitBottom) scelement.scrollTop += x[0].offsetHeight;
                else if (currentFocus == 0) scelement.scrollTop = 0; 
            } else if (e.key == 'ArrowUp') { //up
                /*If the arrow UP key is pressed,
                decrease the currentFocus variable:*/
                currentFocus--;
                /*and and make the current item more visible:*/
                addActive(x);
                if(currentFocus == limitTop) scelement.scrollTop -= x[0].offsetHeight;
                else if(currentFocus == x.length-1) scelement.scrollTop = x[0].offsetHeight*(x.length-1);
            } else if (e.key == 'Enter') {
                /*If the ENTER key is pressed, prevent the form from being submitted,*/
                e.preventDefault();
                if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) x[currentFocus].click();
                }
            }
    });

    function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
    }
    function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }
    function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}