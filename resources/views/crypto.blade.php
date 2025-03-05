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
    
    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold mb-4 sm:mb-6 text-center">
        Gráfico de Criptomonedas
    </h1>

    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 mb-4 w-full max-w-lg">
        <input type="text" id="searchCrypto" placeholder="Buscar criptomoneda..."
            class="p-2 w-full bg-gray-800 text-white rounded-lg border border-gray-600 focus:outline-none focus:ring-2 focus:ring-red-600">
        
        <select id="periodSelector" class="p-2 bg-gray-800 text-white rounded-lg w-full sm:w-auto">
            <option value="7">Última Semana</option>
        </select>
    </div>

    <div id="cryptoChart" class="w-full max-w-4xl h-64 sm:h-80 md:h-96 bg-gray-900 rounded-lg shadow-lg"></div>
    
    <div id="cryptoStats" class="mt-4 w-full max-w-4xl grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4">
        <div class="bg-gray-800 p-2 sm:p-3 rounded-lg text-center">
            <h3 class="text-xs sm:text-sm text-gray-400">Precio Actual</h3>
            <p id="currentPrice" class="text-base sm:text-[18px] font-bold text-green-400">-</p>
        </div>
        <div class="bg-gray-800 p-2 sm:p-3 rounded-lg text-center">
            <h3 class="text-xs sm:text-sm text-gray-400">Cambio 24h</h3>
            <p id="priceChange" class="text-base sm:text-[18px]">-</p>
        </div>
        <div class="bg-gray-800 p-2 sm:p-3 rounded-lg text-center">
            <h3 class="text-xs sm:text-sm text-gray-400">Volumen 24h</h3>
            <p id="volume24h" class="text-base sm:text-[18px]">-</p>
        </div>
        <div class="bg-gray-800 p-4 sm:p-3 rounded-lg text-center">
            <h3 class="text-xs sm:text-sm text-gray-400">Cap. Mercado</h3>
            <p id="marketCap" class="text-base sm:text-[18px]">-</p>
        </div>
    </div>

    <div id="updateStatus" class="mt-2 text-sm text-gray-400">
        Última actualización: <span id="lastUpdateTime">-</span>
    </div>

    <script>
        
        let myChart = null;
        let cryptoData = null; // Global variable to store crypto data
        let updateInterval = null; // To store the interval reference

        const formatPrice = price => new Intl.NumberFormat('en-US', {
            style: 'currency', currency: 'USD', minimumFractionDigits: 5
        }).format(price);

        const formatNumber = (number) => new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        }).format(number);

        async function fetchCryptoData() {
            try {
                // Fetch current crypto prices
                const currentResponse = await axios.get('/crypto-prices');
                cryptoData = currentResponse.data;
                console.log('Crypto data loaded:', cryptoData);
                return cryptoData;
            } catch (error) {
                console.error('Error fetching crypto prices:', error);
                throw error;
            }
        }

        async function fetchHistoricalData() {
            try {
                // Ensure crypto data is loaded
                if (!cryptoData) {
                    await fetchCryptoData();
                }

                const period = document.getElementById('periodSelector').value;
                const symbol = document.getElementById('searchCrypto').value.toUpperCase() || 'AAVE';
                
                // Verify symbol exists
                if (!cryptoData[symbol]) {
                    console.error(`Símbolo ${symbol} no encontrado. Símbolos disponibles:`, Object.keys(cryptoData));
                    alert(`Símbolo ${symbol} no encontrado. Prueba con: ${Object.keys(cryptoData).join(', ')}`);
                    return;
                }

                const currentCryptoData = cryptoData[symbol];
                

                // Update stats
                document.getElementById('currentPrice').textContent = formatPrice(currentCryptoData.quote.USD.price);
                document.getElementById('priceChange').textContent = formatNumber(currentCryptoData.quote.USD.percent_change_24h) + '%';
                document.getElementById('volume24h').textContent = formatPrice(currentCryptoData.quote.USD.volume_24h);
                document.getElementById('marketCap').textContent = formatPrice(currentCryptoData.quote.USD.market_cap);
                
                // Determine chart data
                let chartTitle = '';
                let historicalPrices = [];

                
                const historicalResponse = await axios.get(`/crypto-historical-data?symbol=${symbol}&days=${period}`);
                chartTitle = `Precio Histórico de ${symbol} (Últimos ${period} días)`;
                
                historicalPrices = historicalResponse.data.map(item => [
                    Date.parse(item.recorded_at),
                    item.price
                ]);

                // Configure chart
                let chartDom = document.getElementById('cryptoChart');
                if (!myChart) {
                    myChart = echarts.init(chartDom);
                }

                let option = {
                    title: { 
                        text: chartTitle, 
                        left: 'center', 
                        textStyle: { color: '#ffffff', fontSize: window.innerWidth < 640 ? 16 : 22, fontWeight: 'bold' } 
                    },
                    backgroundColor: '#121212',
                    tooltip: { 
                        trigger: 'axis', 
                        backgroundColor: '#222', 
                        borderColor: '#666', 
                        borderWidth: 1, 
                        textStyle: { color: '#fff' },
                        formatter: params => {
                            const item = params[0];
                            const localDate = new Date(item.value[0]);
                            const fechaColombia = new Date(localDate.getTime() - (localDate.getTimezoneOffset() * 60000));
                            return `Fecha: ${fechaColombia.toLocaleString("es-CO", { timeZone: "America/Bogota" })}
                                    <br>Precio: ${formatPrice(item.value[1])}`;
                        }
                    },
                    xAxis: { 
                        type: 'time', 
                        axisLabel: { 
                            color: '#ffffff', 
                            fontWeight: 'bold',
                            fontSize: window.innerWidth < 640 ? 10 : 12,
                            formatter: (value) => {
                                const date = new Date(value);
                                return date.toLocaleDateString();
                            }
                        } 
                    },
                    yAxis: { 
                        type: 'value', 
                        axisLabel: { 
                            color: '#ffffff',
                            fontSize: window.innerWidth < 640 ? 10 : 12
                        },
                        scale: true 
                    },
                    series: [{
                        name: 'Precio', 
                        type: 'line', 
                        data: historicalPrices,
                        areaStyle: { color: 'rgba(255, 99, 132, 0.5)' },
                        itemStyle: { color: '#FF3B3B' },
                        lineStyle: { width: 2 },
                        symbol: period === '0' ? 'circle' : 'none'
                    }]
                };

                myChart.setOption(option);
                window.addEventListener('resize', () => {
                    myChart.resize();
                    option.title.textStyle.fontSize = window.innerWidth < 640 ? 16 : 22;
                    option.xAxis.axisLabel.fontSize = window.innerWidth < 640 ? 10 : 12;
                    option.yAxis.axisLabel.fontSize = window.innerWidth < 640 ? 10 : 12;
                    myChart.setOption(option);
                });

            } catch (error) {
                console.error('Error al obtener datos:', error);
                console.error('Detalles del error:', error.response?.data);
            }
        }

        // Function to start real-time updates
        function startRealTimeUpdates() {
            // Clear any existing interval
            if (updateInterval) {
                clearInterval(updateInterval);
            }

            // Set up new interval for updates every 20 seconds
            updateInterval = setInterval(async () => {
                try {
                    // Reload crypto data
                    await fetchCryptoData();
                    
                    // Reload historical data with current selection
                    await fetchHistoricalData();

                    const currentTime = new Date()
                    document.getElementById('lastUpdateTime').textContent = currentTime.toLocaleString("es-CO", { timeZone: "America/Bogota" });
                    

                } catch (error) {
                    console.error('Error in real-time update:', error);
                }
            }, 120000); 
        }

        // Stop real-time updates when needed
        function stopRealTimeUpdates() {
            if (updateInterval) {
                clearInterval(updateInterval);
                updateInterval = null;
            }
        }

        // Search event
        document.getElementById('searchCrypto').addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                fetchHistoricalData();
            }
        });

        // Period change event
        document.getElementById('periodSelector').addEventListener('change', fetchHistoricalData);

        // Initialize
        fetchCryptoData()
            .then(fetchHistoricalData)
            .then(startRealTimeUpdates);

    </script>
</body>
</html>