// Appelle la fonction setEvents lorsque le DOM est chargé.
window.addEventListener("DOMContentLoaded", setEvents);

/**
 * Place un écouteur sur le champ "service".
 */
function setEvents() {
    let service = document.getElementById("service");
    if (service === null) {
        return;
    }

    // Appelle la fonction handleServiceChange() lorsque la valeur du champ "service" est modifiée.
    service.addEventListener("change", handleServiceChange);

    // Appelle la fonction handleServiceChange() lorsque la page est chargée.
    let evnt = {target: document.getElementById("service")};
    handleServiceChange(evnt);
}

/**
 * Fait une requête AJAX pour récupérer la liste des descriptions précédemment utilisées.
 *
 * @param Object evnt Un objet issue d'un event représentant le champ "service".
 */
function handleServiceChange(evnt) {
    // Cache avant chaque modification de service le conteneur du selecteur de la description utilisée précédemment.
    let previousDescriptionContainer = document.getElementById("reuse-description-container");
    previousDescriptionContainer.classList = "form-group hidden";

    // Calcule le service id.
    let datalistid = evnt.target.getAttribute("list");
    if (!datalistid) {
        return;
    }

    let item = document.querySelector("#" + datalistid + " option[value='" + evnt.target.value + "']");
    if (!item) {
        return;
    }

    let serviceid = item.getAttribute("data-idservice");

    // Fait la requête AJAX.
    var myHeaders = new Headers();

    var myInit = {
        method: 'GET',
        headers: myHeaders,
        mode: 'cors',
        cache: 'default'
    };

    let url = window.location.href.split('/index.php')[0].replace(/\/+$/, '');
    var myRequest = new Request(url+'/api.php/evenements/description/service/'+serviceid, myInit);

    // Sources:
    //   - https://developer.mozilla.org/fr/docs/Web/API/Fetch_API
    //   - https://developer.mozilla.org/fr/docs/Web/API/Fetch_API/Using_Fetch
    fetch(myRequest, myInit)
        .then(response => {
            // Gère la réponse.
            response.json()
                .then(function(data) {
                    let previousDescriptionContainer = document.getElementById("reuse-description-container");
                    let previousDescriptionSelect = document.getElementById("reuse-description");

                    try {
                        if (data.length === 0) {
                            throw new Error("Aucune description retournée par l'API.");
                        }

                        if (previousDescriptionSelect === null) {
                            throw new Error("L'élément #reuse-description n'existe pas.");
                        }

                        // Ajoute une description vide au début de la liste des élements.
                        if (data[0].value !== "") {
                            data.unshift({"label": "", "value": ""});
                        }

                        // Efface tous les enfants du menu déroulant "descriptions précédemment utilisées".
                        previousDescriptionSelect.replaceChildren();

                        // Ajoute les descriptions dans le menu déroulant "descriptions précédemment utilisées".
                        data.forEach((element) => {
                            let option = document.createElement("option");
                            option.setAttribute("value", element.value);
                            option.textContent = element.label;
                            previousDescriptionSelect.append(option);
                        });

                        // Affiche le menu déroulant "descriptions précédemment utilisées".
                        previousDescriptionContainer.classList = "form-group";

                        // Appelle la fonction handleReuseDescriptionChange() lorsque le menu déroulant "descriptions précédemment utilisées" est modifié.
                        previousDescriptionSelect.addEventListener("change", handleReuseDescriptionChange);
                    } catch (exception) {
                        // Cache le menu déroulant "descriptions précédemment utilisées" en cas d'erreur.
                        previousDescriptionContainer.classList = "form-group hidden";
                    }
                });
        })
        .catch(error => {
            // Gère les erreurs.
            window.console.log(error);
        });
}

/**
 * Remplace la valeur du textarea "description" par la valeur de l'objet passé en paramètre.
 *
 * @param Object evnt Un objet issue d'un event représentant l'élément "option" selectionné du menu déroulant "descriptions précédemment utilisées".
 */
function handleReuseDescriptionChange(evnt) {
    let selectedIndex = evnt.target.selectedIndex;
    let text = evnt.target.options[selectedIndex].value;

    let textarea = document.getElementById("description");
    if (textarea === null) {
        return;
    }

    textarea.value = text;
}
