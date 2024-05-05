// VARIABLES GLOBALES
let timer = null;
let hintTimer = null;

let defaultTime = 60;
let defaultHint = 10;

let startingTime = 0;
let hintInterval = 0;
let nbHints = 0;

let chosenConcept = null;
let chosenConceptNet = null;

let stopGame = null;

(function($) {
    // APPLICATION SAMMY.JS
    var app = Sammy('#main', function() {
        this.use(Sammy.Template);

        // JEU DE QUI SUIS-JE?
        this.get('#/jeux/quisuisje', function(context) {
            setUpJeuQuiSuisJe(context, NaN, NaN);
        });

        this.get('#/jeux/quisuisje/:temps', function(context) {
            let temps = this.params.temps;
            setUpJeuQuiSuisJe(context, parseInt(temps), NaN);
        });

        this.get('#/jeux/quisuisje/:temps/:indice', function(context) {
            let temps = this.params.temps;
            let indice = this.params.indice;
            setUpJeuQuiSuisJe(context, parseInt(temps), parseInt(indice));
        });

        // JEU DES MOTS RELIÉS
        this.get('#/jeux/related', function(context) {
            setUpJeuRelated(context, null);
        });

        this.get('#/jeux/related/:temps', function(context) {
            let temps = this.params.temps;
            setUpJeuRelated(context, parseInt(temps));
        });

    });
})(jQuery);

// MÉTHODES DE JEU QUI SUIS-JE? -----------------------------

// Mettre en place les variables du jeu de Qui suis-je.
function setUpJeuQuiSuisJe(context, time, hint) {
    // Vérifier que les valeurs de temps et d'indice sont valables. Sinon les remplacer par les valeurs par défaut.
    if (!isNaN(time) && time > 0) {
        startingTime = time;
    } else {
        startingTime = defaultTime;
    }

    if (!isNaN(hint) && hint > 0) {
        hintInterval = hint;
    } else {
        hintInterval = defaultHint;
    }

    stopGame = stopJeuQuiSuisJe;

    // Mettre en place le template
    let template = $('#jeu-quisuisje-template').html();
    let html = Mustache.render(template);
    context.$element().html(html);

    $('#intro-timer').text(startingTime);
    $('#intro-hint').text(hintInterval);

    $('#jeu-interface').hide();
    $('#btn-start').attr('disabled', true);

    // Aller chercher un concept aléatoirement dans la base de données
    fetchConcept().then(function (concept) {

        // Aller chercher le concept net attaché au concept sélectionné
        fetchConceptNet(concept).then(function (conceptNet) {

            // Commencer la partie avec tous les concepts trouvés
            readyJeu(concept, conceptNet);
        })
        .catch(function (conceptNetError) {
            console.log(conceptNetError);
        })
    })

    // Au cas où MySQL ne marche pas:
    .catch(function (error) {
        console.error('Erreur:', error);

        // Aller chercher un concept aléatoirement dans le tableau backup HTML
        fetchBackupConcept().then(function (backUpConcept) {

            // Aller chercher le concept net attaché au concept sélectionné
            fetchConceptNet(backUpConcept).then(function (conceptNet) {

                // Commencer la partie avec tous les concepts trouvés
                readyJeu(backUpConcept, conceptNet);
            })
            .catch(function (conceptNetError) {
                console.log(conceptNetError);
            })

        })
            .catch(function (backUpError) {
                console.log('Erreur:', backUpError);
            })
    })
    
}

// Commencer une partie du jeu de Qui suis-je.
function startJeuQuiSuisJe() {
    showJeuInterface();
    startTimer(startingTime);
    startHintTimer(hintInterval);
    addNouvelIndice();
}

// Donner un nouvel indice au joueur.
function addNouvelIndice() {
    // Trouver un nouvel indice à travers la liste de ConceptNet trouvé (s'il reste encore des indices disponibles)
    if (chosenConceptNet.length !== 0) {
        let randomIndex = getRandomInt(chosenConceptNet.length);
        let newHint = (chosenConceptNet.splice(randomIndex, 1))[0];

        // Générer l'indice à l'intérieur de la page
        if (newHint.start.term == chosenConcept.start_id) {
            $('#indices').append('<span class="badge badge-light mx-1 mb-1">' +
                '??? '
                + '<span class="hint-relation">'
                + newHint.rel.label
                + '</span> '
                + ' '
                + newHint.end.label
                + '</span>')

        } else if (newHint.end.term == chosenConcept.start_id) {
            $('#indices').append('<span class="badge badge-light mx-1 mb-1">'
                + newHint.start.label
                + ' '
                + '<span class="hint-relation">'
                + newHint.rel.label
                + '</span> '
                + ' ???'
                + '</span>')
        }

        nbHints++;
    }
}

// Soumettre et vérifier une réponse.
function submitReponse() {
    let answer = $('#player-input').val();

    if (answer.toLowerCase() === chosenConcept.start.toLowerCase()) {
        stopGame(true);
    } else {
        $('#player-input').val("");
        showMessage("Faux! Essayer à nouveau!", true);
    }

}

