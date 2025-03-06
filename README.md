# CryptoInvestment

*CryptoInvestment* is a group of cryptocurrency investors who need a simple web application to track the performance of a customized selection of cryptocurrencies. The app should provide real-time updates on prices, percentage changes, and market volume. Currently, they rely on spreadsheets and scattered websites, which is inefficient and does not offer a consolidated view of price history. The investors consider data persistence over time to be crucial, with the ability to perform timeline verifications within a specific time range and access the data from different devices.

## Main Features
1. Real-Time Market Data
- > live updates on cryptocurrency prices, percentage changes, and market volume.
- > API integration for fetching real-time data from multiple sources.
2. Customizable Portfolio
- > Users can select and track specific cryptocurrencies.
- > Personalized dashboards to monitor investments
3. Historical Data & Trend Analysis
- > Persistent storage of historical price data.
- > TimeLine verification with flexible date range selection.
4. Cross-Device Accessibilty
- > Cloud-Based data storage for access from multiple devices.
- > Responsive design for web and mobile compatibility.
5. Data visualization
- > Interactive charts and graphs to analyze market trends.
- > Comparative insights for different cryptocurrencies.

## Technologies Used
### Backend
- ![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white) Laravel → Backend framework
- ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) PHP → Server-side scripting language 
### Frontend
- > *Blade* → Laravel templating engine
- ![Chart.js](https://img.shields.io/badge/chart.js-F5788D.svg?style=for-the-badge&logo=chart.js&logoColor=white) EChart.js → Data visualization
- ![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E) JavaScript → JavaScript for dynamic behavior
- ![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white) Tailwind CSS → Utility-first CSS framework
### DataBase
- ![SQLite](https://img.shields.io/badge/sqlite-%2307405e.svg?style=for-the-badge&logo=sqlite&logoColor=white) SQLite → Lightweight database
### APIs & Services
- > *CoinMarketCap* → Cryptocurrency data API
### Libraries & Tools 
- > *Axios* → HTTP client for API requests
- > *Intl API* → Internationalization and localization

## Dependencies Used
### Dependencies Composer

|Package|Version     |Description     |
|-------|----------------|----------------|
|  `php` |`^8.2`  |Minimum PHP version required|
|  `laravel/framework` |`^12.0`  |The core Laravel framework|
|  `laravel/tinker` |`^2.10.1`  |Laravel interactive console for testing PHP code|
### Dependencies Node
|Package|Version     |Description     |
|-------|----------------|----------------|
|  `@tailwindcss/vite` |`^4.0.0`  |Tailwind CSS Integration with Vite|
|  `axios` |`^1.7.4`  |HTTP client to make requests to APIs|
|  `concurrently` |`^9.0.1`  |Allows multiple commands to run in parallel (useful for running Laravel and Vite together)|
|  `laravel-vite-plugin` |`^4.0.0`  |Plugin to integrate Vite with Laravel|
|  `tailwindcss` |`^4.0.0`  |Utility-based CSS framework|
|  `vite` |`^6.0.11`  |Modern and fast frontend packager|

## Project Structure
```bash
/CryptoInvestment
│── app/  
│   ├── Console/          # Custom Artisan commands  
│   │   ├── Kernel.php    # Kernel for scheduling and Artisan commands  
│   ├── Http/  
│   │   ├── Controllers/  # Application controllers  
│   │       ├── CryptoController.php  # Controller for crypto-related logic  
│   ├── Jobs/             # Queueable jobs  
│   │   ├── FetchCryptoPricesJob.php  # Job for fetching cryptocurrency prices  
│   ├── Models/           # Database models  
│   │   ├── CryptoHistoricalData.php  # Model for storing historical crypto data  
│  
│── bootstrap/            # Framework bootstrapping configuration  
│── config/               # Configuration files  
│── database/  
│   ├── factories/        # Factories for testing data generation  
│   ├── migrations/       # Database migrations  
│   │   ├── crypto_historical_data_table.php  # Migration for crypto historical data table  
│   ├── seeders/          # Seeders to populate the database  
│  
│── public/               # Public entry point (index.php, assets, images, etc.)  
│── resources/  
│   ├── css/              # CSS files  
│   ├── js/               # JavaScript files  
│   ├── lang/             # Application translations  
│   ├── views/            # Blade templates (views)  
│   │   ├── crypto.blade.php  # Blade template for crypto-related pages  
│  
│── routes/   
│   ├── web.php           # Web routes  
│   ├── console.php       # Custom Artisan console commands  
│  
│── storage/  
│   ├── app/              # Application file storage  
│   ├── framework/        # Cache and sessions  
│   ├── logs/             # Log files  
│  
│── tests/                # Unit and feature tests  
│── vendor/               # Composer dependencies  
│── .env                  # Environment configuration  
│── .env.example          # Example environment configuration  
│── artisan               # Laravel CLI  
│── composer.json         # Composer configuration  
│── package.json          # NPM configuration  
│── server.php            # File to start the local server  
│── vite.config.js        # Vite configuration for Laravel  
```

## Authors
 
Sergio Chica - [GitHub](https://github.com/SergioChica)

## Contributing
Contributions are welcome! Please follow these steps to contribute:

Fork the repository.

- > Create a new branch (git checkout -b feature-branch).

- > Commit your changes (git commit -m "Add new feature").

- > Push to the branch (git push origin feature-branch).

Open a pull request.

## License

The Laravel framework is open-sourced software licensed under the <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>.