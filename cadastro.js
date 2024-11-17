let userId = 1;

// Função para carregar o próximo ID
function obterProximoId() {
    const idField = document.getElementById('user-id');
    idField.value = userId;
}

// Função para pré-visualizar a foto
function carregarFoto() {
    const input = document.getElementById('upload-photo');
    const previewImg = document.getElementById('preview-img');

    const file = input.files[0];
    if (file) {
        const reader = new FileReader();

        reader.onload = (e) => {
            previewImg.src = e.target.result;
        };

        reader.readAsDataURL(file);
    }
}


