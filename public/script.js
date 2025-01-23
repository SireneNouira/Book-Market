function toggleEntrepriseFields() {
    const role = document.getElementById('role').value;
    const extraFields = document.getElementById('extra-fields');
    if (role === 'vendeur') {
        extraFields.style.display = 'block';
        document.getElementById('nom_entreprise').required = true;
        document.getElementById('adresse_entreprise').required = true;
    } else {
        extraFields.style.display = 'none';
        document.getElementById('nom_entreprise').required = false;
        document.getElementById('adresse_entreprise').required = false;
    }
}
