document.addEventListener('DOMContentLoaded', function () {
    // Sélectionnez les champs de formulaire.
    var fieldCollectivite = document.getElementById('edit-name-0-value');
    var fieldIdentifier = document.getElementById('edit-field-identifier-0-value');
  
    // Ajoutez un gestionnaire d'événement pour le champ "field_collectivite".
    fieldCollectivite.addEventListener('input', function () {
      // Convertissez le texte en majuscules.
      var collectiviteValue = this.value.toUpperCase();
      this.value = collectiviteValue;
  
      // Supprimez les caractères spéciaux et convertissez en minuscules pour le champ "field_identifier".
      var identifierValue = collectiviteValue.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^a-zA-Z0-9]/g, "").toLowerCase();
      fieldIdentifier.value = identifierValue;
    });
  });