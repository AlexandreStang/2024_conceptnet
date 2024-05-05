(function ($) {
  var app = Sammy('#main', function () {
    this.use(Sammy.Template);

    // PAGE D'AIDE
    this.get('#/help', function (context) {
      let template = $('#help-page-template').html();
      let html = Mustache.render(template);
      context.$element().html(html);
    });

    // PAGE DE CONNEXION
    this.get('#/login', function (context) {
      if (sessionStorage.getItem('username') != null) {
        alert("Vous êtes déjà connecté!")
        redirectToPageHelp();
        return;
      }

      let template = $('#login-page-template').html();
      let html = Mustache.render(template);
      context.$element().html(html);

      // Vérifier les informations de l'utilisateur lorsque le formulaire est rempli
      $('#login-form').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
          url: '/server/login.php',
          type: 'POST',
          data: formData,
          success: function (data) {
            var response = JSON.parse(data);
            if (!response.error) {
              alert("Bienvenue, " + response.username + "! Vous allez maintenant redirigé vers la page d'accueil!");
              // Sauvegarder l'utilisateur en session
              sessionStorage.setItem('username', response.username);
              redirectToPageHelp();
            } else {
              alert(response.message);
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            alert('Error: ' + textStatus + ' : ' + errorThrown);
          },
        });
      });
    });

    // PAGE DE DÉCONNEXION
    this.get('#/logout', function (context) {
      // Retirer l'utilisateur de la session
      if (sessionStorage.getItem('username') != null) {
        sessionStorage.removeItem('username');

        let template = $('#logout-page-template').html();
        let html = Mustache.render(template, {
          logout_message: "Vous êtes maintenant déconnecté!"
        });
        context.$element().html(html);
      } else {
        let template = $('#logout-page-template').html();
        let html = Mustache.render(template, {
          logout_message: "Vous devez d'abord être connecté pour pouvoir vous déconnecter!"
        });
        context.$element().html(html);
      }

    });

    // PAGE STATISTIQUES DE LA BASE
    this.get('#/stats', function (context) {
      $.ajax({
        url: '/server/stats.php',
        type: 'GET',
        success: function (data) {
          var response = JSON.parse(data);
          //console.log(response);
          if (!response.error) {
            let template = $('#stats-page-template').html();
            let html = Mustache.render(template, {
              number_of_users: response.number_of_users,
              number_of_concepts: response.number_of_concepts,
              number_of_facts: response.number_of_facts,
              number_of_relations: response.number_of_relations
            });
            context.$element().html(html);
            return;
          } else {
            alert(response.message);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert('Error: ' + textStatus + ' : ' + errorThrown);
        },
      });
    });

    // TABLE DES FAITS STOCKÉS
    this.get('#/dump/faits', function (context) {
      $.ajax({
        url: '/server/faits.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
          //console.log(response);
          renderFaits(context, response, 'Tableau des faits stockés');
        },
        error: function (xhr, status, error) {
          console.error(
            'Erreur lors du chargement des données:',
            status,
            error
          );
        },
      });
    });
  });

  $(function () {
    app.run('#/help');
  });
})(jQuery);

function renderFaits(context, response, title) {
  // Mettre en place le template
  let template = $('#datatables-template').html();
  let html = Mustache.render(template, {
    title: title
  });
  context.$element().html(html);

  // Obtenir les données du tableau
  let data = response.faits.map(function(e) {
    return {
      "start": e.start,
      "relation": e.relation,
      "end": e.end
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

function redirectToPageHelp() {
  window.location.href = '/#/help';
}
