<?php

include "conexao.php";
session_start(); // Iniciar a sessão

$idUsuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : null; // Obtém o ID do usuário

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localização do Usuário e Faculdades</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="css/mapa.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    var clientId = '<?php echo htmlspecialchars($idUsuario);?>';

    document.addEventListener('DOMContentLoaded', async () => {
        const map = L.map('map').setView([0, 0], 15);
        const loading = document.getElementById('loading');
        const locationInfo = document.getElementById('location-info');
        loading.style.display = 'block';

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let faculdadesHtml = ''; // Armazenar a lista de faculdades em HTML para reexibir

        try {
            const response = await fetch('get_coordinates.php');
            const data = await response.json();

            console.log(data);

            if (data.user) {
                const userLatLng = [data.user.latitude, data.user.longitude];
                const bounds = new L.LatLngBounds(userLatLng); // Inclua o local do usuário nos limites
                let closestFaculdade = null;
                let closestDistance = Infinity;

                // Ordenar faculdades pela distância do usuário
                const sortedFaculdades = data.faculdades.sort((a, b) => {
                    const distanceA = map.distance(userLatLng, [a.latitude, a.longitude]);
                    const distanceB = map.distance(userLatLng, [b.latitude, b.longitude]);
                    return distanceA - distanceB;
                });

                const userMarker = L.marker(userLatLng).addTo(map)
                    .bindPopup('Localização do Usuário')
                    .on('click', () => {
                        // Exibir informações do usuário
                        locationInfo.innerHTML = `
                            <h2>Informações do Usuário</h2>
                            <p>Logradouro: ${data.user.logradouro || 'Não disponível'}</p>
                            <p>Bairro: ${data.user.bairro || 'Não disponível'}</p>
                            <p>Número: ${data.user.numero || 'Não disponível'}</p>
                            <button id="voltar"><img src="imagens/desfazer.png" alt="Voltar"></button>
                        `;
                        document.getElementById('voltar').addEventListener('click', () => {
                            locationInfo.innerHTML = faculdadesHtml;
                        });
                    })
                    .openPopup();

                sortedFaculdades.forEach(async faculdade => {
                    const faculdadeLatLng = [faculdade.latitude, faculdade.longitude];
                    const distance = map.distance(userLatLng, faculdadeLatLng) / 1000; // Distância em quilômetros

                    const faculdadeMarker = L.marker(faculdadeLatLng).addTo(map)
                        .bindPopup(`
                            <h2>Faculdade: ${faculdade.nome}</h2>
                            <p>Logradouro: ${faculdade.logradouro || 'Não disponível'}</p>
                            <p>Bairro: ${faculdade.bairro || 'Não disponível'}</p>
                            <p>Número: ${faculdade.numero || 'Não disponível'}</p>
                            <p>Distância do Usuário: ${distance.toFixed(2)} km</p>
                        `);

                    faculdadeMarker.on('click', async () => {
                        try {
                            // Fetch courses for the selected college
                            const courseResponse = await fetch(`get_courses.php?id=${faculdade.idfaculdade}`);
                            if (courseResponse.ok) {
                                const coursesHtml = await courseResponse.text();
                                // Exibir informações da faculdade e cursos
                                locationInfo.innerHTML = `
                                    <h2>Informações da Faculdade</h2>
                                    <p>Nome: ${faculdade.nome || 'Não disponível'}</p>
                                    <p>Logradouro: ${faculdade.logradouro || 'Não disponível'}</p>
                                    <p>Bairro: ${faculdade.bairro || 'Não disponível'}</p>
                                    <p>Número: ${faculdade.numero || 'Não disponível'}</p>
                                    <p>Distância do Usuário: ${distance.toFixed(2)} km</p>
                                    <h3>Cursos oferecidos:</h3>
                                    ${coursesHtml}
                                    <button id="voltar"><img src="imagens/desfazer.png" alt="Voltar"></button>
                                `;
                                document.getElementById('voltar').addEventListener('click', () => {
                                    locationInfo.innerHTML = faculdadesHtml;
                                });
                            } else {
                                locationInfo.innerHTML = '<p>Erro ao carregar os cursos.</p>';
                            }
                        } catch (error) {
                            locationInfo.innerHTML = '<p>Erro ao carregar os cursos.</p>';
                        }
                    });

                    faculdadesHtml += `
                        <div class="faculdade-info">
                            <h2>${faculdade.nome}</h2>
                            <p>Logradouro: ${faculdade.logradouro || 'Não disponível'}</p>
                            <p>Bairro: ${faculdade.bairro || 'Não disponível'}</p>
                            <p>Número: ${faculdade.numero || 'Não disponível'}</p>
                            <p>Distância: ${distance.toFixed(2)} km</p>
                        </div>
                    `;

                    if (distance < closestDistance) {
                        closestDistance = distance;
                        closestFaculdade = faculdadeLatLng;
                    }

                    bounds.extend(faculdadeLatLng); // Adicione as coordenadas da faculdade aos limites
                });

                // Atualizar location-info com faculdades ordenadas
                locationInfo.innerHTML = faculdadesHtml;

                map.fitBounds(bounds);

                // Desenhar uma linha entre o usuário e a faculdade mais próxima
                if (closestFaculdade) {
                    L.polyline([userLatLng, closestFaculdade], {
                        color: 'blue'
                    }).addTo(map);

                    // Exibir a distância também no popup do usuário
                    userMarker.bindPopup(`
                        Sua localização<br>
                        Faculdade mais próxima a ${closestDistance.toFixed(2)} km
                    `);
                }
            } else {
                // Caso o usuário não esteja logado
                locationInfo.innerHTML = `
                    <p>Você precisa estar logado para ver a sua localização e as faculdades próximas.</p>
                    <button id="login">Login</button>
                `;
                document.getElementById('login').addEventListener('click', () => {
                    // Redirecionar para a página de login
                    window.location.href = 'login.php';
                });

                // Exibir todas as faculdades sem a localização do usuário
                const sortedFaculdades = data.faculdades.sort((a, b) => {
                    // Ordenar por nome ou algum critério alternativo
                    return a.nome.localeCompare(b.nome);
                });

                sortedFaculdades.forEach(faculdade => {
                    const faculdadeLatLng = [faculdade.latitude, faculdade.longitude];
                    const distance = map.distance([0, 0], faculdadeLatLng) / 1000; // Distância em quilômetros a partir do ponto (0,0)

                    const faculdadeMarker = L.marker(faculdadeLatLng).addTo(map)
                        .bindPopup(`
                            <h2>Faculdade: ${faculdade.nome}</h2>
                            <p>Logradouro: ${faculdade.logradouro || 'Não disponível'}</p>
                            <p>Bairro: ${faculdade.bairro || 'Não disponível'}</p>
                            <p>Número: ${faculdade.numero || 'Não disponível'}</p>
                        `);
                });

                // Ajustar a visualização do mapa para mostrar todas as faculdades
                const bounds = L.latLngBounds(data.faculdades.map(f => [f.latitude, f.longitude]));
                map.fitBounds(bounds);
            }
        } catch (error) {
            console.error('Erro na solicitação:', error);
        } finally {
            loading.style.display = 'none';
        }
    });

    function addToCart(courseId, faculdadeId) {
        if (!clientId) {
            Swal.fire({
                icon: 'warning',
                title: 'Ops...',
                text: 'Por favor, faça login para adicionar cursos ao carrinho.',
                confirmButtonColor: '#f2513f',
                background: '#ffecc7',
                iconColor: '#ff914d'
            });
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "http://localhost/TCC_SITE/add_to_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function() {
            console.log("Resposta do servidor:", xhr.responseText); // Adicionado para depuração

            try {
                var response = JSON.parse(xhr.responseText);

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Curso adicionado ao carrinho com sucesso!',
                        confirmButtonColor: '#4ebc6e',
                        background: '#ffecc7',
                        iconColor: '#4ebc6e'
                    });
                } else if (response.status === 'exists') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Já Adicionado!',
                        text: 'Este curso já foi adicionado ao carrinho.',
                        confirmButtonColor: '#ff914d',
                        background: '#ffecc7',
                        iconColor: '#ff914d'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao adicionar curso ao carrinho.',
                        confirmButtonColor: '#f2513f',
                        background: '#ffecc7',
                        iconColor: '#f2513f'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Resposta do servidor inválida.',
                    confirmButtonColor: '#f2513f',
                    background: '#ffecc7',
                    iconColor: '#f2513f'
                });
                console.error("Erro ao analisar JSON:", e);
            }
        };

        xhr.send("idusuario=" + encodeURIComponent(clientId) +
            "&idfaculdade=" + encodeURIComponent(faculdadeId) +
            "&idcurso=" + encodeURIComponent(courseId));
    }
</script>
</head>
<body>
    <div class="container">
        <div id="map"></div>
        <div id="location">
        <button id="voltar_inicial" onclick="inicialPag();"><img src="imagens/desfazer.png" alt="Voltar"></button>
            <h1>Localização do Usuário e Faculdades</h1>
            <div id="location-info">
                <h1>Faculdades em ordem crescente de distância</h1>
                <!-- Informações das faculdades serão exibidas aqui -->
            </div>
        </div>
    </div>
    <div id="loading">Carregando...</div>

    <script>
        function inicialPag() {
            window.location.href = 'index.php';
        }
    </script>
</body>
</html>

