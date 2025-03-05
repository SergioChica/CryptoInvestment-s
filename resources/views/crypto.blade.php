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
<body class="bg-[#121212] text-white flex flex-col items-center justify-center min-h-screen p-2 md:p-6">
    
    <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-extrabold mb-3 sm:mb-5 text-center">
        Gráfico de Criptomonedas
    </h1>

    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 mb-4 w-full max-w-lg px-3 sm:px-0">
        <input type="text" id="searchCrypto" placeholder="Buscar criptomoneda..."
            class="p-2 w-full bg-gray-800 text-white rounded-lg border border-gray-600 focus:outline-none focus:ring-2 focus:ring-red-600">
        
        <select id="periodSelector" class="p-2 bg-gray-800 text-white rounded-lg w-full sm:w-auto">
            <option value="7">Última Semana</option>
            <option value="14">Últimos 14 días</option>
            <option value="30">Último Mes</option>
        </select>
    </div>

    <div id="cryptoChart" class="w-full max-w-4xl h-56 sm:h-64 md:h-80 lg:h-96 bg-gray-900 rounded-lg shadow-lg mx-2"></div>
    
    <div id="cryptoStats" class="mt-3 sm:mt-4 w-full max-w-4xl grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4 px-2 sm:px-0">
        <div class="bg-gray-800 p-2 sm:p-3 rounded-lg text-center">
            <h3 class="text-xs sm:text-sm text-gray-400">Precio Actual</h3>
            <p id="currentPrice" class="text-sm sm:text-base md:text-lg font-bold text-green-400">-</p>
        </div>
        <div class="bg-gray-800 p-2 sm:p-3 rounded-lg text-center">
            <h3 class="text-xs sm:text-sm text-gray-400">Cambio 24h</h3>
            <p id="priceChange" class="text-sm sm:text-base md:text-lg font-medium">-</p>
        </div>
        <div class="bg-gray-800 p-2 sm:p-3 rounded-lg text-center">
            <h3 class="text-xs sm:text-sm text-gray-400">Volumen 24h</h3>
            <p id="volume24h" class="text-sm sm:text-base md:text-lg">-</p>
        </div>
        <div class="bg-gray-800 p-2 sm:p-3 rounded-lg text-center">
            <h3 class="text-xs sm:text-sm text-gray-400">Cap. Mercado</h3>
            <p id="marketCap" class="text-sm sm:text-base md:text-lg">-</p>
        </div>
    </div>

    <div id="updateStatus" class="mt-2 text-xs sm:text-sm text-gray-400 text-center">
        Última actualización: <span id="lastUpdateTime">-</span>
    </div>

    <script>
        
        let myChart = null;
        let cryptoData = null; // Global variable to store crypto data
        let updateInterval = null; // To store the interval reference

        const formatPrice = price => new Intl.NumberFormat('en-US', {
            style: 'currency', currency: 'USD', 
            minimumFractionDigits: price < 0.1 ? 5 : 2,
            maximumFractionDigits: price < 0.1 ? 5 : 2
        }).format(price);

        const formatNumber = (number) => new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        }).format(number);

        // Función para establecer el color del cambio porcentual
        const setPriceChangeColor = (change) => {
            const priceChangeElement = document.getElementById('priceChange');
            if (change > 0) {
                priceChangeElement.classList.add('text-green-400');
                priceChangeElement.classList.remove('text-red-400');
                priceChangeElement.textContent = '+' + formatNumber(change) + '%';
            } else {
                priceChangeElement.classList.add('text-red-400');
                priceChangeElement.classList.remove('text-green-400');
                priceChangeElement.textContent = formatNumber(change) + '%';
            }
        };

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
                    alert(`Símbolo ${symbol} no encontrado. Prueba con: ${Object.keys(cryptoData).slice(0, 5).join(', ')}...`);
                    return;
                }

                const currentCryptoData = cryptoData[symbol];
                
                // Update stats
                document.getElementById('currentPrice').textContent = formatPrice(currentCryptoData.quote.USD.price);
                setPriceChangeColor(currentCryptoData.quote.USD.percent_change_24h);
                document.getElementById('volume24h').textContent = formatPrice(currentCryptoData.quote.USD.volume_24h);
                document.getElementById('marketCap').textContent = formatPrice(currentCryptoData.quote.USD.market_cap);
                
                // Determine chart data
                let chartTitle = '';
                let historicalPrices = [];

                const historicalResponse = await axios.get(`/crypto-historical-data?symbol=${symbol}&days=${period}`);
                chartTitle = `${symbol} - ${period} días`;
                
                historicalPrices = historicalResponse.data.map(item => [
                    Date.parse(item.recorded_at),
                    item.price
                ]);

                // Configure chart
                let chartDom = document.getElementById('cryptoChart');
                if (!myChart) {
                    myChart = echarts.init(chartDom);
                }

                // Determinar el tamaño de fuente basado en el ancho de la ventana
                const isMobile = window.innerWidth < 640;
                const fontSizeTitle = isMobile ? 14 : 18;
                const fontSizeAxis = isMobile ? 10 : 12;

                let option = {
                    title: { 
                        text: chartTitle, 
                        left: 'center', 
                        textStyle: { color: '#ffffff', fontSize: fontSizeTitle, fontWeight: 'bold' } 
                    },
                    backgroundColor: 'transparent',
                    grid: {
                        left: '8%',
                        right: '5%',
                        top: '15%',
                        bottom: '10%',
                        containLabel: true
                    },
                    tooltip: { 
                        trigger: 'axis', 
                        backgroundColor: 'rgba(25, 25, 25, 0.9)', 
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
                            fontWeight: 'normal',
                            fontSize: fontSizeAxis,
                            formatter: (value) => {
                                const date = new Date(value);
                                const format = isMobile ? 'dd/MM' : 'dd/MM/yy';
                                return date.toLocaleDateString("es-ES", {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: isMobile ? undefined : '2-digit'
                                });
                            }
                        },
                        splitLine: {
                            show: false,
                        }
                    },
                    yAxis: { 
                        type: 'value', 
                        axisLabel: { 
                            color: '#ffffff',
                            fontSize: fontSizeAxis,
                            formatter: (value) => {
                                return isMobile ? 
                                    '$' + value.toLocaleString('en-US', {notation: 'compact', maximumFractionDigits: 2}) :
                                    '$' + value.toLocaleString('en-US');
                            }
                        },
                        scale: true,
                        splitLine: {
                            show: true,
                            lineStyle: {
                                color: 'rgba(255, 255, 255, 0.1)',
                                type: 'dashed'
                            }
                        }
                    },
                    series: [{
                        name: 'Precio', 
                        type: 'line', 
                        data: historicalPrices,
                        smooth: true,
                        showSymbol: false,
                        areaStyle: { 
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: 'rgba(255, 59, 59, 0.5)' },
                                { offset: 1, color: 'rgba(255, 59, 59, 0.05)' }
                            ])
                        },
                        itemStyle: { color: '#FF3B3B' },
                        lineStyle: { width: 3, color: '#FF3B3B' },
                        symbol: 'circle'
                    }]
                };

                myChart.setOption(option, true);
                
                // Resize handler
                const handleResize = () => {
                    if (myChart) {
                        myChart.resize();
                        
                        const isMobile = window.innerWidth < 640;
                        option.title.textStyle.fontSize = isMobile ? 14 : 18;
                        option.xAxis.axisLabel.fontSize = isMobile ? 10 : 12;
                        option.yAxis.axisLabel.fontSize = isMobile ? 10 : 12;
                        
                        myChart.setOption(option);
                    }
                };

                window.removeEventListener('resize', handleResize);
                window.addEventListener('resize', handleResize);

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

            // Set up new interval for updates every 2 minutes
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
        document.addEventListener('DOMContentLoaded', () => {
            fetchCryptoData()
                .then(fetchHistoricalData)
                .then(startRealTimeUpdates)
                .then(() => {
                    const currentTime = new Date();
                    document.getElementById('lastUpdateTime').textContent = currentTime.toLocaleString("es-CO", { timeZone: "America/Bogota" });
                });
        });
    </script>
</body>
</html>