<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Projet 2</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<div class="container pt-4">

    <!--    CONTENU PRINCIPAL   -->
    <div id="main">

    </div>

    <!--    TEMPLATES   -->
    <div id="templates" class="d-none">
        <!--    Template de page d'aide   -->
        <div id="help-page-template">
            <h1>Liste des routes implémentées</h1>
            <hr>
            <p>Voici la liste de toutes les routes que nous avons implémentées pour ce projet. Veuillez prendre en note
                que les mots préfixés de : dans les routes sont des variables; ils doivent être remplacés par d'autres
                pour que les liens soient valides.</p>
            <ul id="pages-implemented">
                <li><a href="#/help">#/help</a></li>
                <li><a href="#/login">#/login</a></li>
                <li><a href="#/logout">#/logout</a></li>
                <li><a href="#/stats">#/stats</a></li>
                <li><a href="#/dump/faits">#/dump/faits</a></li>
                <li><a href="#/concept/:langue/:concept">#/concept/:langue/:concept</a>
                    <ul>
                        <li>Exemple: <a href="#/concept/en/cat">#/concept/en/cat</a></li>
                    </ul>
                </li>
                <li><a href="#/relation/:relation/from/:langue/:concept">#/relation/:relation/from/:langue/:concept</a>
                    <ul>
                        <li>Exemple: <a href="#/relation/IsA/from/en/tree">#/relation/IsA/from/en/tree</a></li>
                    </ul>
                </li>
                <li><a href="#/relation/:relation">#/relation/:relation</a>
                    <ul>
                        <li>Exemple: <a href="#/relation/Synonym">#/relation/Synonym</a></li>
                    </ul>
                </li>
                <li><a href="#/jeux/quisuisje/:temps/:indice">#/jeux/quisuisje/:temps/:indice</a>
                    <ul>
                        <li>Exemple: <a href="#/jeux/quisuisje/30/5">#/jeux/quisuisje/30/5</a></li>
                        <li>Version par défaut: <a href="#/jeux/quisuisje">#/jeux/quisuisje</a></li>
                    </ul>
                </li>
                <li><a href="#/jeux/related/:temps">#/jeux/related/:temps</a>
                    <ul>
                        <li>Exemple: <a href="#/jeux/related/20">#/jeux/related/20</a></li>
                        <li>Version par défaut: <a href="#/jeux/related">#/jeux/related</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <!--    Template de page de connexion   -->
        <div id="login-page-template">
            <h1>Page de connexion</h1>
            <hr>
            <?php
                if (!empty($error)) {
                    echo '<p style="color:red;">' . $error . '</p>';
            }
            ?>
            <form id="login-form" method="post">
                <div class="col-sm-6 col-md-5 col-lg-4">
                    <div class="form-group">
                        <label for="login-form-username">Nom d'utilisateur</label>
                        <input id="login-form-username" class="form-control" type="text" name="username"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="login-form-password">Mot de passe</label>
                        <input id="login-form-password" class="form-control" type="password" name="password"
                               required>
                    </div>
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </div>
            </form>
            <br>
            <p>Retourner à la page d'accueil: <a href="#/help">#/help</a></p>
        </div>

        <!--    Template de page de déconnexion   -->
        <div id="logout-page-template">
            <h1>Page de déconnexion</h1>
            <hr>
            <p>{{logout_message}}</p>
            <p>Retourner à la page d'accueil: <a href="#/help">#/help</a></p>
        </div>

        <!--    Template de page de statistiques   -->
        <div id="stats-page-template">
            <h1>Statistiques de la base</h1>
            <hr>
            <table class="table table-striped table-bordered">
                <tbody>
                <tr>
                    <th>Nombre de faits</th>
                    <td>{{number_of_facts}}</td>
                </tr>
                <tr>
                    <th>Nombre de concepts différents</th>
                    <td>{{number_of_concepts}}</td>
                </tr>
                <tr>
                    <th>Nombre de relations différentes</th>
                    <td>{{number_of_relations}}</td>
                </tr>
                <tr>
                    <th>Nombre d'utilisateurs</th>
                    <td>{{number_of_users}}</td>
                </tr>
                </tbody>
            </table>
            <br>
            <p>Retourner à la page d'accueil: <a href="#/help">#/help</a></p>
        </div>
        <!--    Template de DataTables   -->
        <div id="datatables-template">
            <h1 id="datatables-title">{{title}}</h1>
            <hr>
            <table id="datatables-tab"></table>
            <p>Retourner à la page d'accueil: <a href="#/help">#/help</a></p>
        </div>
        <!--    Template de jeu Quisuisje   -->
        <div id="jeu-quisuisje-template">
            <h1>Jeu: Qui suis-je?</h1>
            <hr>
            <div id="jeu-intro">
                <p>Trouver un concept à partir des indices donnés!</p>
                <p>Vous aurez <span id="intro-timer"></span> secondes pour répondre et vous receverez un nouvel
                    indice toutes les <span id="intro-hint"></span> secondes!</p>
                <button class="btn btn-success" id="btn-start" onclick="startJeuQuiSuisJe()">Commencer une partie!
                </button>
            </div>

            <div id="jeu-interface">
                <div>
                    <span class="font-weight-bold">Temps restant: </span>
                    <span id="timer"></span>
                </div>
                <div>
                    <span class="font-weight-bold">Indices: </span>
                    <span id="indices" class="h5"></span>
                </div>
                <div class="form-group w-25">
                    <label for="player-input"></label>
                    <input type="text" class="form-control" id="player-input" placeholder="Concept...">
                    <button type="" class="btn btn-primary mt-3" id="submit-button" onclick="submitReponse()">Envoyez!
                    </button>
                </div>
                <div class='mt-2' id='jeu-commentaire'></div>
            </div>
        </div>

        <!--    Template de jeu Related   -->
        <div id="jeu-related-template">
            <h1>Jeu des mots reliés.</h1>
            <hr>
            <div id="jeu-intro">
                <p>Trouver le plus de concepts relié à un concept sélectionné au hasard!</p>
                <p>Vous aurez <span id="intro-timer"></span> secondes pour écrire vos réponses! Séparez toutes vos
                    réponses par des virgules et évitez de mettre des espaces après ou avant les virgules.</p>
                <button class="btn btn-success" id="btn-start" onclick="startJeuRelated()">Commencer une partie!
                </button>
            </div>
            <div id="jeu-interface">
                <div>
                    <span class="font-weight-bold">Temps restant: </span>
                    <span id="timer"></span>
                </div>
                <div>
                    <span class="font-weight-bold">Votre concept: </span>
                    <span id="chosen-concept"></span>
                </div>
                <div class="form-group w-50">
                    <label for="player-input"></label>
                    <textarea type="text" class="form-control" id="player-input" placeholder="Concepts..."
                              rows="10"></textarea>
                </div>
                <div class='mt-2' id='jeu-commentaire'></div>
                <br>
                <div id="rundown">
                    <div>Vos bonne réponses:</div>
                    <ul id='valid-concepts'></ul>
                    <div>Vos mauvaises réponses:</div>
                    <ul id='invalid-concepts'></ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/3.1.0/mustache.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sammy.js/0.7.6/sammy.min.js"></script>
    <script src="js/sammy-base.js"></script>
    <script src="js/sammy-concept.js"></script>
    <script src="js/sammy-jeux.js"></script>
</body>

</html>