// Arrêter le jeu de Qui suis-je.
function stopJeuQuiSuisJe(win) {
    stopTimer();
    stopHintTimer();

    if (win) {
        let score = calculateScoreJeuQuiSuisJe();
        showMessage("Bravo, vous avez trouvé le bon concept! " +
            "Vous avez obtenu un score de " + score + " points!", false);
        updateScoreUtilisateur(score);
    } else {
        showMessage("Désolé, le temps est écoulé! " +
            "La réponse attendu était: " + chosenConcept.start, false);
    }

    $('#submit-button').attr('disabled', true);
}

// Calculer le score final du joueur pour le jeu de Qui suis-je.
function calculateScoreJeuQuiSuisJe() {
    return Math.ceil(startingTime/hintInterval) - nbHints;
}


// MÉTHODES DE JEU RELATED -----------------------------

// Mettre en place les variables du jeu des mots reliés.
function setUpJeuRelated(context, time) {
    // Valeur par défaut de time
    if (!isNaN(time) && time > 0) {
        startingTime = time;
    } else {
        startingTime = defaultTime;
    }

    stopGame = stopJeuRelated;

    // Mettre en place le template
    let template = $('#jeu-related-template').html();
    let html = Mustache.render(template);
    context.$element().html(html);

    $("#intro-timer").text(startingTime);

    $("#jeu-interface").hide();
    $('#btn-start').attr('disabled', true);
    $("#rundown").hide();

    // Aller chercher un concept aléatoirement dans la base de données
    fetchConcept().then(function (concept) {

        // Aller chercher le concept net attaché au concept sélectionné
        fetchConceptNet(concept).then(function (conceptNet) {

            // Commencer la partie avec tous les concepts trouvés
            readyJeu(concept, conceptNet);
        })
            .catch(function (conceptNetError) {
                console.log(conceptNetError);
            })
    })

        // Au cas où MySQL ne marche pas:
        .catch(function (error) {
            console.error('Erreur:', error);

            // Aller chercher un concept aléatoirement dans le tableau backup HTML
            fetchBackupConcept().then(function (backUpConcept) {

                // Aller chercher le concept net attaché au concept sélectionné
                fetchConceptNet(backUpConcept).then(function (conceptNet) {

                    // Commencer la partie avec tous les concepts trouvés
                    readyJeu(backUpConcept, conceptNet);
                })
                    .catch(function (conceptNetError) {
                        console.log(conceptNetError);
                    })

            })
                .catch(function (backUpError) {
                    console.log('Erreur:', backUpError);
                })
        })

}

// Commencer une partie du jeu des mots reliés.
function startJeuRelated() {
    showJeuInterface();
    $("#chosen-concept").text(chosenConcept.start);
    startTimer(startingTime);
}

// Arrêter le jeu des mots reliés.
function stopJeuRelated() {
    stopTimer();
    let score = calculateScoreJeuRelated();
    showMessage("La partie est terminé! Vous avez obtenu un score de " + score + " points!", false);
    updateScoreUtilisateur(score);
}

// Calculer le score du joueur pour le jeu des mots reliés.
function calculateScoreJeuRelated() {
    let score = 0;

    // Aller chercher les réponses du joueur.
    let playerInput = $("#player-input").val();

    if (playerInput.length > 0) {
        let answers = playerInput.split(",");

        // Faire une liste de toutes les réponses acceptées.
        let relatedConcepts = new Set([]);
        for(let j=0; j < chosenConceptNet.length; j++) {
            let startConcept = chosenConceptNet[j].start.label;
            let endConcept = chosenConceptNet[j].end.label;

            if (!relatedConcepts.has(startConcept)) {
                relatedConcepts.add(startConcept);
            }

            if(!relatedConcepts.has(endConcept)) {
                relatedConcepts.add(endConcept);
            }
        }

        let validConcepts = new Set([]);
        let invalidConcepts = new Set([]);

        // Vérifier les réponses et augmenter le score lorsque nécessaire
        for(let i = 0; i < answers.length; i++) {
            let currentAnswer = answers[i];
            if(currentAnswer.length !== 0) {
                if (relatedConcepts.has(currentAnswer) && !validConcepts.has(currentAnswer)) {
                    score++;
                    validConcepts.add(currentAnswer);
                } else {
                    invalidConcepts.add(currentAnswer);
                }
            }
        }

        showRundown(validConcepts, invalidConcepts);
    }

    return score;
}

function showRundown(validConcepts, invalidConcepts) {
    $('#rundown').show();

    console.log(validConcepts);
    console.log(invalidConcepts);

    validConcepts.forEach(function (concept) {
        $('#valid-concepts').append('<li>' + concept + '</li>')
    });

    invalidConcepts.forEach(function (concept) {
        $('#invalid-concepts').append('<li>' + concept + '</li>')
    });

}


// MÉTHODES DE JEU PARTAGÉES -----------------------------

