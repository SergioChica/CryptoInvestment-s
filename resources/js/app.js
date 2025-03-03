async function fetchCryptoData() {
    try {
        const response = await axios.get('/crypto-prices');
        const data = response.data;

        const labels = data.data.map(crypto => crypto.name);
        const prices = data.data.map(crypto => crypto.quote.USD.price);

        updateChart(labels, prices);
    } catch (error) {
        console.error("Error al obtener datos:", error);
        
    }
}

let chart

function updateChart(labels, prices) {
    const ctx = document.getElementById('cryptoChart').getContext('2d');

    if (chart) {
        chart.destroy();
    }

    chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Precio en USD',
                data:prices,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        }
    });
}

setInterval(fetchCryptoData, 60000)