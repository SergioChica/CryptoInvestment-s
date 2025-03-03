<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfico de Criptomonedas</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
</head>
<body>
    <div id="cryptoChart" style="width: 100%; height: 400px;"></div>

    <script>
    async function fetchCryptoData() {
        const response = await axios.get('/crypto-prices');
        const data = response.data.data;

        const labels = data.map(crypto => crypto.name);
        const prices = data.map(crypto => crypto.quote.USD.price);
        const changes = data.map(crypto => crypto.quote.USD.percent_change_24h);
        const volumes = data.map(crypto => crypto.quote.USD.volume_24h);

        // Función para formatear números con comas y 2 decimales
        const formatPrice = (price) => new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(price);

        let chartDom = document.getElementById('cryptoChart');
        let myChart = echarts.init(chartDom);

        let option = {
            title: {
                text: 'Precios de Criptomonedas',
                left: 'center',
                textStyle: { color: '#ffffff', fontSize: 20 }
            },
            backgroundColor: '#1e1e1e',
            tooltip: {
                trigger: 'axis',
                backgroundColor: '#333',
                borderColor: '#777',
                borderWidth: 1,
                textStyle: { color: '#fff' },
                formatter: params => {
                    let item = params[0];
                    return `${item.name}: ${formatPrice(item.value)}
                            <br> Cambio 24h: ${changes[item.dataIndex].toFixed(2)}%
                            <br> Volumen: ${formatPrice(volumes[item.dataIndex])}`;
                }
            },
            xAxis: {
                type: 'category',
                data: labels,
                axisLabel: { color: '#ffffff' }
            },
            yAxis: {
                type: 'value',
                axisLabel: {
                    color: '#ffffff',
                    formatter: value => formatPrice(value)
                }
            },
            series: [{
                name: 'Precio',
                type: 'bar',
                data: prices,
                itemStyle: {
                    color: params => {
                        let colors = ['#FF5733', '#FFC300', '#36D1DC', '#5B86E5', '#8E44AD'];
                        return colors[params.dataIndex % colors.length];
                    },
                    barBorderRadius: [5, 5, 0, 0]
                },
                label: {
                    show: true,
                    position: 'top',
                    color: '#ffffff',
                    formatter: params => formatPrice(params.value)
                }
            }]
        };

        myChart.setOption(option);
        window.addEventListener('resize', () => myChart.resize());
    }

        fetchCryptoData();
        setInterval(fetchCryptoData, 60000);
    </script>
</body>
</html>
