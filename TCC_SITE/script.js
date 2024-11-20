const faculdade = document.getElementById("faculdade");
const programabolsa = document.getElementById("programabolsa");
const facul_text = document.getElementById("facul_none");
const prog_text = document.getElementById("prog_none");

function infFaculdade() {
    if (prog_text.style.display === "flex") {
        prog_text.style.display = "none";
    } else {
        facul_text.style.display = "flex";
    }
}

function disabledFacul() {
    facul_text.style.display = "none";
}

function infPrograma() {
    if (facul_text.style.display === "flex") {
        facul_text.style.display = "none";
    } else {
        prog_text.style.display = "flex";
    }
}

function disabledProg() {
    prog_text.style.display = "none";
}

window.addEventListener('scroll', function() {
    if (facul_text.style.display === "flex") {
        facul_text.style.display = "none";
    }
    if (prog_text.style.display === "flex") {
        prog_text.style.display = "none";
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const carrinho = document.querySelector('.carrinho');
    const sidebar = document.getElementById('sidebar');
    const closeSidebar = document.getElementById('closeSidebar');

    // Alternar a barra lateral ao clicar no ícone do carrinho
    carrinho.addEventListener('click', function() {
        if (sidebar.classList.contains('sidebar-active')) {
            sidebar.classList.remove('sidebar-active'); // Fecha a barra lateral se estiver aberta
        } else {
            sidebar.classList.add('sidebar-active'); // Abre a barra lateral se estiver fechada
        }
    });

    // Fechar a barra lateral ao clicar no botão "Fechar"
    closeSidebar.addEventListener('click', function() {
        sidebar.classList.remove('sidebar-active');
    });
});

function removeFavorite(idFaculdade, idCurso) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Você tem certeza que deseja remover este item dos favoritos?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4ebc6e',
        cancelButtonColor: '#f2513f',
        confirmButtonText: 'Sim, remover!',
        cancelButtonText: 'Cancelar',
        background: '#ffecc7'
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "remove_favorite.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: 'Item removido dos favoritos com sucesso!',
                            confirmButtonColor: '#4ebc6e',
                            background: '#ffecc7',
                            iconColor: '#4ebc6e'
                        }).then(() => {
                            location.reload(); // Recarregar a página para atualizar a lista
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro ao remover item dos favoritos.',
                            confirmButtonColor: '#f2513f',
                            background: '#ffecc7',
                            iconColor: '#f2513f'
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao processar solicitação.',
                        confirmButtonColor: '#f2513f',
                        background: '#ffecc7',
                        iconColor: '#f2513f'
                    });
                }
            };            

            xhr.send("idfaculdade=" + encodeURIComponent(idFaculdade) +
                     "&idcurso=" + encodeURIComponent(idCurso));
        }
    });
}


/* Carrosel */

let index = 0;

function showSlide(n) {
    const slides = document.querySelectorAll('.carousel-images img');
    if (n >= slides.length) index = 0;
    if (n < 0) index = slides.length - 1;
    slides.forEach((slide, i) => {
        slide.style.display = i === index ? 'block' : 'none';
    });
}

function nextSlide() {
    index++;
    showSlide(index);
}

function prevSlide() {
    index--;
    showSlide(index);
}

// Inicializa o carrossel mostrando a primeira imagem
showSlide(index);

// Opcional: muda automaticamente as imagens a cada 5 segundos
setInterval(nextSlide, 5000);