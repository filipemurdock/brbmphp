document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.toggle-proxies');
    const refillButtons = document.querySelectorAll('.refill-btn');
    const copyButtons = document.querySelectorAll('.copy-btn,.btn-copy');
    const replaceButtons = document.querySelectorAll('.btn-replace');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const proxyList = this.nextElementSibling;
            proxyList.style.display = proxyList.style.display === 'none' ? 'block' : 'none';
        });
    });

    refillButtons.forEach(button => {
        button.addEventListener('click', function () {
            alert('Refill button clicked for proxy: ' + this.previousElementSibling.textContent.trim());
        });
    });

    copyButtons.forEach(button => {
        button.addEventListener('click', function () {
            const proxy = this.dataset.proxy || this.getAttribute('data-proxy');
            navigator.clipboard.writeText(proxy).then(() => {
                Swal.fire('Copiado!', 'Proxy copiado para a área de transferência.', 'success');
            }).catch(err => {
                console.error('Erro ao copiar o proxy: ', err);
            });
        });
    });

    replaceButtons.forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.dataset.orderId || this.getAttribute('data-order-id');
            const serviceId = this.dataset.serviceId || this.getAttribute('data-service-id');
            const proxyUrl = this.dataset.proxy || this.getAttribute('data-proxy');

            if (!this.classList.contains('clicked')) {
                this.classList.add('clicked');
                refillOrder(orderId, serviceId, proxyUrl);
            }
        });
    });

    function updateProxies() {
        fetch('/api/update_proxies.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Erro ao buscar dados:', data.error);
                    return;
                }
                updateTable(data);
                // Verificar se há algum pedido que não esteja completado
                const hasPendingOrInProgress = data.some(order => 
                    order.order_status === 'pending' || order.order_status === 'inprogress'
                );

                if (!hasPendingOrInProgress) {
                    clearInterval(window.updateProxiesInterval);
                    window.updateProxiesInterval = null;
                }
            })
            .catch(error => console.error('Erro:', error));
    }

    function updateTable(orders) {
        const container = document.getElementById('orders-container');
        container.innerHTML = ''; // Limpa o conteúdo atual

        orders.forEach(order => {
            let orderDiv = document.createElement('div');
            orderDiv.className = 'gcard mb-3';

            let headerDiv = document.createElement('div');
            headerDiv.className = 'card-header';

            headerDiv.innerHTML = `<strong class="service-name">${order.service_name}</strong>`;
            orderDiv.appendChild(headerDiv);

            let bodyDiv = document.createElement('div');
            bodyDiv.className = 'card-body';
            bodyDiv.innerHTML = `
                <p><strong>Ordem ID:</strong> ${order.order_id}</p>
                <p><strong>Status do Pedido:</strong> <span class="g-status ${order.order_status.replace(/ /g, '')}">
                    ${order.order_status === 'inprogress' ? 'Em Progresso' :
                    order.order_status === 'pending' ? 'Pendente' :
                    order.order_status === 'partial' ? 'Parcial' :
                    order.order_status === 'canceled' ? 'Cancelado' :
                    order.order_status === 'processing' ? 'Processando' :
                    order.order_status === 'completed' ? 'Concluído' : ''}
                </span></p>
                <p>Elegível para Troca: ${order.eligible_for_refill === '1' ? '1 Troca' :
                    order.eligible_for_refill === '2' ? '2 Trocas' : 'Não'}
                </p>
                <button class="btn btn-info btn-show-proxies" data-toggle="collapse" data-target="#order-${order.order_id}-proxies">Show Proxies</button>
                <div id="order-${order.order_id}-proxies" class="collapse mt-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Proxy URL</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${order.proxies.map(proxy => `
                                <tr>
                                    <td>${proxy}</td>
                                    <td>
                                        <button class="btn btn-blue btn-copy ri-file-copy-line me-2 btn-md mr-1" data-proxy="${proxy}">
                                            <i class="ri-file-copy-line me-2"></i>
                                            <span></span> 
                                        </button>
                                       ${(order.order_status === 'completed' || order.order_status === 'concluido') &
 											 (order.eligible_for_refill === "1" || order.eligible_for_refill === "2") ? `
                                            <button class="btn btn-warning btn-replace" data-proxy="${proxy}" data-order-id="${order.order_id}" data-service-id="${order.service_id}">
                                                <i class="ri-restart-line me-2"></i>
                                                <span></span>
                                            </button>` : ''}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
            orderDiv.appendChild(bodyDiv);
            container.appendChild(orderDiv);
        });

        applyEventListeners();
    }

    function applyEventListeners() {
        const toggleButtons = document.querySelectorAll('.toggle-proxies');
        const refillButtons = document.querySelectorAll('.refill-btn');
        const copyButtons = document.querySelectorAll('.copy-btn,.btn-copy');
        const replaceButtons = document.querySelectorAll('.btn-replace');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function () {
                const proxyList = this.nextElementSibling;
                proxyList.style.display = proxyList.style.display === 'none' ? 'block' : 'none';
            });
        });

        refillButtons.forEach(button => {
            button.addEventListener('click', function () {
                alert('Refill button clicked for proxy: ' + this.previousElementSibling.textContent.trim());
            });
        });

        copyButtons.forEach(button => {
            button.addEventListener('click', function () {
                const proxy = this.dataset.proxy || this.getAttribute('data-proxy');
                navigator.clipboard.writeText(proxy).then(() => {
                    Swal.fire('Copiado!', 'Proxy copiado para a área de transferência.', 'success');
                }).catch(err => {
                    console.error('Erro ao copiar o proxy: ', err);
                });
            });
        });

        replaceButtons.forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.dataset.orderId || this.getAttribute('data-order-id');
                const serviceId = this.dataset.serviceId || this.getAttribute('data-service-id');
                const proxyUrl = this.dataset.proxy || this.getAttribute('data-proxy');

                if (!this.classList.contains('clicked')) {
                    this.classList.add('clicked');
                    refillOrder(orderId, serviceId, proxyUrl);
                }
            });
        });
    }

    function refillOrder(orderId, serviceId, proxyUrl) {
        fetch('/register_refill_proxy.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order_id: orderId, service_id: serviceId, proxy_url: proxyUrl })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Reposição Solicitada, o Suporte abrirá um ticket!',
                    showConfirmButton: true,
                    confirmButtonText: 'Okay'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao realizar reposição.',
                    text: data.message || 'Erro desconhecido',
                    showConfirmButton: true,
                    confirmButtonText: 'Okay'
                });
                if (data.error) {
                    console.error('Erro do Banco de Dados:', data.error);
                }
            }
            document.querySelector('.btn-replace.clicked').classList.remove('clicked');
        })
        .catch((error) => {
            console.error('Erro:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro ao realizar reposição.',
                showConfirmButton: true,
                confirmButtonText: 'Okay'
            });
            document.querySelector('.btn-replace.clicked').classList.remove('clicked');
        });
    }

    // Evitar múltiplas chamadas de setInterval
    if (!window.updateProxiesInterval) {
        window.updateProxiesInterval = setInterval(updateProxies, 60000);
    }

    // Atualizar a tabela imediatamente na carga da página
    updateProxies();
});