// Rendre le jeu disponible une fois que toutes les données nécessaires sont assemblées.
function readyJeu(concept, conceptNet) {
    // Standardiser les id de concept (Retirer les attributs excessifs)
    let index = concept.start_id.indexOf('/', 6); // TODO: Fix mysterious

    if (index !== -1) {
        concept.start_id = concept.start_id.substring(0, index);
    }

    // Définir les concepts choisis
    chosenConcept = concept;
    chosenConceptNet = conceptNet;

    // Réactiver le bouton pour commencer la partie
    $('#btn-start').attr('disabled', false);
}

// Faire apparaître l'interface du jeu pour le joueur.
function showJeuInterface() {
    $("#jeu-intro").hide();
    $("#jeu-interface").show();

    // Arrêter les timers au cas où ils sont déjà actifs
    stopTimer();
    stopHintTimer();
}

// Faire apparaître un message pour le joueur.
function showMessage(message, fadeOut) {
    $("#jeu-commentaire").css("display", "block").text(message); // Show the message
    if (fadeOut) {
        setTimeout(function() {
            $("#jeu-commentaire").fadeOut(1000); // Fade out after a delay
        }, 1000); // 1000 milliseconds (1 second) delay before fade out
    }
}


// MÉTHODES DE FETCH -----------------------------

// Aller chercher un concept au hasard dans la base de données MySQL
function fetchConcept() {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: '../server/get_concepts.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                //reject("Debug Backup"); // TODO: REMOVE. THIS IS TO DEBUG THE BACKUP

                if(response.length === 0) {
                    reject("La base de données est vide.");
                }

                // Sélectionner un concept au hasard
                resolve(response[getRandomInt(response.length)]);
            },
            error: function(xhr, status, error) {
                console.error('Erreur:', error);
                reject(error);
            }
        })
    })
}

// Aller chercher un concept au hasard dans le tableau backup HTML
function fetchBackupConcept() {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: '../backup/facts_table.html', // Path to your HTML file
            dataType: 'html', // Data type to expect
            success: function(response) {
                // Trouver tous les concepts à l'intérieur du tableau backup

                if(response.length === 0) {
                    reject("Erreur: le tableau backup HTML est vide.");
                }

                // Sélectionner un concept au hasard
                let concepts = $(response).find('tr');
                let randomConcept = concepts[getRandomInt(concepts.length)];

                // Assembler le concept choisi en tableau associatif
                let fields = [];
                $(response).find("th").each(function () {
                    fields.push($(this).attr('id'));
                })

                let concept = {}
                randomConcept.querySelectorAll('td').forEach(function (td, id) {
                    concept[fields[id]] = td.textContent.trim();
                })

                // Choisir un concept au hasard
                resolve(concept);
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du chargement du tableau backup HTML', error);
                resolve(error);
            }
        });
    })
}

// Aller chercher des concepts
function fetchConceptNet(concept) {
    return new Promise(function (resolve, reject) {
        let language = (concept.start_id.split('/'))[2];
        let queryURL = 'https://api.conceptnet.io' + concept.start_id;

        $.ajax({
            url: queryURL,
            type: 'GET',
            dataType: 'json',
            success: function(response) {

                if(response.edges.length === 0) {
                    reject("Erreur lors de la requête ConceptNet.");
                }

                let filteredResponse = response.edges.filter(edge => edge.end.language === language
                    && edge.start.language === language)

                resolve(filteredResponse);
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du chargement des données:', status, error);
                reject(error);
            }
        });
    })
}


// MÉTHODES DE POST -----------------------------

function updateScoreUtilisateur(score) {
    if (sessionStorage.getItem('username') != null) {
        $.ajax({
            url: '../server/update_score.php',
            method: 'POST',
            data: { username: sessionStorage.username, score: score },
            success: function(response) {
                alert("Votre score a été mis à jour! Votre nouveau score total est: " + response.new_score);
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de la mise à jour du score:', status, error);
            }
        });
    }
}


// MÉTHODES GÉNÉRALES -----------------------------

// Obtenir un nombre au hasard entre 0 et max (exclusif)
function getRandomInt(max) {
    return Math.floor(Math.random() * max);
}


// TIMERS -----------------------------

// Commencer le timer à partir de la valeur time (en secondes.)
function startTimer(time) {
    $("#timer").empty().append(time);

    timer = setInterval(function() {
        time--;
        $("#timer").empty().append(time);

        if(time === 0) {
            stopGame(false);
        }
    }, 1000);
}

// Arrêter le timer
function stopTimer() {
    clearInterval(timer);
}

// Commencer le timer des indices à partir de la valeur time (en secondes.)
function startHintTimer(interval) {
    let time = 0;

    hintTimer = setInterval(function() {
        time++;

        if (time % interval === 0) {
            addNouvelIndice();
        }
    }, 1000);
}

// Commencer le timer des indices à partir de la valeur time (en secondes.)
function stopHintTimer(interval) {
    clearInterval(hintTimer);
}