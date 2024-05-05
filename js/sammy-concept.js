(function($) {
    var app = Sammy('#main', function() {
        this.use(Sammy.Template);
        const baseURL = 'https://api.conceptnet.io/query?';

        // TABLE DES FAITS CONCEPTNET
        this.get('#/concept/:langue/:concept', function(context) {
            let langue = this.params.langue;
            let concept = this.params.concept;

            // let query = 'start=/c/' + langue + '/' + concept;
            // let queryURL = baseURL + query + '&limit=1000';

            $.ajax({
                url: '../server/get_facts_start.php',
                method: 'GET',
                dataType: 'json',
                data: {
                    langue: langue,
                    concept: concept
                },
                success: function(response) {
                    renderTemplate(context, response, response['@id']);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors du chargement des données:', status, error);
                }
            });

            // Version original (Appel direct)
            // $.ajax({
            //     url: queryURL,
            //     type: 'GET',
            //     dataType: 'json',
            //     success: function(response) {
            //         console.log(response);
            //         renderTemplate(context, response, query);
            //     },
            //     error: function(xhr, status, error) {
            //         console.error('Erreur lors du chargement des données:', status, error);
            //     }
            // });
        });

        // TABLE DES FAITS CONCEPTNET (affiche les faits ( :langue/ :concept, :relation,x) pour tout x nœud end dans ConceptNet.)
        this.get('#/relation/:relation/from/:langue/:concept', function(context) {
            let relation = this.params.relation;
            let langue = this.params.langue;
            let concept = this.params.concept;

            // let query = 'start=/c/' + langue + '/' + concept + '&rel=/r/' + relation;
            // let queryURL = baseURL + query + '&limit=1000';

            $.ajax({
                url: '../server/get_facts_start_relation.php',
                method: 'GET',
                dataType: 'json',
                data: {
                    langue: langue,
                    concept: concept,
                    relation: relation
                },
                success: function(response) {
                    renderTemplate(context, response, response['@id']);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors du chargement des données:', status, error);
                }
            });

            // Version original (Appel direct)
            // $.ajax({
            //     url: queryURL,
            //     type: 'GET',
            //     dataType: 'json',
            //     success: function(response) {
            //         renderTemplate(context, response, query);
            //     },
            //     error: function(xhr, status, error) {
            //         console.error('Erreur lors du chargement des données:', status, error);
            //     }
            // });

        });

        // TABLE DES FAITS CONCEPTNET (même consigne que la route précédente, à la différence que le concept de départ n’est pas spécifié.)
        this.get('#/relation/:relation', function(context) {
            let relation = this.params.relation;

            // let query = 'rel=/r/' + relation;
            // let queryURL = baseURL + query + '&limit=100';

            $.ajax({
                url: '../server/get_facts_relation.php',
                method: 'GET',
                dataType: 'json',
                data: {
                    relation: relation
                },
                success: function(response) {
                    renderTemplate(context, response, response['@id']);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors du chargement des données:', status, error);
                }
            });

            // Version original (Appel direct)
            // $.ajax({
            //     url: queryURL,
            //     type: 'GET',
            //     dataType: 'json',
            //     success: function(response) {
            //         renderTemplate(context, response, query);
            //     },
            //     error: function(xhr, status, error) {
            //         console.error('Erreur lors du chargement des données:', status, error);
            //     }
            // });
        });
    });
})(jQuery);

function renderTemplate(context, response, title) {
    // Mettre en place le template
    let template = $('#datatables-template').html();
    let html = Mustache.render(template, {
        title: "Tableau " + title
    });
    context.$element().html(html);

    // Obtenir les données du tableau
    let data = response.edges.map(function(e) {
        return {
            "start": e.start.label,
            "relation": e.rel.label,
            "end": e.end.label
        };
    })

    // Insérer les données du tableau
    $('#datatables-tab').DataTable({
        data: data,
        columns: [
            { data: "start", title: "start", width: '33%' },
            { data: "relation", title: "relation", width: '33%' },
            { data: "end", title: "end", width: '33%' }
        ]
    });
}
