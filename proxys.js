document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.toggle-proxies');
    const refillButtons = document.querySelectorAll('.refill-btn');
    const copyButtons = document.querySelectorAll('.copy-btn,.btn-copy');
    const replaceButtons = document.querySelectorAll('.btn-replace');

    if (toggleButtons) {
        toggleButtons.forEach(button => {
            button.addEventListener('click', function () {
                const proxyList = this.nextElementSibling;
                proxyList.style.display = proxyList.style.display === 'none' ? 'block' : 'none';
            });
        });
    }

    if (refillButtons) {
        refillButtons.forEach(button => {
            button.addEventListener('click', function () {
                alert('Refill button clicked for proxy: ' + this.previousElementSibling.textContent.trim());
                // Aqui você pode adicionar a lógica para enviar a solicitação de reposição ao backend, se necessário
            });
        });
    }

    if (copyButtons) {
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
    }

    if (replaceButtons) {
        replaceButtons.forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.dataset.orderId || this.getAttribute('data-order-id');
                const serviceId = this.dataset.serviceId || this.getAttribute('data-service-id');
                const proxyUrl = this.dataset.proxy || this.getAttribute('data-proxy');

                // Adicionar controle de clique único
                if (!this.classList.contains('clicked')) {
                    this.classList.add('clicked');
                    refillOrder(orderId, serviceId, proxyUrl);
                }
            });
        });
    }
});

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
            // Remover controle de clique único
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
            // Remover controle de clique único
            document.querySelector('.btn-replace.clicked').classList.remove('clicked');
        });
}

// Modificar a parte do clique do botão de reposição para incluir o proxy URL
document.addEventListener('DOMContentLoaded', function () {
    const replaceButtons = document.querySelectorAll('.btn-replace');

    if (replaceButtons) {
        replaceButtons.forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.dataset.orderId || this.getAttribute('data-order-id');
                const serviceId = this.dataset.serviceId || this.getAttribute('data-service-id');
                const proxyUrl = this.dataset.proxy || this.getAttribute('data-proxy');

                // Adicionar controle de clique único
                if (!this.classList.contains('clicked')) {
                    this.classList.add('clicked');
                    refillOrder(orderId, serviceId, proxyUrl);
                }
            });
        });
    }
});