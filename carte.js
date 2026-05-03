function afficher(part) {
    document.querySelector('[data-region="' + part + '"]').style.opacity = 1;
}

function masquer(part) {
    document.querySelector('[data-region="' + part + '"]').style.opacity = 0;
}