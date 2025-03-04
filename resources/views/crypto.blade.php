<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfico de Criptomonedas</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#121212] text-white flex flex-col items-center justify-center min-h-screen p-4">
    
    <h1 class="text-4xl font-extrabold mb-6 text-transparent bg-clip-text text-white">
        Gráfico de Criptomonedas
    </h1>

    <div class="mb-6 flex flex-col sm:flex-row items-center gap-4 bg-gray-800 p-4 rounded-lg shadow-lg">
        <label for="cryptoSelect" class="text-lg font-semibold">Selecciona Criptomonedas:</label>
        <select id="cryptoSelect" class="p-2 rounded bg-gray-800 text-white border border-gray-500 hover:bg-gray-700 transition">
            <option value="BTC,ETH,BNB">Bitcoin, Ethereum, BNB</option>
            <option value="ADA,XRP,SOL">Cardano, XRP, Solana</option>
            <option value="DOT,DOGE,LTC">Polkadot, Dogecoin, Litecoin</option>
        </select>
        <button onclick="fetchCryptoData()" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition-transform transform hover:scale-105">
            Cargar Datos
        </button>
    </div>

    <div id="cryptoChart" class="w-full max-w-4xl h-96 bg-gray-900 rounded-lg shadow-lg"></div>

    <script>
async function fetchCryptoData() {
    const selectedCryptos = document.getElementById('cryptoSelect').value;
    
    try {
        const response = await axios.get('/crypto-prices', { params: { symbols: selectedCryptos } });
        const cryptoData = response.data.data;

        const labels = Object.keys(cryptoData);
        const prices = labels.map(symbol => cryptoData[symbol].quote.USD.price);
        const changes = labels.map(symbol => cryptoData[symbol].quote.USD.percent_change_24h);
        const volumes = labels.map(symbol => cryptoData[symbol].quote.USD.volume_24h);

        const formatPrice = price => new Intl.NumberFormat('en-US', {
            style: 'currency', currency: 'USD', minimumFractionDigits: 2
        }).format(price);

        let chartDom = document.getElementById('cryptoChart');
        let myChart = echarts.init(chartDom);

        let option = {
            title: { text: 'Precios de Criptomonedas', left: 'center', textStyle: { color: '#ffffff', fontSize: 22, fontWeight: 'bold' } },
            backgroundColor: '#121212',
            tooltip: {
                trigger: 'axis',
                backgroundColor: '#222',
                borderColor: '#666',
                borderWidth: 1,
                textStyle: { color: '#fff' },
                formatter: params => {
                    let item = params[0];
                    return `${item.name}: ${formatPrice(item.value)}
                            <br> Cambio 24h: ${changes[item.dataIndex].toFixed(2)}%
                            <br> Volumen: ${formatPrice(volumes[item.dataIndex])}`;
                }
            },
            xAxis: { type: 'category', data: labels, axisLabel: { color: '#ffffff', fontWeight: 'bold' } },
            yAxis: { type: 'value', axisLabel: { color: '#ffffff', formatter: value => formatPrice(value) } },
            series: [{
                name: 'Precio', type: 'bar', data: prices,
                itemStyle: { color: params => ['#FF3B3B', '#FFD700', '#1E90FF', '#00FA9A', '#FF69B4'][params.dataIndex % 5] },
                label: { show: true, position: 'top', color: '#ffffff', fontWeight: 'bold', formatter: params => formatPrice(params.value) }
            }]
        };

        myChart.setOption(option);
        window.addEventListener('resize', () => myChart.resize());

    } catch (error) {
        console.error('Error al obtener datos:', error);
    }
}

    fetchCryptoData();
    setInterval(fetchCryptoData, 60000);
    </script>

</body>
</html>
