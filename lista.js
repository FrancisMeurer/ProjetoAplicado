document.querySelector('.add').addEventListener('click', () => {
    alert('Adicionar novo usuário');
});

document.querySelector('.back').addEventListener('click', () => {
    alert('Voltando para a página anterior');
});

document.querySelectorAll('.edit').forEach(button => {
    button.addEventListener('click', () => {
        alert('Editar usuário');
    });
});
