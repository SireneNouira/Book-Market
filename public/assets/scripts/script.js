function toggleEntrepriseFields() {
  const role = document.getElementById("role").value;
  const extraFields = document.getElementById("extra-fields");
  if (role === "vendeur") {
    extraFields.style.display = "block";
    document.getElementById("nom_entreprise").required = true;
    document.getElementById("adresse_entreprise").required = true;
  } else {
    extraFields.style.display = "none";
    document.getElementById("nom_entreprise").required = false;
    document.getElementById("adresse_entreprise").required = false;
  }
}

// menu
const menuIcon = document.querySelector("#menu");
const sidebar = document.querySelector("#sidebar");

menuIcon.addEventListener("click", (event) => {
    event.preventDefault(); 
    menuIcon.classList.add("hidden");
    sidebar.classList.remove("hidden");
    sidebar.classList.add("flex");
});

// Fermer le menu quand on clique en dehors
document.addEventListener("click", (event) => {
    if (!sidebar.contains(event.target) && !menuIcon.contains(event.target)) {
        sidebar.classList.add("hidden");
        menuIcon.classList.remove("hidden");
        sidebar.classList.remove("flex");
    }
});
