


(function ($) {
  Drupal.behaviors.myModule = {
    attach: function (context, settings) {
      
      document.addEventListener('DOMContentLoaded', function () {
        
        const nomInput = document.getElementById("edit-field-nom-0-value");
        const prenomInput = document.getElementById("edit-field-prenom-0-value");
        const collectiviteInput = document.getElementById("edit-field-collectivite-0-target-id");
        const username = document.getElementById("edit-field-collectivite-0-target-id");
        updateNomUtilisateur();
        
        nomInput.addEventListener("input", updateNomUtilisateur);
        prenomInput.addEventListener("input", updateNomUtilisateur);
        username.addEventListener("input", updateNomUtilisateur);
        
        if (collectiviteInput) {
          collectiviteInput.addEventListener("input", updateNomUtilisateur); 
          collectiviteInput.addEventListener("blur", updateNomUtilisateur);  // Lorsqu'il perd le focus
          collectiviteInput.addEventListener("change", updateNomUtilisateur); // Lorsqu'une valeur est sélectionnée dans la liste
        }
      
        // Sélectionnez la case à cocher et le formulaire
      var checkboxPastell = $('#edit-field-groupes-applicatifs-67');
      var checkboxPastellActes = $('#edit-field-groupes-applicatifs-87');
      var checkboxPastellConvoc = $('#edit-field-groupes-applicatifs-89');
      var checkboxPastellDocs = $('#edit-field-groupes-applicatifs-84');
      var checkboxPastellHelios = $('#edit-field-groupes-applicatifs-86');
      var checkboxPastellChorus = $('#edit-field-groupes-applicatifs-91');
      var userDetailsWrapperPastell = $('#user-details-wrapper');

      var userDetailsActes = $('.form-item--user-details-0-field-role-agent-actes, .form-item--user-details-0-field-role-agent-actes-lecture')
      var userDetailsDocs = $('.form-item--user-details-0-field-role-agent-document')
      var userDetailsHelios = $('.form-item--user-details-0-field-role-agent-helios')
      // Fonction pour afficher ou masquer le formulaire
      function toggleFormVisibilityPastell() {
        if (checkboxPastell.is(':checked')) {
          // Affichez le formulaire si la case à cocher est cochée
          userDetailsWrapperPastell.show();
        } else {
          // Masquez le formulaire si la case à cocher est décochée
          userDetailsWrapperPastell.hide();
        }
      }
      // Fonction pour afficher ou masquer le formulaire
      function toggleFormVisibilityPastellRole() {
        if (checkboxPastellActes.is(':checked')) {
          // Affichez le formulaire si la case à cocher est cochée
          userDetailsActes.show();
          // $('.form-item--user-details-0-field-role-agent-actes').find('input[type="checkbox"]').prop('checked', true);
        } else {
          // Masquez le formulaire si la case à cocher est décochée
          userDetailsActes.hide();
        }
      }
      function toggleFormVisibilityPastellDocs() {
        if (checkboxPastellDocs.is(':checked')) {
          // Affichez le formulaire si la case à cocher est cochée
          userDetailsDocs.show();
          // userDetailsDocs.find('input[type="checkbox"]').prop('checked', true);
        } else {
          // Masquez le formulaire si la case à cocher est décochée
          userDetailsDocs.hide();
        }
      }
      function toggleFormVisibilityPastellHelios() {
        if (checkboxPastellHelios.is(':checked')) {
          // Affichez le formulaire si la case à cocher est cochée
          userDetailsHelios.show();
          // userDetailsDocs.find('input[type="checkbox"]').prop('checked', true);
        } else {
          // Masquez le formulaire si la case à cocher est décochée
          userDetailsHelios.hide();
        }
      }
      
      function updatePastellCheckbox() {
        if (checkboxPastellConvoc.is(':checked') || checkboxPastellActes.is(':checked') || checkboxPastellDocs.is(':checked') || checkboxPastellHelios.is(':checked') || checkboxPastellChorus.is(':checked')) {
            checkboxPastell.prop('checked', true); // coche "Pastell"
        } else {
            checkboxPastell.prop('checked', false); // décoche "Pastell" si aucune autre n'est cochée
        }
    }
    
    // Associe la fonction à chaque case de "Actes", "Docs", "Helios" et "Chorus"
    checkboxPastellConvoc.on('change', updatePastellCheckbox);
    checkboxPastellActes.on('change', updatePastellCheckbox);
    checkboxPastellDocs.on('change', updatePastellCheckbox);
    checkboxPastellHelios.on('change', updatePastellCheckbox);
    checkboxPastellChorus.on('change', updatePastellCheckbox);

      // Vérifiez l'état de la case à cocher au chargement initial de la page
      toggleFormVisibilityPastell()
      toggleFormVisibilityPastellRole()
      toggleFormVisibilityPastellDocs()
      toggleFormVisibilityPastellHelios()

      // Ajoutez un gestionnaire d'événements pour la case à cocher
      checkboxPastell.on('change', toggleFormVisibilityPastell);
      checkboxPastellActes.on('change', toggleFormVisibilityPastellRole);
      checkboxPastellDocs.on('change',  toggleFormVisibilityPastellDocs);
      checkboxPastellHelios.on('change',  toggleFormVisibilityPastellHelios);
function updateNomUtilisateur() {
          let partieCollectivite = '';

          if (collectiviteInput) {
            const selectedText = collectiviteInput.value.split(" (")[0]; // Utilisation de la valeur entrée par l'utilisateur
            partieCollectivite = removeSpecialCharacters(selectedText.toLowerCase());
          }

          const nom = removeSpecialCharacters(nomInput.value);
          const prenom = removeSpecialCharacters(prenomInput.value);
          
          const partiePrenomNom = `${prenom}.${nom}`.toLowerCase();
          document.getElementById("edit-field-nom-0-value").value = nom;
          document.getElementById("edit-field-prenom-0-value").value = prenom;
          document.getElementById("edit-name").value = `${partiePrenomNom}@${partieCollectivite}`;
        }

        // Fonction pour supprimer les caractères spéciaux
        function removeSpecialCharacters(text) {
          return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^a-zA-Z0-9]/g, "");
        }
      });
    }
  };
})(jQuery);
